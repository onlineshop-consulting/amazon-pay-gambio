<?php

use OncoAmazonPay\ConfigurationService;
use OncoAmazonPay\Struct\Configuration;

class AmazonPay_BottomContentControl extends AmazonPay_BottomContentControl_parent
{
    public function proceed($p_close_db_connection = true)
    {
        parent::proceed($p_close_db_connection);

        $configurationService = new ConfigurationService();
        if (!($configurationService->isConfigurationComplete() && $configurationService->isPaymentMethodEnabled())) {
            return;
        }

        $configuration = $configurationService->getConfiguration();
        $outputArray = $configurationService->getPublicConfigurationArray();
        if (!empty($_SESSION['amazonCheckoutSessionId'])) {
            $outputArray['checkoutSessionId'] = $_SESSION['amazonCheckoutSessionId'];
        }
        $outputArray['loginPayload'] = stripcslashes(json_encode([
            'signInReturnUrl' => xtc_href_link('shop.php', 'do=AmazonPay/Login', 'SSL'),
            'storeId' => $configuration->getClientId(),
            'signInScopes' => ["name", "email", "postalCode", "shippingAddress"],
        ], JSON_UNESCAPED_UNICODE));
        $outputArray['loginSignature'] = $this->getCachedSignature($outputArray['loginPayload'], $configuration);
        if($configurationService->hasLocalButtons()){
            $outputArray['localButtonHtml'] = $this->getCachedLocalButtonHtml('de');
        }
        $this->v_output_buffer .= '<script>const AmazonPayConfiguration = ' . json_encode($outputArray) . ';</script>';
        $this->v_output_buffer .= '<script>if(window.onLoadAmazonPayConfiguration){window.onLoadAmazonPayConfiguration();}</script>';
        $this->addCss($configuration);

    }

    protected function addCss(Configuration $configuration){
        if($configuration->isHidden()){
            $this->v_output_buffer .= '
                <style>.amazon-pay-button, .amazon-login-button, #checkout_payment .list-group-item.amazon_pay{display:none;}</style>';
        }
    }

    protected function getCachedSignature($payload, Configuration $configuration){
        //TODO more Gambio style?
        $storageKey = 'apcv2_button_signature_' . md5(serialize([$configuration->toArray(), $payload]));
        $cacheFile = DIR_FS_CATALOG . 'cache/' . $storageKey;
        if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 28800) {
            return file_get_contents($cacheFile);
        }
        $apiService = new \OncoAmazonPay\ApiService();
        $signature = $apiService->getClient()->generateButtonSignature($payload);
        file_put_contents($cacheFile, $signature);
        return $signature;
    }

    protected function getCachedLocalButtonHtml($languageIsoCode){
        //TODO more Gambio style?
        $storageKey = 'apcv2_local_button.html';
        $cacheFile = DIR_FS_CATALOG . 'cache/' . $storageKey;
        if (file_exists($cacheFile) && filemtime($cacheFile) > time() - 28800) {
            return file_get_contents($cacheFile);
        }

        $html = file_get_contents(DIR_FS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Resources/html/local-button.html');
        $html = str_replace('%DIR_WS_CATALOG%', DIR_WS_CATALOG, $html);
        file_put_contents($cacheFile, $html);
        return $html;
    }
}