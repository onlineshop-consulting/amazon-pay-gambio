<?php

function smarty_function_checkIfAmazonPayButtonIsAllowed($params, &$smarty)
{
    if(!class_exists('\OncoAmazonPay\ConfigurationService')){
        return;
    }

    $result = false;
    $configurationService = new \OncoAmazonPay\ConfigurationService();

    if ($configurationService->isConfigurationComplete() && $configurationService->isPaymentMethodEnabled()) {
        (new \OncoAmazonPay\CheckoutService())->doOptionalCartCheck();
        try{
            if (isset($_SESSION['allow_checkout']) && $_SESSION['allow_checkout'] === 'false') {
                throw new Exception('Checkout is not allowed');
            }
            $disallowedPaymentMethods = explode(',', $_SESSION['customers_status']['customers_status_payment_unallowed'] ?? '');
            if (is_array($disallowedPaymentMethods) && in_array('amazon_pay', $disallowedPaymentMethods, true)){
                throw new Exception('Payment method not allowed');
            }
            $result = true;
        } catch (Exception $e) {
            //silent
        }
    }

    $smarty->assign('isAmazonPayButtonAllowed', $result);
}