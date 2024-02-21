<?php
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\StatusDetails;
use OncoAmazonPay\ApiService;
use OncoAmazonPay\LogService;
use OncoAmazonPay\Utils;

class AmazonPay_CheckoutConfirmationThemeContentView extends AmazonPay_CheckoutConfirmationThemeContentView_parent
{
    public function prepare_data()
    {
        parent::prepare_data();
        if (Utils::isAmazonPayCheckout()) {
            try {
                $apiService = new ApiService();
                $checkoutSession = $apiService->getClient()->getCheckoutSession($_SESSION['amazonCheckoutSessionId']);
                if (empty($checkoutSession->getCheckoutSessionId()) || empty($checkoutSession->getStatusDetails()) || $checkoutSession->getStatusDetails()->getState() !== StatusDetails::OPEN) {
                    throw new Exception('CheckoutSession not valid or in wrong state');
                }
            } catch (Exception $e) {
                $logger = new LogService();
                $logger->error('customer on confirmation page with wrong checkoutSessionId', [
                        'msg' => $e->getMessage(),
                    ]
                    + (isset($checkoutSession) && is_object($checkoutSession) ? ['checkoutSession' => $checkoutSession->toArray()] : [])
                );
                unset($_SESSION['amazonCheckoutSessionId']);
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            }

            $this->content_array['isAmazonPayCheckout'] = true;
            $this->content_array['amazonPayPaymentDescriptor'] = $this->getPaymentDetailsFromCheckoutSession($checkoutSession);
            $this->content_array['amazonPayLogoUrl'] = DIR_WS_CATALOG.'GXModules/OnlineshopConsulting/AmazonPay/Resources/image/logo_full.svg';
            $this->content_array['PAYMENT_EDIT'] = xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'resetAmazonPaySession=1', 'SSL');
        }
    }

    protected function getPaymentDetailsFromCheckoutSession(CheckoutSession $checkoutSession)
    {
        $details = '';
        if (!empty($checkoutSession->getPaymentPreferences())) {
            foreach ($checkoutSession->getPaymentPreferences() as $paymentPreference) {
                if (is_array($paymentPreference) && isset($paymentPreference['paymentDescriptor'])) {
                    $details = $paymentPreference['paymentDescriptor'];
                }
            }
        }
        return $details;
    }

}