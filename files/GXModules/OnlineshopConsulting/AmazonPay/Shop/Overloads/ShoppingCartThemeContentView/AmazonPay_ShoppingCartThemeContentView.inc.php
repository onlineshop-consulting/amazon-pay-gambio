<?php

use OncoAmazonPay\CheckoutService;


class AmazonPay_ShoppingCartThemeContentView extends AmazonPay_ShoppingCartThemeContentView_parent{
    public function prepare_data()
    {
        parent::prepare_data();
        if(class_exists('\OncoAmazonPay\CheckoutService')){
            CheckoutService::setCartChecked();
        }
    }
}