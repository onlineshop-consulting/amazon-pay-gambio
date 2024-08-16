<?php

use OncoAmazonPay\Utils;

class AmazonPay_CheckoutPaymentModulesThemeContentView extends AmazonPay_CheckoutPaymentModulesThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        if (Utils::isAmazonPayCheckout()) {
            $content = $this->get_content_array();
            foreach ($content['module_content'] as $key => $module) {
                if ($module['id'] === amazon_pay_ORIGIN::PAYMENT_METHOD_CODE) {
                    $content['module_content'][$key]['checked'] = 1;
                } else {
                    unset($content['module_content'][$key]);
                }
            }
            $this->set_content_data('module_content', $content['module_content']);
        }
    }
}