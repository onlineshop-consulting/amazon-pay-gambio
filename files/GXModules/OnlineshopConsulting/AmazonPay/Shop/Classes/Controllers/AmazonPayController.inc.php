<?php
declare(strict_types=1);

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use OncoAmazonPay\AccountService;
use OncoAmazonPay\ApiService;
use OncoAmazonPay\CheckoutService;
use OncoAmazonPay\ConfigurationService;
use OncoAmazonPay\LogService;
use OncoAmazonPay\Models\Transaction;
use OncoAmazonPay\TransactionService;

class AmazonPayController extends HttpViewController
{
    /**
     * @var LogService
     */
    private $logger;

    public function init()
    {
        $this->logger = new LogService();
    }

    public function actionCreateCheckoutSession()
    {
        $checkoutService = new CheckoutService();
        $result = [];
        try {
            $checkoutSession = $checkoutService->createCheckoutSession();
            $result['amazonCheckoutSessionId'] = $checkoutSession->getCheckoutSessionId();

        } catch (\Exception $e) {
            $this->logger->error('createCheckoutSession failed', ['msg' => $e->getMessage()]);
        }
        return MainFactory::create(JsonHttpControllerResponse::class, $result);
    }

    protected function getErrorRedirectResponse()
    {
        return MainFactory::create(RedirectHttpControllerResponse::class, xtc_href_link(FILENAME_SHOPPING_CART));
    }

    protected function getLoginErrorRedirectResponse()
    {
        return MainFactory::create(RedirectHttpControllerResponse::class, xtc_href_link(FILENAME_LOGIN));
    }

    public function actionLogin()
    {

        try {
            $buyerToken = $this->_getQueryParameter('buyerToken');
            $this->logger->debug('actionLogin', ['buyerToken' => $buyerToken]);
            if (empty($buyerToken)) {
                throw new \Exception('buyerToken is empty');
            }
            $apiService = new ApiService();
            $buyer = $apiService->getClient()->getBuyer($buyerToken);
        } catch (\Exception $e) {
            $this->logger->error('getBuyer failed', ['msg' => $e->getMessage()]);
            return $this->getLoginErrorRedirectResponse();
        }


        $accountService = new AccountService();
        $customerId = $accountService->createAccountSession($buyer);
        if (empty($customerId)) {
            return $this->getLoginErrorRedirectResponse();
        }
        return MainFactory::create(RedirectHttpControllerResponse::class, xtc_href_link(FILENAME_ACCOUNT));
    }

    public function actionReviewReturn()
    {
        $checkoutSessionId = $this->_getQueryParameter('amazonCheckoutSessionId');
        if (empty($checkoutSessionId)) {
            $this->logger->error('actionReviewReturn without checkoutSessionId', ['GET'=>$_GET]);
            return $this->getErrorRedirectResponse();
        }
        $this->logger->debug('actionReviewReturn', ['checkoutSessionId' => $checkoutSessionId]);
        $checkoutService = new CheckoutService();
        $_SESSION['amazonCheckoutSessionId'] = $checkoutSessionId;
        $checkoutSession = $checkoutService->getCheckoutSession($checkoutSessionId);

        if (empty($checkoutSession)) {
            $this->logger->error('actionReviewReturn without valid checkoutSession', ['GET'=>$_GET]);
            return $this->getErrorRedirectResponse();
        }
        $accountService = new AccountService();
        if (!$accountService->isLoggedIn()) {
            $customerId = $accountService->createGuestSession($checkoutSession);
            $accountService->setBillingAddressFromCheckoutSession($checkoutSession);
            if (empty($customerId)) {
                $this->logger->error('actionReviewReturn failed to create guest session', ['checkoutSession' => $checkoutSession]);
                return $this->getErrorRedirectResponse();
            }
        } else {
            $accountService->setShippingAddressFromCheckoutSession($checkoutSession);
            $accountService->setBillingAddressFromCheckoutSession($checkoutSession);
        }
        $_SESSION['payment'] = amazon_pay_ORIGIN::PAYMENT_METHOD_CODE;
        $this->logger->debug('actionReviewReturn success', ['checkoutSession' => $checkoutSession]);
        return MainFactory::create(RedirectHttpControllerResponse::class, xtc_href_link(FILENAME_CHECKOUT_SHIPPING));
    }

    public function actionCheckoutResultReturn()
    {
        $checkoutSessionId = $this->_getQueryParameter('amazonCheckoutSessionId');
        if (empty($checkoutSessionId)) {
            return $this->getErrorRedirectResponse();
        }
        $_SESSION['amazonCheckoutSessionId'] = $checkoutSessionId;
        return MainFactory::create(RedirectHttpControllerResponse::class, xtc_href_link(FILENAME_CHECKOUT_PROCESS));
    }

    public function actionIpn()
    {
        $tempSessionId = uniqid();
        try {
            $body = file_get_contents('php://input');
            $this->logger->debug('🌐 ipn - ' . $tempSessionId, [$this->_getPostDataCollection()->getArray()]);
            $message = Message::fromJsonString($body);
            $this->logger->debug('ipn data - ' . $tempSessionId, [$message->toArray()]);
            $validator = new MessageValidator();
            $validator->validate($message);
            $this->logger->debug('ipn validated - ' . $tempSessionId, [$message->toArray()]);
            if ($data = json_decode($body, true)) {
                if ($message = json_decode($data['Message'], true)) {
                    $this->processIpnMessage($message, $tempSessionId);
                } else {
                    throw new Exception('Could not decode message');
                }
            } else {
                throw new Exception('Could not decode body');
            }
        } catch (Exception $e) {
            $this->logger->error('IPN processing failed - ' . $tempSessionId, ['trace' => $e->getTrace(), 'msg' => $e->getMessage(), 'raw' => $body]);
            return MainFactory::create(JsonHttpControllerResponse::class, ['error' => true]);
        }
        return MainFactory::create(JsonHttpControllerResponse::class, ['success' => true]);
    }

    protected function processIpnMessage(array $message, string $tempSessionId)
    {
        $this->logger->debug('ipn message - ' . $tempSessionId, ['message' => $message]);
        $transactionService = new TransactionService();
        $apiService = new ApiService();
        switch ($message['ObjectType']) {
            case 'CHARGE':
                //catch race conditions
                $i = 1;
                while (true) {
                    if ($transaction = $transactionService->getTransaction($message['ObjectId'], Transaction::TRANSACTION_TYPE_CHARGE, true)) {
                        break;
                    } elseif ($i <= 5) {
                        $this->logger->debug('🛈 race condition catcher iteration ' . $i, ['chargeId' => $message['ObjectId']]);
                        usleep(pow(2, $i) * 100000);
                    } else {
                        break;
                    }
                    $i++;
                }
                if ($transaction) {
                    $charge = $apiService->getClient()->getCharge($message['ObjectId']);
                    $transactionService->updateCharge($charge);
                } else {
                    $this->logger->debug('🛈 Tried to update not existing charge via IPN', ['ipn' => $message]);
                }

                break;
            case 'REFUND':
                $transaction = $transactionService->getTransaction($message['ObjectId'], Transaction::TRANSACTION_TYPE_REFUND);
                $refund = $apiService->getClient()->getRefund($message['ObjectId']);
                if (!($orderId = $transaction->order_id)) {
                    $chargePermissionTransaction = $transactionService->getTransaction($message['ChargePermissionId'], Transaction::TRANSACTION_TYPE_CHARGE_PERMISSION, true);
                    if (!$chargePermissionTransaction || empty($chargePermissionTransaction->order_id)) {
                        throw new Exception('tried to process refund IPN from unregistered charge permission #' . $message['ChargePermissionId']);
                    }
                    $orderId = $chargePermissionTransaction->order_id;
                }
                if (!($parentCharge = $transaction->parent_id)) {
                    $parentCharge = $transactionService->getTransaction($refund->getChargeId(), Transaction::TRANSACTION_TYPE_CHARGE, true);
                    if (!$parentCharge) {
                        throw new Exception('tried to process refund IPN for unregistered charge #' . $refund->getChargeId());
                    }
                }
                $transactionService->updateRefund($refund, $orderId, $parentCharge->id);
                break;
            default:
                $this->logger->debug('🛈 unknown ipn', [$message]);
                break;
        }
    }

    public function actionPurePayment()
    {
        $this->logger->debug('::actionPurePayment()');
        $GLOBALS['order'] = new order();
        $checkoutService = new CheckoutService();
        $amount = $checkoutService->getAmountOfCurrentPendingOrder();

        $apiService = new ApiService();
        $configurationService = new ConfigurationService();
        $checkoutSessionObject = $checkoutService->createCheckoutSessionObjectForPurePayment($GLOBALS['order'], $amount);
        $createCheckoutSessionPayload = stripcslashes(json_encode($checkoutSessionObject->toArray(), JSON_UNESCAPED_UNICODE));
        $createCheckoutSessionSignature = $apiService->getClient()->generateButtonSignature($createCheckoutSessionPayload);


        $templateAmount = $checkoutSessionObject->getPaymentDetails()->getChargeAmount()->getAmount();
        $templatePayload = json_encode($createCheckoutSessionPayload);
        $templateSignature = json_encode($createCheckoutSessionSignature);
        $templatePublicKeyId = json_encode($configurationService->getConfiguration()->getPublicKeyId());
        $templateConfiguration = json_encode($configurationService->getPublicConfigurationArray());
        $javascriptPath = DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Shop/Themes/All/Javascript/Global/amazon_pay.js';
        $html = <<<EOT
<html>
    <head>
      <meta charset="UTF-8">
    </head>
    <body>
        <div id="amazon-pay-button-hidden" style="display:none;" data-amount="$templateAmount"></div>
        
        <script>const AmazonPayConfiguration = $templateConfiguration;</script>
        <script src="$javascriptPath"></script>
        <script type="text/javascript" charset="utf-8">
            var amazonCheckoutStarter = function(){
                if(typeof OncoAmazonPay !== 'undefined' && typeof amazon !== 'undefined'){
                    OncoAmazonPay.startPurePaymentCheckout(
                        {
                            payloadJSON: $templatePayload,
                            signature: $templateSignature,
                            publicKeyId: $templatePublicKeyId
                        }
                    );
                }else{
                    setTimeout(amazonCheckoutStarter, 50);
                }
            }
            amazonCheckoutStarter();
        </script>
    </body>
</html>
EOT;
        return MainFactory::create(HttpControllerResponse::class, $html);

    }
}