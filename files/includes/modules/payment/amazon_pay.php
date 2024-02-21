<?php
require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay/amazon_pay.inc.php';

use AmazonPayApiSdkExtension\Struct\ChargePermission;
use AmazonPayApiSdkExtension\Struct\CheckoutSession;
use AmazonPayApiSdkExtension\Struct\MerchantMetadata;
use AmazonPayApiSdkExtension\Struct\PaymentDetails;
use AmazonPayApiSdkExtension\Struct\Price;
use AmazonPayApiSdkExtension\Struct\StatusDetails;
use AmazonPayApiSdkExtension\Struct\WebCheckoutDetails;
use OncoAmazonPay\ApiService;
use OncoAmazonPay\CheckoutService;
use OncoAmazonPay\ConfigurationService;
use OncoAmazonPay\DbAdapter;
use OncoAmazonPay\InstallationService;
use OncoAmazonPay\LogService;
use OncoAmazonPay\OrderService;
use OncoAmazonPay\TransactionService;

if (!class_exists('amazon_pay_ORIGIN')) {
    class amazon_pay_ORIGIN
    {
        const VERSION = '1.1.0';
        const PLATFORM_ID = 'A2VVGKBLLUJHH1';

        const PAYMENT_METHOD_CODE = 'amazon_pay';
        /**
         * @var string
         */
        public $code;
        /**
         * @var string
         */
        public $title;
        /**
         * @var string
         */
        public $description;
        /**
         * @var int
         */
        public $sort_order;
        /**
         * @var bool
         */
        public $enabled;
        /**
         * @var string
         */
        public $info;

        public function __construct()
        {
            $this->code = self::PAYMENT_METHOD_CODE;
            $this->title = defined('MODULE_PAYMENT_AMAZON_PAY_TEXT_TITLE') ? MODULE_PAYMENT_AMAZON_PAY_TEXT_TITLE : 'Amazon Pay';
            $this->description = '';//defined('MODULE_PAYMENT_AMAZON_PAY_TEXT_DESCRIPTION') ? MODULE_PAYMENT_AMAZON_PAY_TEXT_DESCRIPTION : '';
            $this->sort_order = defined('MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER') ? MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER : 0;
            $this->enabled = defined('MODULE_PAYMENT_AMAZON_PAY_STATUS') && strtolower(MODULE_PAYMENT_AMAZON_PAY_STATUS) === 'true';
            $this->info = defined('MODULE_PAYMENT_AMAZON_PAY_TEXT_INFO') ? MODULE_PAYMENT_AMAZON_PAY_TEXT_INFO : '';
            $this->update_status();
        }

        public function update_status()
        {
            if ($this->enabled === false) {
                return;
            }
            $configurationService = new ConfigurationService();
            if (!$configurationService->isConfigurationComplete()) {
                $this->enabled = false;
            }
        }

        public function selection(): array
        {
            return [
                'id' => $this->code,
                'module' => $this->title,
                'description' => $this->info . '<div style="display:none;"><div id="amazon-pay-button-manual"></div></div>',
            ];
        }

        public function pre_confirmation_check()
        {
            return false;
        }

        public function confirmation()
        {
            return [];
        }

        public function process_button(): string
        {
            return '';
        }

        public function before_process()
        {
            $logger = new LogService();
            $logger->debug('::before_process start');
            $checkoutSession = $this->check_for_open_session();
            if (empty($checkoutSession->getWebCheckoutDetails()) || empty($checkoutSession->getWebCheckoutDetails()->getCheckoutResultReturnUrl())) {
                $configurationService = new ConfigurationService();
                $checkoutService = new CheckoutService();
                $amount = $checkoutService->getAmountOfCurrentPendingOrder();


                $checkoutSessionUpdate = new CheckoutSession();
                $webCheckoutDetails = new WebCheckoutDetails();
                $webCheckoutDetails->setCheckoutResultReturnUrl($configurationService->getCheckoutResultReturnUrl());

                $paymentDetails = new PaymentDetails();
                $paymentDetails
                    ->setPaymentIntent('Authorize')
                    ->setCanHandlePendingAuthorization($configurationService->getConfiguration()->getCanHandlePendingAuthorization())
                    ->setChargeAmount(new Price(['amount' => $amount, 'currencyCode' => $configurationService->getCurrency()]));
                $logger->debug('checkout action payment details', ['payment_details' => $paymentDetails->toArray(), 'order' => $GLOBALS['order']]);
                $checkoutSessionUpdate
                    ->setWebCheckoutDetails($webCheckoutDetails)
                    ->setPaymentDetails($paymentDetails);

                $updatedCheckoutSession = (new ApiService())->getClient()->updateCheckoutSession($_SESSION['amazonCheckoutSessionId'], $checkoutSessionUpdate);
                $logger->debug('checkout updated checkout session', [$updatedCheckoutSession->toArray()]);
                if ($updatedCheckoutSession->getWebCheckoutDetails() && ($redirectUrl = $updatedCheckoutSession->getWebCheckoutDetails()->getAmazonPayRedirectUrl())) {
                    xtc_redirect($redirectUrl);
                } else {
                    $logger->debug('checkout session update failed', ['session' => $updatedCheckoutSession->toArray()]);
                    $this->redirect_with_error();
                }
            }else{
                $logger->debug('::before_process - skip because CheckoutResultReturnUrl is already set');
            }

        }

        public function check_for_open_session()
        {
            $logger = new LogService();
            if (empty($_SESSION['amazonCheckoutSessionId'])) {
                $logger->debug('::check_for_open_session - no checkout session id in session > APB');
                //APB/Pure Payment Flow
                xtc_redirect(xtc_href_link('shop.php', 'do=AmazonPay/PurePayment', 'SSL'));
            }
            $checkoutSessionId = $_SESSION['amazonCheckoutSessionId'];
            $checkoutService = new CheckoutService();
            $checkoutSession = $checkoutService->getCheckoutSession($checkoutSessionId);
            $logger->debug('::check_for_open_session - checkoutSession', ['checkoutSession' => $checkoutSession?$checkoutSession->toArray():null]);

            if (!$checkoutSession || !$checkoutSession->getCheckoutSessionId()) {
                $logger->error('invalid amazon checkout session id', [$checkoutSessionId, $checkoutSession]);
                unset($_SESSION['amazonCheckoutSessionId']);
                $this->redirect_with_error();
            }

            if (empty($checkoutSession->getStatusDetails()) || $checkoutSession->getStatusDetails()->getState() !== StatusDetails::OPEN) {
                $logger->error('invalid checkout session status for checkout', [$checkoutSessionId, $checkoutSession]);
                unset($_SESSION['amazonCheckoutSessionId']);
                $this->redirect_with_error();
            }
            return $checkoutSession;
        }

        protected function redirect_with_error()
        {
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, 'error=amazon_pay', 'SSL'));
        }

        public function after_process()
        {
            global $insert_id;

            $logger = new LogService();
            $logger->debug('::after_process start');
            $checkoutSession = $this->check_for_open_session();
            $order = MainFactory::create('order', $insert_id);
            $apiService = new ApiService();
            $orderService = new OrderService();

            $paymentDetails = new PaymentDetails();
            $paymentDetails->setChargeAmount(new Price(['amount' => round($order->info['pp_total'], 2), 'currencyCode' => $order->info['currency']]));

            $logger->debug('::after_process - payment details', ['paymentDetails' => $paymentDetails->toArray(), 'checkoutSession' => $checkoutSession->toArray()]);
            try {
                $checkoutSession = $apiService->getClient()->completeCheckoutSession($checkoutSession->getCheckoutSessionId(), $paymentDetails);
                $logger->debug('::after_process - completed checkout session', $checkoutSession->toArray());
                $transactionService = new TransactionService();
                if ($checkoutSession->getStatusDetails()->getState() === StatusDetails::COMPLETED) {
                    $chargePermission = $apiService->getClient()->getChargePermission($checkoutSession->getChargePermissionId());
                    $chargePermissionTransaction = $transactionService->persistTransaction($chargePermission, $insert_id);

                    try {
                        $chargePermissionUpdate = (new ChargePermission())->setMerchantMetadata((new MerchantMetadata())->setMerchantReferenceId($insert_id));
                        $chargePermission = $apiService->getClient()->updateChargePermission($checkoutSession->getChargePermissionId(), $chargePermissionUpdate);
                        $logger->debug('checkout finish  - updated charge permission', [$chargePermission->toArray()]);
                        $chargePermissionTransaction = $transactionService->persistTransaction($chargePermission, $insert_id);
                    } catch (Exception $e) {
                        $logger->error('updateChargePermission failed', [$e->getMessage()]);
                    }
                    if ($checkoutSession->getChargeId()) {
                        $charge = $apiService->getClient()->getCharge($checkoutSession->getChargeId());
                        $transactionService->updateCharge($charge, $insert_id, $chargePermissionTransaction->id);
                    }
                } else {
                    $logger->error('checkout session not completed as expected', [$checkoutSession->toArray()]);
                    //TODO ??
                }
                unset($_SESSION['amazonCheckoutSessionId']);

            } catch (Exception $e) {
                $orderService->setOrderStatusDeclined($insert_id);
                $logger->error('Complete checkout session failed: ' . $e->getMessage(), ['trace' => $e->getTrace()]);
                //TODO ??
            }
        }

        public function get_error(): array
        {
            return ['error' => TEXT_AMAZON_PAY_ERROR];
        }

        public function check()
        {
            if (!isset ($this->_check)) {
                if (TABLE_CONFIGURATION === 'configuration') {
                    $check_query = xtc_db_query("SELECT configuration_value FROM " . TABLE_CONFIGURATION . " WHERE configuration_key = 'MODULE_PAYMENT_AMAZON_PAY_STATUS'");
                } else {
                    $check_query = xtc_db_query("SELECT value FROM " . TABLE_CONFIGURATION . " WHERE `key` = 'configuration/MODULE_PAYMENT_AMAZON_PAY_STATUS'");
                }
                $this->_check = xtc_db_num_rows($check_query);
            }

            return $this->_check;
        }

        public function install()
        {
            $values = [
                'MODULE_PAYMENT_AMAZON_PAY_STATUS' => ['value' => 'False', 'type' => 'switcher'],
                'MODULE_PAYMENT_AMAZON_PAY_ALLOWED' => ['value' => ''],
                'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER' => ['value' => '0'],
                'MODULE_PAYMENT_AMAZON_PAY_ZONE' => ['value' => '', 'type' => 'geo-zone'],
                'MODULE_PAYMENT_AMAZON_PAY_ALIAS' => ['value' => 'AMZ'],
            ];

            foreach ($values as $key => $data) {
                try {
                    if (TABLE_CONFIGURATION === 'configuration') {
                        //legacy
                        if (isset($data['type'])) {
                            if ($data['type'] === 'switcher') {
                                $data['set_function'] = "gm_cfg_select_option(array('true', 'false'),'";
                            }
                            if ($data['type'] === 'geo-zone') {
                                $data['set_function'] = "xtc_cfg_pull_down_zone_list('";
                            }
                            unset($data['type']);
                        }
                        DbAdapter::insert(
                            TABLE_CONFIGURATION,
                            [
                                'configuration_key' => $key,
                                'configuration_value' => $data['value'],
                                'sort_order' => 0,
                            ]
                            +
                            (isset($data['type']) ? ['type' => $data['type']] : [])
                        );
                    } else {
                        DbAdapter::insert(
                            TABLE_CONFIGURATION,
                            [
                                'key' => 'configuration/' . $key,
                                'value' => $data['value'],
                                'sort_order' => 0,
                            ]
                            +
                            (isset($data['type']) ? ['type' => $data['type']] : [])
                        );
                    }
                } catch (Exception $e) {
                    //silent
                }
            }
            $installationService = new InstallationService();
            $installationService->process();

        }

        public function remove()
        {
            if (TABLE_CONFIGURATION === 'configuration') {
                xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE `configuration_key` IN ('" . implode("', '", $this->keys()) . "')");
            } else {
                xtc_db_query("DELETE FROM " . TABLE_CONFIGURATION . " WHERE `key` IN ('" . implode("', '", $this->keys()) . "')");
            }
        }

        public function keys(): array
        {
            if (TABLE_CONFIGURATION === 'configuration') {
                return [
                    'MODULE_PAYMENT_AMAZON_PAY_STATUS',
                    'MODULE_PAYMENT_AMAZON_PAY_ALLOWED',
                    'MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER',
                    'MODULE_PAYMENT_AMAZON_PAY_ZONE',
                    'MODULE_PAYMENT_AMAZON_PAY_ALIAS',
                ];
            } else {
                return [
                    'configuration/MODULE_PAYMENT_AMAZON_PAY_STATUS',
                    'configuration/MODULE_PAYMENT_AMAZON_PAY_ALLOWED',
                    'configuration/MODULE_PAYMENT_AMAZON_PAY_SORT_ORDER',
                    'configuration/MODULE_PAYMENT_AMAZON_PAY_ZONE',
                    'configuration/MODULE_PAYMENT_AMAZON_PAY_ALIAS',
                ];
            }
        }
    }
}
if (class_exists('MainFactory') && method_exists('MainFactory', 'loadClass')) {
    MainFactory::loadClass('amazon_pay');
} else {
    MainFactory::load_origin_class('amazon_pay');
}