<?php
declare(strict_types=1);


use OncoAmazonPay\OrderService;
use OncoAmazonPay\TransactionService;

class AmazonPayCronjobTask extends AbstractCronjobTask
{
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime)
    {
        return function () {
            $logger = new \OncoAmazonPay\LogService();
            $logger->debug('start cronjob');

            $orderService = new OrderService();
            $orderService->doCaptureFromStatus();
            $orderService->doRefundFromStatus();

            $transactionService = new TransactionService();
            foreach ($transactionService->getOpenTransactions() as $transaction) {
                $transactionService->updateFromApi($transaction);
            }
            return true;
        };
    }

}