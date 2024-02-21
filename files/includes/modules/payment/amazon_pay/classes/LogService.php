<?php

namespace OncoAmazonPay;

use FileLog;
use Gambio\Core\Logging\LoggerBuilder;
use LegacyDependencyContainer;
use Psr\Log\LoggerInterface;

class LogService
{
    const LOG_LEVEL_DEBUG = 'debug';
    const LOG_LEVEL_ERROR = 'error';
    protected static $logLevel = null;
    /**
     * @var FileLog|LoggerInterface
     */
    protected $logger;

    public function error($message, array $context = [])
    {
        $this->log(self::LOG_LEVEL_ERROR, $message, $context);
    }

    public function log($level, $message, array $context = [])
    {

        if (class_exists(LoggerBuilder::class)) {
            if (empty($this->logger)) {
                /** @var LoggerBuilder $loggerBuilder */
                $loggerBuilder = LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
                $this->logger = $loggerBuilder->omitRequestData()->changeNamespace('amazon_pay')->build();
            }
            $this->logger->log($level, $message, $context);
        } else {
            //legacy
            if (empty($this->logger)) {
                $this->logger = new FileLog('amazon_pay', true);;
            }
            $prefix = str_pad(strtoupper($level) . ' | ' . date('Y-m-d H:i:s') . ' - '.session_id(), 60, ' ').' - ';
            $this->logger->write($prefix.$message . ' ' . serialize($context) . "\n");
        }


    }

    public function debug($message, array $context = [])
    {
        if (self::$logLevel === null) {
            self::$logLevel = (new ConfigurationService())->getConfiguration()->getLogLevel();
        }
        if (self::$logLevel !== self::LOG_LEVEL_DEBUG) {
            return;
        }
        $this->log(self::LOG_LEVEL_DEBUG, $message, $context);
    }
}