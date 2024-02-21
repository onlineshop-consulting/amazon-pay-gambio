<?php
declare(strict_types=1);

class AmazonPayCronjobLogger extends AbstractCronjobLogger
{
    /**
     * @param array $context
     */
    public function log(array $context = [])
    {
        if (!empty($context['message']) && !empty($context['level'])) {
            $this->logger->log($context['level'], $context['message']);
        } else {
            $this->logger->info('Amazon Pay Cronjob', $context);
        }
    }


    /**
     * @param array $context
     */
    public function logError(array $context = [])
    {
        if (!empty($context['message'])) {
            $this->logger->error($context['message']);
        } else {
            $this->logger->error('Amazon Pay Cronjob error', $context);
        }
    }
}
