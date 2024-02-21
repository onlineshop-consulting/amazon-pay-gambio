<?php

use OncoAmazonPay\ConfigurationService;
use OncoAmazonPay\LogService;
use OncoAmazonPay\ValidationService;

require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay/amazon_pay.inc.php';

class AmazonPayCheckoutConfigurationController extends AdminHttpViewController
{
    public function actionDefault()
    {
        $title = new NonEmptyStringType(APC_CONFIGURATION_TITLE);
        $template = new ExistingFile(new NonEmptyStringType(DIR_FS_CATALOG . '/GXModules/OnlineshopConsulting/AmazonPay/Admin/Html/amazon_pay_checkout_configuration.html'));
        $data = $this->getTemplateData();
        $assets = $this->getAssetData();

        return MainFactory::create(AdminLayoutHttpControllerResponse::class, $title, $template, $data, $assets);
    }

    public function actionGetConfiguration()
    {
        $configurationService = new ConfigurationService();
        return MainFactory::create(JsonHttpControllerResponse::class, [
            'configuration' => $configurationService->getConfiguration()->toArray(),
            'readonly' => [
                'publicKey' => $configurationService->getPublicKey(),
                'ipnUrl' => $configurationService->getIpnUrl(),
            ],
        ]);
    }

    public function actionResetKey()
    {
        $configurationService = new ConfigurationService();
        $configurationService->resetKey();
        return MainFactory::create(JsonHttpControllerResponse::class, [
            'publicKey' => $configurationService->getPublicKey(),
        ]);
    }

    protected function getTemplateData()
    {
        return MainFactory::create(KeyValueCollection::class,
            [
                'urls' => [
                    'saveConfiguration' => xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration/saveConfiguration'),
                    'getConfiguration' => xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration/getConfiguration'),
                    'resetKey' => xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration/resetKey'),
                    'logo' => DIR_WS_CATALOG . 'images/icons/payment/amazon_pay.svg',
                ],
                'options' => [
                    'logLevel' => [
                        [
                            'value' => LogService::LOG_LEVEL_DEBUG,
                            'label' => APC_LOG_LEVEL_DEBUG_OPTION_LABEL,
                        ],
                        [
                            'value' => LogService::LOG_LEVEL_ERROR,
                            'label' => APC_LOG_LEVEL_ERROR_OPTION_LABEL,
                        ],
                    ],
                    'checkoutButtonColor' => [
                        [
                            'value' => 'Gold',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_GOLD_OPTION_LABEL,
                        ],
                        [
                            'value' => 'LightGray',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_LIGHTGRAY_OPTION_LABEL,
                        ],
                        [
                            'value' => 'DarkGray',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_DARKGRAY_OPTION_LABEL,
                        ],
                    ],
                    'loginButtonColor' => [
                        [
                            'value' => 'Gold',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_GOLD_OPTION_LABEL,
                        ],
                        [
                            'value' => 'LightGray',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_LIGHTGRAY_OPTION_LABEL,
                        ],
                        [
                            'value' => 'DarkGray',
                            'label' => APC_CHECKOUT_BUTTON_COLOR_DARKGRAY_OPTION_LABEL,
                        ],
                        [
                            'value' => '',
                            'label' => APC_LOGIN_BUTTON_NO_LOGIN_OPTION_LABEL,
                        ],
                    ],
                    'captureMode' => [
                        [
                            'value' => \OncoAmazonPay\Struct\Configuration::CAPTURE_MODE_IMMEDIATELY,
                            'label' => APC_CAPTURE_MODE_IMMEDIATELY,
                        ],
                        [
                            'value' => \OncoAmazonPay\Struct\Configuration::CAPTURE_MODE_MANUALLY,
                            'label' => APC_CAPTURE_MODE_MANUALLY,
                        ],
                    ],
                    'orderStatus' => $this->getOrderStatusArray(APC_CHECKOUT_ORDER_STATUS_NO_CHANGE),
                    'orderStatusTrigger' => $this->getOrderStatusArray(),

                ],
            ]
        );
    }

    protected function getAssetData()
    {
        return MainFactory::create(AssetCollection::class,
            [
                MainFactory::create(Asset::class, DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Admin/Assets/css/admin.css'),
                MainFactory::create(Asset::class, DIR_WS_CATALOG . 'GXModules/OnlineshopConsulting/AmazonPay/Admin/Assets/js/admin.js'),
            ]
        );
    }


    public function actionSaveConfiguration()
    {
        if (!empty($this->_getPostData('configuration'))) {
            $configurationService = new ConfigurationService();
            $configuration = $configurationService->getConfiguration();
            $configuration->setFromArray($this->_getPostData('configuration'));
            $configurationService->cleanConfiguration($configuration);
            $configurationService->saveConfiguration($configuration);
            $configurationService->updateConfigurationValue('MODULE_PAYMENT_AMAZON_PAY_STATUS', $configuration->getStatus() ? 'True' : 'False');
        }

        return MainFactory::create('RedirectHttpControllerResponse',
            xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration/validateConfiguration'));
    }

    public function actionValidateConfiguration()
    {
        $validationService = new ValidationService();
        $validationResult = $validationService->validate();

        $GLOBALS['messageStack']->add_session(
            $validationResult['message'] . (empty($validationResult['exceptionMessage']) ? '' : ' | ' . $validationResult['exceptionMessage']),
            $validationResult['success'] ? 'success' : 'error'
        );

        return MainFactory::create('RedirectHttpControllerResponse',
            xtc_href_link('admin.php', 'do=AmazonPayCheckoutConfiguration'));
    }

    protected function getOrderStatusArray($firstLabel = '')
    {
        $return = [
            [
                'value' => '-1',
                'label' => $firstLabel,
            ],
        ];

        $rs = \OncoAmazonPay\DbAdapter::fetchAll('SELECT * FROM orders_status WHERE language_id = ? ORDER BY orders_status_name', [$_SESSION['languages_id']]);
        foreach ($rs as $row) {
            $return[] = [
                'value' => $row['orders_status_id'],
                'label' => $row['orders_status_name'],
            ];
        }

        return $return;
    }
}
