<?php

class AmazonPay_ConfigurationBoxContentView extends AmazonPay_ConfigurationBoxContentView_parent
{
    function prepare_data()
    {
        parent::prepare_data();
        require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay/amazon_pay.inc.php';

        if (isset($this->content_array['formAction']) && strpos($this->content_array['formAction'], 'amazon_pay') !== false) {
            if (!defined('MODULE_PAYMENT_AMAZON_PAY_STATUS')) {
                return;
            }
            $configurationService = new \OncoAmazonPay\ConfigurationService();
            $this->set_content_data(
                'additionalContent',
                '<div>v' . $configurationService->getPluginVersion() . '</div>' .
                '<a href="' . xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration') . '" class="button" style="margin:10px 0;">' . (defined('APC_CONFIGURATION_TITLE') ? APC_CONFIGURATION_TITLE : 'Konfigurieren') . '</a>'
            );
        }
    }
}