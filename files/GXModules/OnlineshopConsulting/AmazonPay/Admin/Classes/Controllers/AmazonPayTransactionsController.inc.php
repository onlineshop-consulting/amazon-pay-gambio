<?php

use OncoAmazonPay\LogService;

require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay/amazon_pay.inc.php';

class AmazonPayTransactionsController extends AdminHttpViewController
{
    public function actionDefault()
    {
        return $this->actionGetTransactions();
    }

    public function actionGetTransactions()
    {
        $transactionService = new \OncoAmazonPay\TransactionService();
        /** @var \OncoAmazonPay\Models\Transaction[] $transactions */
        $transactions = $transactionService->getTransactionsOfOrder((int)$this->_getQueryParameter('orderId'));

        $contentView = MainFactory::create('ContentView');
        $contentView->set_escape_html(true);
        $contentView->set_flat_assigns(true);
        $contentView->set_template_dir(DIR_FS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Admin/Html/');
        $contentView->set_content_template('amazon_pay_transactions.html');
        $chargePermissionTransaction = null;
        foreach ($transactions as $transaction) {
            if ($transaction->type === \OncoAmazonPay\Models\Transaction::TRANSACTION_TYPE_CHARGE_PERMISSION) {
                $chargePermissionTransaction = $transaction->toArray();
            }
        }
        $contentView->set_content_data('chargePermission', $chargePermissionTransaction);
        $contentView->set_content_data('transactions', array_map(function (\OncoAmazonPay\Models\Transaction $transaction) {
            return $transaction->toArray();
        }, $transactions));
        $html = $contentView->build_html();

        return MainFactory::create(
            'JsonHttpControllerResponse',
            ['success' => true, 'html' => $html]
        );
    }

    public function actionRefresh()
    {
        $transactionService = new \OncoAmazonPay\TransactionService();
        /** @var \OncoAmazonPay\Models\Transaction[] $transactions */
        $transactions = $transactionService->getTransactionsOfOrder((int)$this->_getPostData('orderId'));
        foreach ($transactions as $transaction) {
            $transactionService->updateFromApi($transaction);
        }
        return $this->actionGetTransactions();
    }

    public function actionDoAction()
    {
        $logger = new LogService();
        $transactionService = new \OncoAmazonPay\TransactionService();
        $action = $this->_getPostData('action');
        $error = null;
        switch ($action) {
            case 'capture':
                try {
                    $capture = $transactionService->capture($this->_getPostData('transaction'), (float)$this->_getPostData('amount'));
                } catch (Exception $e) {
                    $logger->error('error while capturing charge: ' . $e->getMessage());
                    $error = 'ERROR: ' . $e->getMessage();
                }
                break;
            case 'refund':
                try {
                    $transactionService->refund($this->_getPostData('transaction'), (float)$this->_getPostData('amount'));
                } catch (Exception $e) {
                    $logger->error('error for refund: ' . $e->getMessage());
                    $error = 'ERROR: ' . $e->getMessage();
                }
                break;
            case 'authorize':
                try {
                    $transactionService->authorize($this->_getPostData('transaction'), (float)$this->_getPostData('amount'));
                } catch (Exception $e) {
                    $logger->error('error for authorize: ' . $e->getMessage());
                    $error = 'ERROR: ' . $e->getMessage();
                }
                break;

        }
        return MainFactory::create(
            'JsonHttpControllerResponse',
            [
                'success' => true,
                'error' => $error,
            ]
        );
    }
}
