<?php

namespace OncoAmazonPay;

use Exception;
use MainFactory;
use order_ORIGIN;


class OrderService
{
    /**
     * @param $orderId
     * @return float
     */
    public function getOrderTotal($orderId): float
    {
        $order = $this->getOrder($orderId);
        return (float)$order->info['pp_total'];
    }

    /**
     * @param $orderId
     * @return mixed|order_ORIGIN
     */
    public function getOrder($orderId)
    {
        return MainFactory::create('order', $orderId);
    }

    public function doCaptureFromStatus()
    {
        $configuration = (new ConfigurationService())->getConfiguration();
        if ($configuration->getOrderStatusTriggerCapture() === -1) {
            return;
        }

        $q = "SELECT DISTINCT a.*
                FROM " . TABLE_ORDERS . " o
                JOIN amazon_pay_transactions a ON (o.orders_id = a.order_id AND a.type = 'Charge' AND a.status = 'Authorized')
              WHERE
                o.payment_method = 'amazon_pay'
                    AND
                o.orders_status = ?";

        $rs = DbAdapter::fetchAll($q, [$configuration->getOrderStatusTriggerCapture()]);
        $transactionService = new TransactionService();
        $logger = new LogService();
        foreach ($rs as $r) {
            try {
                $transactionService->capture($r['reference']);
            } catch (Exception $e) {
                $logger->error('Capture from status failed: ' . $e->getMessage());
            }
        }

    }

    public function doRefundFromStatus()
    {
        $configuration = (new ConfigurationService())->getConfiguration();
        if ($configuration->getOrderStatusTriggerRefund() === -1) {
            return;
        }

        $q = "SELECT DISTINCT a.*
                FROM " . TABLE_ORDERS . " o
                JOIN amazon_pay_transactions a ON (o.orders_id = a.order_id AND a.type = 'Charge' AND a.status = 'Captured')
              WHERE
                a.refunded_amount < a.captured_amount
                    AND
                o.payment_method = 'amazon_pay'
                    AND
                o.orders_status = ?";

        $rs = DbAdapter::fetchAll($q, [$configuration->getOrderStatusTriggerRefund()]);
        $transactionService = new TransactionService();
        $logger = new LogService();
        foreach ($rs as $r) {
            try {
                $transactionService->refund($r['reference']);
            } catch (Exception $e) {
                $logger->error('Refund from status failed: ' . $e->getMessage());
            }
        }

    }

    public function setOrderStatusDeclined($orderId)
    {
        self::setOrderStatus(
            $orderId,
            (new ConfigurationService())->getConfiguration()->getOrderStatusFailed(),
            'Amazon Pay - declined'
        );
    }

    public function setOrderStatus($orderId, $status, $comment = '')
    {
        //TODO refactor gambio style
        $orderId = (int)$orderId;
        $status = (int)$status;
        if ($status <= 0) {
            $q = "SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id = " . $orderId;
            $rs = xtc_db_query($q);
            if ($r = xtc_db_fetch_array($rs)) {
                $status = (int)$r["orders_status"];
            } else {
                return;
            }
        } else {
            $q = "SELECT * FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = " . $orderId . " AND orders_status_id = " . $status;
            $rs = xtc_db_query($q);
            if (xtc_db_num_rows($rs)) {
                return;
            }
        }
        $data = [
            'orders_id' => $orderId,
            'orders_status_id' => $status,
            'date_added' => 'now()',
            'customer_notified' => 0,
            'comments' => $comment,
        ];
        xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $data);
        $q = "UPDATE " . TABLE_ORDERS . " SET orders_status = " . $status . " WHERE orders_id = " . $orderId;
        xtc_db_query($q);
    }

    public function setOrderStatusAuthorized($orderId)
    {
        self::setOrderStatus(
            $orderId,
            (new ConfigurationService())->getConfiguration()->getOrderStatusAuthorized(),
            'Amazon Pay - authorize'
        );
    }


}