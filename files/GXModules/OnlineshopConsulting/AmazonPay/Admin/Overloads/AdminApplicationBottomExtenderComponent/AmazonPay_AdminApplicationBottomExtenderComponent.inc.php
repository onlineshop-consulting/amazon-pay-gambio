<?php

class AmazonPay_AdminApplicationBottomExtenderComponent extends AmazonPay_AdminApplicationBottomExtenderComponent_parent
{
    public function proceed()
    {
        parent::proceed();
        if (defined('HAS_AMAZON_PAY_TRANSACTIONS')) {
            echo '<script src="' . DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Admin/Assets/js/admin_transactions.js"></script>
                  <link rel="stylesheet" href="' . DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Admin/Assets/css/admin_transactions.css" />';
        }
    }
}