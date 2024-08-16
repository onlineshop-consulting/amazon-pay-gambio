<?php
declare(strict_types=1);

namespace OncoAmazonPay;


use AmazonPayApiSdkExtension\Struct\AddressDetails;
use AmazonPayApiSdkExtension\Struct\AddressRestrictions;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\DeliverySpecifications;
use AmazonPayApiSdkExtension\Struct\MerchantMetadata;
use AmazonPayApiSdkExtension\Struct\PaymentDetails;
use AmazonPayApiSdkExtension\Struct\Price;
use AmazonPayApiSdkExtension\Struct\WebCheckoutDetails;
use Exception;
use MainFactory;
use order_ORIGIN;

class CheckoutService
{

    public static $isCartChecked = false;

    public static function setCartChecked()
    {
        self::$isCartChecked = true;
    }

    public static function isCartChecked(): bool
    {
        return self::$isCartChecked;
    }

    /**
     * @var ConfigurationService
     */
    private $configurationService;
    /**
     * @var LogService
     */
    private $logService;
    /**
     * @var ApiService
     */
    private $apiService;

    public function __construct()
    {
        $this->apiService = new ApiService();
        $this->configurationService = new ConfigurationService();
        $this->logService = new LogService();
    }

    /**
     * @return CheckoutSession|null
     * @throws Exception
     */
    public function createCheckoutSession($forceSandbox = false)
    {
        $checkoutSession = $this->createCheckoutSessionBaseObject();
        return $this->apiService->getClient($forceSandbox)->createCheckoutSession($checkoutSession);

    }

    public function createCheckoutSessionBaseObject(): CheckoutSession
    {
        $storeName = STORE_NAME;
        $encoding = mb_detect_encoding($storeName, ['UTF-8', 'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-15']);
        if ($encoding !== 'UTF-8') {
            $storeName = mb_convert_encoding($storeName, 'UTF-8', $encoding);
        }
        $storeName = (mb_strlen($storeName) <= 50) ? $storeName : (mb_substr($storeName, 0, 47) . '...');

        $merchantData = new MerchantMetadata();
        $merchantData->setMerchantStoreName($storeName);
        $merchantData->setCustomInformation($this->configurationService->getCustomInformationString());

        $webCheckoutDetails = new WebCheckoutDetails();
        $webCheckoutDetails->setCheckoutReviewReturnUrl($this->configurationService->getReviewReturnUrl());

        $addressRestrictions = new AddressRestrictions();
        $addressRestrictions->setType('Allowed')
            ->setRestrictions($this->configurationService->getAllowedCountries());
        $deliverySpecifications = new DeliverySpecifications();
        $deliverySpecifications->setAddressRestrictions($addressRestrictions);

        $checkoutSession = new CheckoutSession();
        $checkoutSession->setMerchantMetadata($merchantData)
            ->setWebCheckoutDetails($webCheckoutDetails)
            ->setStoreId($this->configurationService->getConfiguration()->getClientId())
            ->setPlatformId($this->configurationService->getPlatformId())
            ->setDeliverySpecifications($deliverySpecifications);
        return $checkoutSession;
    }

    /**
     * @param order_ORIGIN $order
     * @return CheckoutSession
     */
    public function createCheckoutSessionObjectForPurePayment($order, $amount)
    {
        $checkoutSession = $this->createCheckoutSessionBaseObject();
        $checkoutSession->getWebCheckoutDetails()
            ->setCheckoutResultReturnUrl($this->configurationService->getCheckoutResultReturnUrl())
            ->setCheckoutReviewReturnUrl(null)
            ->setCheckoutCancelUrl($this->configurationService->getCancelUrl())
            ->setCheckoutMode(WebCheckoutDetails::CHECKOUT_MODE_PROCESS_ORDER);

        $paymentDetails = (new PaymentDetails())
            ->setPaymentIntent('Authorize')
            ->setCanHandlePendingAuthorization($this->configurationService->getConfiguration()->getCanHandlePendingAuthorization())
            ->setChargeAmount(new Price(['amount' => $amount, 'currencyCode' => $order->info['currency']]))
            ->setPresentmentCurrency($order->info['currency']);

        $checkoutSession->setPaymentDetails($paymentDetails);
        $checkoutSession->setAddressDetails($this->getAddressDetails());
        return $checkoutSession;

    }

    public function getAmountOfCurrentPendingOrder()
    {
        $this->logService->debug('::getAmountOfCurrentPendingOrder start');
        $amount = null;
        if (empty($GLOBALS['order'])) {
            $GLOBALS['order'] = new \order();
        }
        if (empty($GLOBALS['order_total_modules'])) {
            $GLOBALS['order_total_modules'] = new \order_total();
            $orderTotalResult = $GLOBALS['order_total_modules']->process();
            $this->logService->debug('::getAmountOfCurrentOrder - new order total modules', ['result' => $orderTotalResult]);
        } else {
            if (!empty($GLOBALS['ot_total']) && is_object($GLOBALS['ot_total']) && empty($GLOBALS['ot_total']->output)) {
                $orderTotalResult = $GLOBALS['order_total_modules']->process();
                $this->logService->debug('::getAmountOfCurrentOrder - process existing order total modules', ['result' => $orderTotalResult]);
            } else {
                $this->logService->debug('::getAmountOfCurrentOrder - order total modules had been processed', ['result' => $GLOBALS['ot_total']]);
            }
        }
        if (!empty($orderTotalResult) && is_array($orderTotalResult)) {
            foreach ($orderTotalResult as $orderTotal) {
                if ($orderTotal['code'] === 'ot_total') {
                    $amount = $orderTotal['value'];
                }
            }

        }
        if (empty($amount)) {
            $amount = $GLOBALS['order']->info['total'];
        }
        return $amount;
    }

    /**
     * @param order_ORIGIN $order
     * @return mixed
     */
    protected function getAddressDetails()
    {
        $shippingAddress = DbAdapter::fetch("
            SELECT 
                ab.entry_firstname, 
                ab.entry_lastname, 
                ab.entry_company, 
                ab.entry_street_address, 
                ab.entry_house_number, 
                ab.entry_additional_info, 
                ab.entry_suburb, 
                ab.entry_postcode, 
                ab.entry_city,
                c.countries_iso_code_2, 
                ab.entry_state 
            FROM 
                " . TABLE_ADDRESS_BOOK . " ab 
                LEFT JOIN " . TABLE_COUNTRIES . " c ON (ab.entry_country_id = c.countries_id) 
            WHERE 
                ab.customers_id = ? 
                    AND 
                ab.address_book_id = ?
            ", [$_SESSION['customer_id'], $_SESSION['sendto']]);

        return (new AddressDetails())
            ->setName($shippingAddress['entry_firstname'] . ' ' . $shippingAddress['entry_lastname'])
            ->setAddressLine1($shippingAddress['entry_company'] ?: $shippingAddress['entry_street_address'])
            ->setAddressLine2($shippingAddress['entry_company'] ? $shippingAddress['entry_street_address'] : $shippingAddress['entry_additional_info'])
            ->setAddressLine3($shippingAddress['entry_company'] ? $shippingAddress['entry_additional_info'] : $shippingAddress['entry_suburb'])
            ->setPostalCode($shippingAddress['entry_postcode'])
            ->setCity($shippingAddress['entry_city'])
            ->setCountryCode($shippingAddress['countries_iso_code_2'])
            ->setPhoneNumber('00000');
    }

    /**
     * @param string $checkoutSessionId
     * @return CheckoutSession|null
     */
    public function getCheckoutSession(string $checkoutSessionId)
    {
        try {
            return $this->apiService->getClient()->getCheckoutSession($checkoutSessionId);
        } catch (Exception $e) {
            $this->logService->error('getCheckoutSession failed', ['msg' => $e->getMessage()]);
        }
        return null;
    }

    /**
     * @param string $checkoutSessionId
     * @param CheckoutSession $checkoutSession
     * @return CheckoutSession|null
     */
    public function updateCheckoutSession(string $checkoutSessionId, CheckoutSession $checkoutSession)
    {
        try {
            return $this->apiService->getClient()->updateCheckoutSession($checkoutSessionId, $checkoutSession);
        } catch (Exception $e) {
            $this->logService->error('updateCheckoutSession failed', ['msg' => $e->getMessage()]);
        }
        return null;
    }

    public function setOrderIdToChargePermission($chargePermissionId, $orderId)
    {

        $this->apiService->getClient()->updateChargePermission(
            $chargePermissionId,
            ['merchantMetadata' => ['merchantReferenceId' => $orderId]]
        );
    }

    public function doOptionalCartCheck()
    {
        global $xtPrice;
        if (self::isCartChecked()){
            return;
        }
        try {
            $_SESSION['allow_checkout'] = 'true';
            $minAmount = $_SESSION['customers_status']['customers_status_min_order'];
            $maxAmount = $_SESSION['customers_status']['customers_status_max_order'];
            if($minAmount > 0 || $maxAmount > 0){
                $totalForMinMaxOrder = $_SESSION['cart']->getTotalForMinMaxOrder();
                $minAmount = $xtPrice->xtcCalculateCurr($minAmount);
                $maxAmount = $xtPrice->xtcCalculateCurr($maxAmount);
                if($totalForMinMaxOrder < $minAmount){
                    $_SESSION['allow_checkout'] = 'false';
                }
                if($maxAmount > 0 && $totalForMinMaxOrder > $maxAmount){
                    $_SESSION['allow_checkout'] = 'false';
                }
            }
        }catch(Exception $e){
            $this->logService->error('cart check failed: '.$e->getMessage(), ['trace'=>$e->getTrace()]);
        }
        self::setCartChecked();
    }
}
