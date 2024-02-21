<?php

class AmazonPay_OrderExtenderComponent extends AmazonPay_OrderExtenderComponent_parent
{
    const ORDER_EXTENDER_POSITION = 'below_product_data';

    public function proceed()
    {
        parent::proceed();
        $orderId = (int)$this->v_data_array['GET']['oID'];
        $transactionService = new \OncoAmazonPay\TransactionService();

        $transactions = $transactionService->getTransactionsOfOrder($orderId);

        if (!empty($transactions)) {
            define('HAS_AMAZON_PAY_TRANSACTIONS', true);
            $heading = '<img src="' . DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Resources/image/logo_full.svg" alt="Amazon Pay" style="height: 18px; margin-top: 3px;" />';
            $body = '<div id="admin-amazon-pay-transactions" 
                          data-load-url="' . xtc_href_link('admin.php', 'do=AmazonPayTransactions/getTransactions&orderId=' . $orderId) . '"
                          data-refresh-url="' . xtc_href_link('admin.php', 'do=AmazonPayTransactions/refresh') . '"
                          data-action-url="' . xtc_href_link('admin.php', 'do=AmazonPayTransactions/doAction') . '"
                          data-order-id="' . $orderId . '"
                     ></div>';
            $this->addContentToCollection(self::ORDER_EXTENDER_POSITION, $body, $heading);
        }
    }
}