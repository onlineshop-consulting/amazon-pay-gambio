<?php
require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay/amazon_pay.inc.php';

class AmazonPay_ApplicationTopExtenderComponent extends AmazonPay_ApplicationTopExtenderComponent_parent
{
    public function proceed()
    {
        parent::proceed();

        $configurationService = new \OncoAmazonPay\ConfigurationService();
        if ($configurationService->isConfigurationComplete() && $configurationService->isPaymentMethodEnabled()) {
            define('SHOW_AMAZON_PAY_BUTTON', true);
            if($configurationService->getConfiguration()->getButtonColorLogin()){
                define('SHOW_AMAZON_LOGIN_BUTTON', true);
            }
        }

        if (isset($this->v_data_array['GET']['resetAmazonPaySession'])) {
            if (isset($_SESSION['amazonCheckoutSessionId'])) {
                unset($_SESSION['amazonCheckoutSessionId']);
            }
        }
    }
}