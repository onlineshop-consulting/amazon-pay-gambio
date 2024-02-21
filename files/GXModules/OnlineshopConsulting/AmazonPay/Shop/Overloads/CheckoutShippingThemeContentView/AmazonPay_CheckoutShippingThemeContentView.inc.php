<?php

use OncoAmazonPay\Utils;

class AmazonPay_CheckoutShippingThemeContentView extends AmazonPay_CheckoutShippingThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        if (Utils::isAmazonPayCheckout()) {
            $this->set_content_data('isAmazonPayCheckout', true);
        }
    }
}