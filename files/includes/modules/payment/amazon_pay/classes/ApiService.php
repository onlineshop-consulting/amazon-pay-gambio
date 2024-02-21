<?php
declare(strict_types=1);

namespace OncoAmazonPay;

use AmazonPayApiSdkExtension\Client\Client;
use Exception;

class ApiService
{
    /**
     * @var Client
     */
    private static $client;
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    public function __construct()
    {
        $this->configurationService = new ConfigurationService();
    }

    /**
     * @param bool $forceSandbox
     * @return Client
     * @throws Exception
     */
    public function getClient($forceSandbox = false): Client
    {
        $config = $this->configurationService->getApiClientConfig();
        if ($forceSandbox) {
            $config['sandbox'] = true;
        }
        $config['integrator_id'] = $this->configurationService->getPlatformId();
        $config['integrator_version'] = $this->configurationService->getPluginVersion();
        $config['platform_version'] = '4.0.0'; //TODO
        if (!isset(self::$client) || $forceSandbox) {
            self::$client = new Client($config);
        }

        return self::$client;
    }

    public function getHeaders(): array
    {
        return ['x-amz-pay-Idempotency-Key' => uniqid()];
    }
}