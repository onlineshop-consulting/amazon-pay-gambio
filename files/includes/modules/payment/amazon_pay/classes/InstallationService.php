<?php
declare(strict_types=1);

namespace OncoAmazonPay;

use Exception;
use OncoAmazonPay\Struct\Configuration;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InstallationService
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function process()
    {
        $this->addTable();
        $this->addConfiguration();
        $configHelper = new ConfigurationService();
        $configHelper->resetKey();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     */
    protected function addTable()
    {
        DbAdapter::execute("
            CREATE TABLE IF NOT EXISTS `amazon_pay_transactions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `parent_id` int(11) NULL,
              `reference` varchar(255) NULL,
              `merchant_id` varchar(32) DEFAULT NULL,
              `mode` varchar(16) DEFAULT NULL,
              `type` varchar(16) NULL,
              `time` datetime NULL,
              `expiration` datetime NULL,
              `amount` float NULL,
              `charge_amount` float NULL,
              `captured_amount` float NULL,
              `refunded_amount` float NULL,
              `currency` varchar(16) DEFAULT NULL,
              `status` varchar(32) NULL,
              `last_change` datetime NULL,
              `last_update` datetime NULL,
              `order_id` int(11) NULL,
              `customer_informed` tinyint(1) NULL,
              `admin_informed` tinyint(1) NULL,
              PRIMARY KEY (`id`),
              KEY `parent_id` (`parent_id`),
              KEY `reference` (`reference`),
              KEY `type` (`type`)
            );
        ");
    }

    protected function addConfiguration()
    {
        $configHelper = new ConfigurationService();
        $configHelper->saveConfiguration(new Configuration());
    }
}