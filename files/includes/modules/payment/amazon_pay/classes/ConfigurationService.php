<?php

namespace OncoAmazonPay;

use amazon_pay_ORIGIN;
use Exception;
use MainFactory;
use OncoAmazonPay\Struct\Configuration;
use phpseclib\Crypt\RSA;
use stdClass;


if (!class_exists('amazon_pay_ORIGIN')) {
    require_once DIR_FS_CATALOG . 'includes/modules/payment/amazon_pay.php';
}

class ConfigurationService
{
    const SHOP_CONFIGURATION_KEY = 'AMAZON_PAY_CONFIGURATION_JSON';

    public $config;

    protected $configuration;
    /**
     * @var LogService
     */
    private $logger;

    public function __construct()
    {
        MainFactory::load_class('amazon_pay');
        $this->logger = new LogService();
    }

    public function hasLocalButtons()
    {
        return file_exists(DIR_FS_CATALOG . '.amazon-pay-local-buttons');
    }

    public function getApiClientConfig()
    {
        return [
            'public_key_id' => $this->getConfiguration()->getPublicKeyId(),
            'private_key' => $this->getPrivateKeyPath(),
            'region' => $this->getConfiguration()->getRegion(),
            'sandbox' => $this->getConfiguration()->isSandbox(),
        ];
    }

    public function getConfiguration($forceReload = false): Configuration
    {
        if (!$this->configuration || $forceReload) {
            $this->configuration = new Configuration(json_decode($this->getShopConfigurationValue(self::SHOP_CONFIGURATION_KEY), true));
        }
        if (defined('MODULE_PAYMENT_AMAZON_PAY_STATUS')) {
            if (strtolower(MODULE_PAYMENT_AMAZON_PAY_STATUS) === 'true') {
                $this->configuration->setStatus(true);
            }
        }
        return $this->configuration;
    }

    public function getShopConfigurationValue($key)
    {
        return defined($key) ? constant($key) : null;
    }

    public function getPrivateKeyPath(): string
    {
        $this->checkKey();
        return $this->getBasePath() . 'keys/' . $this->getConfiguration()->getKeyFileName() . '.pem';
    }

    protected function checkKey()
    {
        if (empty($this->getConfiguration()->getKeyFileName())) {
            try {
                $this->resetKey();
            } catch (Exception $e) {
                $this->logger->error('Could not reset key', ['exception' => $e, 'trace'=>$e->getTrace()]);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function resetKey()
    {
        $this->logger->debug('reset key', ['trace' => debug_backtrace(8)]);
        $configuration = $this->getConfiguration();
        $rsaService = new RSA();
        if (!($keys = $rsaService->createKey(2048))) {
            throw new Exception('Could not create key pair');
        }
        $configuration->setKeyFileName(uniqid('amz_', true));
        $writeResultPrivate = file_put_contents($this->getPrivateKeyPath(), $keys['privatekey']);
        $writeResultPublic = file_put_contents($this->getPublicKeyPath(), $keys['publickey']);
        if (!$writeResultPrivate || !$writeResultPublic) {
            throw new Exception('Could not write key files');
        }
        $this->saveConfiguration($configuration);

    }

    public function getPublicKeyPath(): string
    {
        $this->checkKey();
        return $this->getBasePath() . 'keys/' . $this->getConfiguration()->getKeyFileName() . '.pub';
    }

    public function getBasePath(): string
    {
        return dirname(__DIR__) . '/';
    }

    public function saveConfiguration(Configuration $configuration)
    {
        $this->saveShopConfigurationValue(self::SHOP_CONFIGURATION_KEY, json_encode($configuration->toArray()));
        $this->configuration = $configuration;
    }

    public static function isOldConfigTable(): bool
    {
        if (defined('TABLE_CONFIGURATION') && TABLE_CONFIGURATION === 'configuration') {
            $q = "SHOW TABLES LIKE 'configuration'";
            $result = xtc_db_query($q);
            return xtc_db_num_rows($result) > 0;
        }
        return false;
    }

    public function saveShopConfigurationValue($key, $value)
    {

        if (self::isOldConfigTable()) {
            $q = "INSERT INTO configuration SET
                `configuration_key` = ?,
                `configuration_value` = ?
                ON DUPLICATE KEY UPDATE
                configuration_value = VALUES(`configuration_value`)";
            DbAdapter::execute($q, [$key, $value]);
        } else {
            if (strpos($key, '/') === false) {
                $key = 'configuration/' . $key;
            }

            //GX4.4 had no unique key on configuration table, so we need to delete the old value first
            $q = "DELETE FROM gx_configurations WHERE `key` = ?";
            DbAdapter::execute($q, [$key]);

            $q = "INSERT INTO gx_configurations SET
                `key` = ?,
                `value` = ?
                ON DUPLICATE KEY UPDATE
                value = VALUES(`value`)";
            DbAdapter::execute($q, [$key, $value]);
        }

    }

    public function isPaymentMethodEnabled()
    {
        return defined('MODULE_PAYMENT_AMAZON_PAY_STATUS') && strtolower(MODULE_PAYMENT_AMAZON_PAY_STATUS) === 'true';
    }

    public function isConfigurationComplete()
    {
        $configuration = $this->getConfiguration();
        return $configuration->getMerchantId() && $configuration->getClientId() && $configuration->getPublicKeyId() && $configuration->getRegion();
    }

    public function getPublicConfigurationArray(): array
    {
        $configuration = $this->getConfiguration();

        return [
            'merchantId' => $configuration->getMerchantId(),
            'createCheckoutSessionUrl' => $this->getCreateCheckoutSessionUrl(),
            'isSandbox' => $configuration->isSandbox(),
            'isPayOnly' => false,
            'currency' => $this->getCurrency(),
            'isHidden' => $configuration->isHidden(),
            'ledgerCurrency' => $this->getLedgerCurrency(),
            'region' => $configuration->getRegion(),
            'defaultErrorMessage' => '',
            'language' => $this->getLanguage(),
            'checkoutButtonColor' => $configuration->getButtonColorCheckout(),
            'loginButtonColor' => $configuration->getButtonColorLogin(),
            'publicKeyId' => $configuration->getPublicKeyId(),
        ];
    }

    protected function getCreateCheckoutSessionUrl(): string
    {
        return xtc_href_link('shop.php', 'do=AmazonPay/CreateCheckoutSession', 'SSL');
    }

    public function getCurrency()
    {
        return $_SESSION['currency'];
    }

    protected function getLedgerCurrency(): string
    {
        return $this->getConfiguration()->getRegion() === 'UK' ? 'GBP' : 'EUR';
    }

    protected function getLanguage(): string
    {
        $supportedLanguages = [
            'en' => 'en_GB',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'it' => 'it_IT',
            'es' => 'es_ES',
        ];
        if (isset($supportedLanguages[$_SESSION['language_code']])) {
            return $supportedLanguages[$_SESSION['language_code']];
        } else {
            return 'de_DE';
        }
    }

    public function cleanConfiguration(Configuration $configuration)
    {
        $configuration->setMerchantId(trim($configuration->getMerchantId()));
        $configuration->setPublicKeyId(trim($configuration->getPublicKeyId()));
        $configuration->setClientId(trim($configuration->getClientId()));
    }

    public function getPublicKey(): string
    {
        return (string)file_get_contents($this->getPublicKeyPath());
    }

    public function getAllowedCountries()
    {
        $return = [];
        $q = "SELECT countries_iso_code_2 AS iso FROM " . TABLE_COUNTRIES . " WHERE status = '1'";
        foreach (DbAdapter::fetchAll($q) as $country) {
            if ($country['iso'] === 'XI') {
                continue;
            }
            $return[$country['iso']] = new stdClass();
        }
        return $return;
    }

    public function updateConfigurationValue($key, $value)
    {
        if (self::isOldConfigTable()) {
            xtc_db_perform(
                'configuration',
                [
                    'configuration_value' => $value,
                ],
                'update',
                "configuration_key = '" . xtc_db_input($key) . "'"
            );
        } else {
            xtc_db_perform(
                'gx_configurations',
                [
                    'value' => $value,
                ],
                'update',
                " `key` = 'configuration/" . xtc_db_input($key) . "'"
            );
        }

    }

    public function getPlatformId(): string
    {
        return amazon_pay_ORIGIN::PLATFORM_ID;
    }

    public function getCustomInformationString(): string
    {
        return 'Gambio CV2 by onlineshop.consulting, v' . $this->getPluginVersion();
    }

    public function getPluginVersion(): string
    {
        return amazon_pay_ORIGIN::VERSION;
    }

    public function getReviewReturnUrl(): string
    {
        return xtc_href_link('shop.php', 'do=AmazonPay/ReviewReturn', 'SSL');
    }

    public function getIpnUrl(): string
    {
        return xtc_catalog_href_link('shop.php', 'do=AmazonPay/ipn', 'SSL');
    }

    public function getCheckoutResultReturnUrl(): string
    {
        return xtc_href_link('shop.php', 'do=AmazonPay/CheckoutResultReturn', 'SSL');
    }

    public function getCancelUrl(): string
    {
        return xtc_href_link('checkout_payment.php', 'resetAmazonPaySession=1', 'SSL');
    }

}