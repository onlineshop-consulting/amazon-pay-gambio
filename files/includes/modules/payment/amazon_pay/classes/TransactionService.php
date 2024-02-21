<?php

namespace OncoAmazonPay;

use AmazonPayApiSdkExtension\Exceptions\AmazonPayException;
use AmazonPayApiSdkExtension\Struct\CaptureAmount;
use AmazonPayApiSdkExtension\Struct\Charge;
use AmazonPayApiSdkExtension\Struct\ChargeAmount;
use AmazonPayApiSdkExtension\Struct\ChargePermission;
use AmazonPayApiSdkExtension\Struct\Refund;
use AmazonPayApiSdkExtension\Struct\RefundAmount;
use AmazonPayApiSdkExtension\Struct\StatusDetails;
use AmazonPayApiSdkExtension\Struct\StructBase;
use Exception;
use Generator;
use OncoAmazonPay\Models\Transaction;
use OncoAmazonPay\Struct\Configuration;

class TransactionService
{

    /**
     * @var LogService
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new LogService();
    }

    public function getTransactionsOfOrder(int $orderId): array
    {
        return $this->searchTransactions("order_id =  ?", [$orderId]);
    }

    public function searchTransactions(string $whereString, array $whereParameters = []): array
    {
        $rs = DbAdapter::fetchAll("SELECT * FROM " . Transaction::TABLE_NAME . " WHERE " . $whereString, $whereParameters);
        $return = [];
        foreach ($rs as $r) {
            $return[] = new Transaction($r);
        }
        return $return;
    }

    public function updateCharge(Charge $charge, $orderId = null, $parentId = null)
    {
        $this->logger->debug('update charge', [$charge->toArray()]);
        $chargeTransaction = $this->persistTransaction($charge, $orderId, $parentId);

        if ($chargeTransaction->parent_id && ($chargePermission = $this->getTransactionById($chargeTransaction->parent_id))) {
            $this->updateFromApi($chargePermission);
        }

        $orderId = $chargeTransaction->order_id;

        if (!$orderId) {
            return;
        }
        $orderService = new OrderService();
        $configurationService = new ConfigurationService();
        $configuration = $configurationService->getConfiguration();
        if ($chargeTransaction->status === StatusDetails::CAPTURED) {
            if ((int)$chargeTransaction->admin_informed === 0) {
                $this->logger->debug('update order status after capture', ['charge' => $chargeTransaction->toArray()]);
                $orderAmount = $orderService->getOrderTotal($orderId);
                $isComplete = $charge->getCaptureAmount()->getAmount() >= $orderAmount;
                $orderService->setOrderStatus(
                    $orderId,
                    $isComplete ? $configuration->getOrderStatusCapturedCompletely() : $configuration->getOrderStatusCapturedPartly(),
                    'Amazon Pay'
                );

                $chargeTransaction->admin_informed = 1;
                $this->writeTransaction($chargeTransaction);
            }
        } elseif ($chargeTransaction->status === StatusDetails::AUTHORIZED) {
            $this->logger->debug('update order status after authorize');
            $orderService->setOrderStatusAuthorized($orderId);
        } elseif ($chargeTransaction->status === StatusDetails::DECLINED) {
            $this->logger->debug('update order status after decline');
            $orderService->setOrderStatusDeclined($orderId);
        }

        if ($chargeTransaction->status === StatusDetails::AUTHORIZED && $configuration->getCaptureMode() === Configuration::CAPTURE_MODE_IMMEDIATELY) {
            try {
                $this->capture($charge->getChargeId());
            } catch (Exception $e) {
                $this->logger->error('error while capturing charge: ' . $e->getMessage());
            }
        }


    }

    public function persistTransaction(StructBase $transactionStruct, $orderId = null, $parentId = null)
    {
        if ($transactionStruct instanceof ChargePermission) {
            $transaction = $this->getChargePermissionTransaction($transactionStruct);
        } elseif ($transactionStruct instanceof Charge) {
            $transaction = $this->getChargeTransaction($transactionStruct);
        } elseif ($transactionStruct instanceof Refund) {
            $transaction = $this->getRefundTransaction($transactionStruct);
        }

        if (empty($transaction)) {
            throw new Exception('Invalid Transaction Type ' . get_class($transactionStruct));
        }

        if ($orderId) {
            $transaction->order_id = $orderId;
        }

        if ($parentId) {
            $transaction->parent_id = $parentId;
        }
        $transactionId = $this->writeTransaction($transaction);
        return $this->getTransactionById($transactionId);
    }

    protected function getChargePermissionTransaction(ChargePermission $chargePermission)
    {
        $chargePermissionTransaction = $this->getTransaction($chargePermission->getChargePermissionId(), Transaction::TRANSACTION_TYPE_CHARGE_PERMISSION);
        $chargePermissionTransaction->setFromArray([
            'currency' => $chargePermission->getLimits()->getAmountLimit()->getCurrencyCode(),
            'amount' => $chargePermission->getLimits()->getAmountLimit()->getAmount(),
            'captured_amount' => $chargePermission->getLimits()->getAmountLimit()->getAmount() - $chargePermission->getLimits()->getAmountBalance()->getAmount(),
            'status' => $chargePermission->getStatusDetails()->getState(),
            'time' => Utils::formatDate($chargePermission->getCreationTimestamp()),
            'expiration' => Utils::formatDate($chargePermission->getExpirationTimestamp()),
        ]);
        return $chargePermissionTransaction;
    }

    /**
     * @param string $reference
     * @param string $type
     * @param bool $onlyExisting
     * @return Transaction|null
     */
    public function getTransaction(string $reference, string $type, bool $onlyExisting = false)
    {
        /** @var Transaction $transaction */
        if ($transaction = $this->getTransactionFromDatabase($reference, $type)) {
            return $transaction;
        } elseif (!$onlyExisting) {
            $configuration = (new ConfigurationService())->getConfiguration();
            return (new Transaction(
                [
                    'reference' => $reference,
                    'type' => $type,
                    'merchant_id' => $configuration->getMerchantId(),
                    'mode' => $configuration->isSandbox() ? 'sandbox' : 'live',
                ]
            ));
        } else {
            return null;
        }
    }

    public function getTransactionFromDatabase($reference, $type)
    {
        $q = "SELECT * FROM " . Transaction::TABLE_NAME . " WHERE reference = ? AND type = ?";
        if ($r = DbAdapter::fetch($q, [$reference, $type])) {
            return new Transaction($r);
        }
        return null;
    }

    /**
     * @param Charge $charge
     * @return Transaction|null
     */
    protected function getChargeTransaction(Charge $charge)
    {
        $chargeTransaction = $this->getTransaction($charge->getChargeId(), Transaction::TRANSACTION_TYPE_CHARGE);
        $chargeTransaction->setFromArray(
            [
                'currency' => $charge->getChargeAmount()->getCurrencyCode(),
                'amount' => $charge->getChargeAmount()->getAmount(),
                'status' => $charge->getStatusDetails()->getState(),
                'time' => Utils::formatDate($charge->getCreationTimestamp()),
                'expiration' => Utils::formatDate($charge->getExpirationTimestamp()),
            ]
        );

        if ($charge->getCaptureAmount()) {
            $chargeTransaction->captured_amount = (float)$charge->getCaptureAmount()->getAmount();
        }
        if ($charge->getRefundedAmount()) {
            $chargeTransaction->refunded_amount = (float)$charge->getRefundedAmount()->getAmount();
        }

        return $chargeTransaction;
    }

    protected function getRefundTransaction(Refund $refund)
    {
        $refundTransaction = $this->getTransaction($refund->getRefundId(), Transaction::TRANSACTION_TYPE_REFUND);
        $refundTransaction->setFromArray([
            'currency' => $refund->getRefundAmount()->getCurrencyCode(),
            'amount' => $refund->getRefundAmount()->getAmount(),
            'status' => $refund->getStatusDetails()->getState(),
            'time' => Utils::formatDate($refund->getCreationTimestamp()),
        ]);
        var_dump($refundTransaction);


        return $refundTransaction;
    }

    public function writeTransaction(Transaction $transaction): int
    {
        $transactionArray = $transaction->toArray();
        if (!empty($transactionArray['id'])) {
            DbAdapter::update(Transaction::TABLE_NAME, $transactionArray, ['id' => $transactionArray['id']]);
            return (int)$transactionArray['id'];
        } else {
            DbAdapter::insert(Transaction::TABLE_NAME, $transactionArray);
            return (int)DbAdapter::lastInsertId();
        }
    }

    public function getTransactionById($id)
    {
        $q = "SELECT * FROM " . Transaction::TABLE_NAME . " WHERE id = ?";
        if ($r = DbAdapter::fetch($q, [$id])) {
            return new Transaction($r);
        }
        return null;
    }

    /**
     * This method gets the current transaction details from the API and updates the local DB row
     *
     * @param Transaction $transaction
     * @throws AmazonPayException
     * @throws Exception
     */
    public function updateFromApi(Transaction $transaction)
    {
        $this->logger->debug('update transaction from API ' . $transaction->reference);
        $configurationService = new ConfigurationService();
        $apiService = new ApiService();
        $configuration = $configurationService->getConfiguration();

        $forceSandboxValue = null;
        $isTransactionSandbox = ($transaction->mode === 'sandbox');
        if ($transaction->mode && ($isTransactionSandbox !== $configuration->isSandbox())) {
            $forceSandboxValue = $isTransactionSandbox;
        }
        if ($transaction->type === Transaction::TRANSACTION_TYPE_REFUND) {
            $refund = $apiService->getClient($forceSandboxValue)->getRefund($transaction->reference);
            $this->updateRefund($refund);
        } elseif ($transaction->type === Transaction::TRANSACTION_TYPE_CHARGE) {
            $charge = $apiService->getClient($forceSandboxValue)->getCharge($transaction->reference);
            $this->updateCharge($charge);
        } elseif ($transaction->type === Transaction::TRANSACTION_TYPE_CHARGE_PERMISSION) {
            $chargePermission = $apiService->getClient($forceSandboxValue)->getChargePermission($transaction->reference);
            $this->persistTransaction($chargePermission);
        }
    }

    public function updateRefund(Refund $refund, $orderId = null, $parentId = null)
    {
        $this->logger->debug('update refund', ['refund' => $refund->toArray()]);
        $refundTransaction = $this->persistTransaction($refund, $orderId, $parentId);
        $orderId = $refundTransaction->order_id;

        if ($refundTransaction->status === StatusDetails::REFUNDED && $orderId) {
            if ((int)$refundTransaction->admin_informed === 0) {
                $this->logger->debug('update order status after refund', ['refund' => $refundTransaction->toArray()]);
                $orderService = new OrderService();
                $configurationService = new ConfigurationService();
                $configuration = $configurationService->getConfiguration();
                $orderAmount = $orderService->getOrderTotal($orderId);
                $isComplete = $refund->getRefundAmount()->getAmount() >= $orderAmount;

                $orderService->setOrderStatus(
                    $orderId,
                    $isComplete ? $configuration->getOrderStatusRefundedCompletely() : $configuration->getOrderStatusRefundedPartly(),
                    'Amazon Pay'
                );

                $refundTransaction->admin_informed = 1;
                $this->writeTransaction($refundTransaction);
            }
        }
    }

    public function capture($chargeId, $amount = null): Charge
    {
        $apiClient = (new ApiService())->getClient();
        $originalCharge = $apiClient->getCharge($chargeId);
        if ($originalCharge->getStatusDetails()->getState() === StatusDetails::AUTHORIZED) {
            $captureCharge = new Charge();
            $captureAmount = new CaptureAmount($originalCharge->getChargeAmount()->toArray());
            if ($amount !== null) {
                $captureAmount->setAmount($amount);
            }
            $captureCharge->setCaptureAmount($captureAmount);
            $captureCharge = $apiClient->captureCharge($originalCharge->getChargeId(), $captureCharge);
            $this->updateCharge($captureCharge);
            return $captureCharge;
        } else {
            $this->updateCharge($originalCharge);
            throw new Exception('Charge is not in authorized state');
        }
    }

    public function refund($chargeId, $amount = null)
    {
        $apiClient = (new ApiService())->getClient();
        $this->logger->debug('refund transaction ' . $chargeId);
        try {
            $originalCharge = $apiClient->getCharge($chargeId);
            $chargeTransaction = $this->persistTransaction($originalCharge);
            if ($originalCharge->getStatusDetails()->getState() === StatusDetails::CAPTURED) {
                $refund = new Refund();
                $refundAmount = new RefundAmount($originalCharge->getCaptureAmount()->toArray());
                if ($amount !== null) {
                    $refundAmount->setAmount($amount);
                } elseif ($originalCharge->getRefundedAmount()->getAmount()) {
                    $refundAmount->setAmount($refundAmount->getAmount() - $originalCharge->getRefundedAmount()->getAmount());
                }
                if ($refundAmount->getAmount() > 0) {
                    $refund->setRefundAmount($refundAmount);
                    $refund->setChargeId($chargeId);
                    $refund = $apiClient->createRefund($refund);
                    $this->updateRefund($refund, $chargeTransaction->order_id, $chargeId);
                    return $refund;
                } else {
                    $this->logger->error('Tried refund with empty amount for charge ' . $chargeId);
                }

            } else {
                $this->logger->error('Tried refund for uncaptured charge: ' . $chargeId, ['charge' => $originalCharge->toArray()]);
            }
        } catch (Exception $e) {
            $this->logger->error('Refund failed: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            throw $e;
        }
        return null;
    }

    public function authorize($chargePermissionId, $amount = null): Charge
    {
        $apiClient = (new ApiService())->getClient();
        try {
            $chargePermission = $apiClient->getChargePermission($chargePermissionId);
            $chargePermissionTransaction = $this->persistTransaction($chargePermission);

            $charge = (new Charge())
                ->setChargePermissionId($chargePermissionId)
                ->setChargeAmount(
                    (new ChargeAmount())
                        ->setAmount($amount ?: $chargePermission->getLimits()->getAmountBalance()->getAmount())
                        ->setCurrencyCode($chargePermission->getPresentmentCurrency())
                );
            $charge = $apiClient->createCharge($charge);
            $order = $chargePermissionTransaction->order_id;
            $this->persistTransaction($charge, $order, $chargePermissionTransaction->id);

        } catch (Exception $e) {
            $this->logger->error('Create charge failed: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            throw new Exception('Create charge failed: ' . $e->getMessage());
        }
        return $charge;
    }

    public function getOpenTransactions(): Generator
    {
        $statusCollection = [
            StatusDetails::REFUND_INITIATED,
            StatusDetails::OPEN,
            StatusDetails::AUTHORIZATION_INITIATED,
            StatusDetails::NON_CHARGEABLE,
            StatusDetails::CHARGEABLE,
        ];
        if ((new ConfigurationService())->getConfiguration()->getCaptureMode() === Configuration::CAPTURE_MODE_IMMEDIATELY) {
            $statusCollection[] = StatusDetails::AUTHORIZED;
        }

        $elements = str_repeat('?,', count($statusCollection) - 1) . '?';
        $q = "SELECT * FROM " . Transaction::TABLE_NAME . " WHERE status IN ($elements)";
        foreach (DbAdapter::fetchAll($q, $statusCollection) as $row) {
            yield new Transaction($row);
        }
    }


}