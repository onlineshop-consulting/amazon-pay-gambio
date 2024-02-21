<?php

class AmazonPay_ProductInfoThemeContentView extends AmazonPay_ProductInfoThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        $configurationService = new \OncoAmazonPay\ConfigurationService();
        if ($configurationService->getConfiguration()->isShowButtonOnProductPage()) {
            $this->set_content_data('SHOW_AMAZON_PAY_BUTTON', 1);
        }
    }
}