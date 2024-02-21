<?php
declare(strict_types=1);

namespace OncoAmazonPay\Struct;


use OncoAmazonPay\LogService;

class Configuration extends AbstractStruct
{
    const CAPTURE_MODE_IMMEDIATELY = 'immediately';
    const CAPTURE_MODE_MANUALLY = 'manually';


    /**
     * proxy for MODULE_PAYMENT_AMAZON_PAY_STATUS
     * @var bool
     */
    protected $status = false;


    /**
     * @var string|null
     */
    protected $merchantId;
    /**
     * @var string|null
     */
    protected $clientId;
    /**
     * @var string|null
     */
    protected $publicKeyId;
    /**
     * @var string|null
     */
    protected $keyFileName;
    /**
     * @var string|null
     */
    protected $region;
    /**
     * @var bool|null
     */
    protected $isSandbox;

    /**
     * @var bool|null
     */
    protected $isHidden;
    /**
     * @var string|null
     */
    protected $logLevel;
    /**
     * @var string|null
     */
    protected $buttonColorCheckout;
    /**
     * @var string|null
     */
    protected $buttonColorLogin;
    /**
     * @var bool
     */
    protected $showButtonOnProductPage = true;
    /**
     * @var string|null
     */
    protected $captureMode = self::CAPTURE_MODE_IMMEDIATELY;
    /**
     * @var bool|null
     */
    protected $canHandlePendingAuthorization = false;

    /**
     * @var int
     */
    protected $orderStatusAuthorized = -1;
    /**
     * @var int
     */
    protected $orderStatusCapturedCompletely = -1;

    /**
     * @var int
     */
    protected $orderStatusCapturedPartly = -1;
    /**
     * @var int
     */
    protected $orderStatusFailed = -1;
    /**
     * @var int
     */
    protected $orderStatusRefundedCompletely = -1;
    /**
     * @var int
     */
    protected $orderStatusRefundedPartly = -1;
    /**
     * @var int
     */
    protected $orderStatusTriggerRefund = -1;
    /**
     * @var int
     */
    protected $orderStatusTriggerCapture = -1;

    /**
     * @return string|null
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param string|null $merchantId
     * @return Configuration
     */
    public function setMerchantId($merchantId): Configuration
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientId
     * @return Configuration
     */
    public function setClientId($clientId): Configuration
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPublicKeyId()
    {
        return $this->publicKeyId;
    }

    /**
     * @param string|null $publicKeyId
     * @return Configuration
     */
    public function setPublicKeyId($publicKeyId): Configuration
    {
        $this->publicKeyId = $publicKeyId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeyFileName()
    {
        return empty($this->keyFileName) ? '' : $this->keyFileName;
    }

    /**
     * @param string|null $keyFileName
     * @return Configuration
     */
    public function setKeyFileName($keyFileName): Configuration
    {
        $this->keyFileName = $keyFileName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion()
    {
        return 'EU'; //TODO
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return Configuration
     */
    public function setRegion($region): Configuration
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isSandbox(): bool
    {
        return (bool)$this->isSandbox;
    }

    /**
     * @param bool|null $isSandbox
     * @return Configuration
     */
    public function setIsSandbox($isSandbox): Configuration
    {
        $this->isSandbox = (bool)$isSandbox;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus($status): Configuration
    {
        $this->status = (bool)$status;
        return $this;
    }



    /**
     * @return bool|null
     */
    public function isHidden(): bool
    {
        return (bool)$this->isHidden;
    }

    /**
     * @param bool|null $isHidden
     * @return Configuration
     */
    public function setIsHidden($isHidden): Configuration
    {
        $this->isHidden = (bool)$isHidden;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogLevel(): string
    {
        return empty($this->logLevel) ? LogService::LOG_LEVEL_DEBUG : $this->logLevel;
    }

    /**
     * @param string|null $logLevel
     * @return Configuration
     */
    public function setLogLevel($logLevel): Configuration
    {
        $this->logLevel = $logLevel;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getButtonColorCheckout(): string
    {
        return !is_string($this->buttonColorCheckout) ? 'Gold' : $this->buttonColorCheckout;
    }

    /**
     * @param string|null $buttonColorCheckout
     * @return Configuration
     */
    public function setButtonColorCheckout($buttonColorCheckout): Configuration
    {
        $this->buttonColorCheckout = $buttonColorCheckout;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getButtonColorLogin(): string
    {
        return !is_string($this->buttonColorLogin) ? 'Gold' : $this->buttonColorLogin;
    }

    /**
     * @param string|null $buttonColorLogin
     * @return Configuration
     */
    public function setButtonColorLogin($buttonColorLogin): Configuration
    {
        $this->buttonColorLogin = $buttonColorLogin;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowButtonOnProductPage(): bool
    {
        return $this->showButtonOnProductPage;
    }

    /**
     * @param bool $showButtonOnProductPage
     * @return Configuration
     */
    public function setShowButtonOnProductPage($showButtonOnProductPage): Configuration
    {
        $this->showButtonOnProductPage = (bool)$showButtonOnProductPage;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getCaptureMode()
    {
        return $this->captureMode;
    }

    /**
     * @param string|null $captureMode
     * @return Configuration
     */
    public function setCaptureMode($captureMode): Configuration
    {
        $this->captureMode = $captureMode;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCanHandlePendingAuthorization(): bool
    {
        return (bool)$this->canHandlePendingAuthorization;
    }

    /**
     * @param bool|null $canHandlePendingAuthorization
     * @return Configuration
     */
    public function setCanHandlePendingAuthorization($canHandlePendingAuthorization): Configuration
    {
        $this->canHandlePendingAuthorization = (bool)$canHandlePendingAuthorization;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderStatusAuthorized(): int
    {
        return (int)$this->orderStatusAuthorized;
    }

    /**
     * @param int|null $orderStatusAuthorized
     * @return Configuration
     */
    public function setOrderStatusAuthorized($orderStatusAuthorized): Configuration
    {
        $this->orderStatusAuthorized = (int)$orderStatusAuthorized;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderStatusCapturedCompletely(): int
    {
        return (int)$this->orderStatusCapturedCompletely;
    }

    /**
     * @param int|null $orderStatusCapturedCompletely
     * @return Configuration
     */
    public function setOrderStatusCapturedCompletely($orderStatusCapturedCompletely): Configuration
    {
        $this->orderStatusCapturedCompletely = (int)$orderStatusCapturedCompletely;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderStatusCapturedPartly(): int
    {
        return (int)$this->orderStatusCapturedPartly;
    }

    /**
     * @param int|null $orderStatusCapturedPartly
     * @return Configuration
     */
    public function setOrderStatusCapturedPartly($orderStatusCapturedPartly): Configuration
    {
        $this->orderStatusCapturedPartly = (int)$orderStatusCapturedPartly;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getOrderStatusFailed(): int
    {
        return (int)$this->orderStatusFailed;
    }

    /**
     * @param int|null $orderStatusFailed
     * @return Configuration
     */
    public function setOrderStatusFailed($orderStatusFailed): Configuration
    {
        $this->orderStatusFailed = (int)$orderStatusFailed;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderStatusRefundedCompletely(): int
    {
        return (int)$this->orderStatusRefundedCompletely;
    }

    /**
     * @param int|null $orderStatusRefundedCompletely
     * @return Configuration
     */
    public function setOrderStatusRefundedCompletely($orderStatusRefundedCompletely): Configuration
    {
        $this->orderStatusRefundedCompletely = (int)$orderStatusRefundedCompletely;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrderStatusRefundedPartly(): int
    {
        return (int)$this->orderStatusRefundedPartly;
    }

    /**
     * @param int|null $orderStatusRefundedPartly
     * @return Configuration
     */
    public function setOrderStatusRefundedPartly($orderStatusRefundedPartly): Configuration
    {
        $this->orderStatusRefundedPartly = (int)$orderStatusRefundedPartly;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderStatusTriggerRefund(): int
    {
        return $this->orderStatusTriggerRefund;
    }

    /**
     * @param int $orderStatusTriggerRefund
     * @return Configuration
     */
    public function setOrderStatusTriggerRefund($orderStatusTriggerRefund): Configuration
    {
        $this->orderStatusTriggerRefund = (int)$orderStatusTriggerRefund;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderStatusTriggerCapture(): int
    {
        return $this->orderStatusTriggerCapture;
    }

    /**
     * @param int $orderStatusTriggerCapture
     * @return Configuration
     */
    public function setOrderStatusTriggerCapture($orderStatusTriggerCapture): Configuration
    {
        $this->orderStatusTriggerCapture = (int)$orderStatusTriggerCapture;
        return $this;
    }


}


