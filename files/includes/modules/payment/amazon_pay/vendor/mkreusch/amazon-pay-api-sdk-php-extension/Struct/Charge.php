<?php
namespace AmazonPayApiSdkExtension\Struct;

class Charge extends StructBase
{
    /**
     * @var string
     */
    protected $chargeId;
    /**
     * @var string
     */
    protected $chargePermissionId;
    /**
     * @var ChargeAmount
     */
    protected $chargeAmount;
    /**
     * @var CaptureAmount
     */
    protected $captureAmount;
    /**
     * @var RefundedAmount
     */
    protected $refundedAmount;
    /**
     * @var string
     */
    protected $softDescriptor;
    /**
     * @var bool
     */
    protected $captureNow;
    /**
     * @var bool
     */
    protected $canHandlePendingAuthorization;
    /**
     * @var ProviderMetadata
     */
    protected $providerMetadata;
    /**
     * @var string
     */
    protected $creationTimestamp;
    /**
     * @var string
     */
    protected $expirationTimestamp;
    /**
     * @var StatusDetails
     */
    protected $statusDetails;
    /**
     * @var ConvertedAmount
     */
    protected $convertedAmount;
    /**
     * @var double
     */
    protected $conversionRate;
    /**
     * @var string
     */
    protected $releaseEnvironment;

    /**
     * @return string
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }

    /**
     * @param string $chargeId
     *
     * @return Charge
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getChargePermissionId()
    {
        return $this->chargePermissionId;
    }

    /**
     * @param string $chargePermissionId
     *
     * @return Charge
     */
    public function setChargePermissionId($chargePermissionId)
    {
        $this->chargePermissionId = $chargePermissionId;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\ChargeAmount
     */
    public function getChargeAmount()
    {
        return $this->chargeAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\ChargeAmount $chargeAmount
     *
     * @return Charge
     */
    public function setChargeAmount($chargeAmount)
    {
        $this->chargeAmount = $chargeAmount;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\CaptureAmount
     */
    public function getCaptureAmount()
    {
        return $this->captureAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\CaptureAmount $captureAmount
     *
     * @return Charge
     */
    public function setCaptureAmount($captureAmount)
    {
        $this->captureAmount = $captureAmount;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\RefundedAmount
     */
    public function getRefundedAmount()
    {
        return $this->refundedAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\RefundedAmount $refundedAmount
     *
     * @return Charge
     */
    public function setRefundedAmount($refundedAmount)
    {
        $this->refundedAmount = $refundedAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoftDescriptor()
    {
        return $this->softDescriptor;
    }

    /**
     * @param string $softDescriptor
     *
     * @return Charge
     */
    public function setSoftDescriptor($softDescriptor)
    {
        $this->softDescriptor = $softDescriptor;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCaptureNow()
    {
        return $this->captureNow;
    }

    /**
     * @param bool $captureNow
     *
     * @return Charge
     */
    public function setCaptureNow($captureNow)
    {
        $this->captureNow = $captureNow;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCanHandlePendingAuthorization()
    {
        return $this->canHandlePendingAuthorization;
    }

    /**
     * @param bool $canHandlePendingAuthorization
     *
     * @return Charge
     */
    public function setCanHandlePendingAuthorization($canHandlePendingAuthorization)
    {
        $this->canHandlePendingAuthorization = $canHandlePendingAuthorization;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\ProviderMetadata
     */
    public function getProviderMetadata()
    {
        return $this->providerMetadata;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\ProviderMetadata $providerMetadata
     *
     * @return Charge
     */
    public function setProviderMetadata($providerMetadata)
    {
        $this->providerMetadata = $providerMetadata;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     * @param string $creationTimestamp
     *
     * @return Charge
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationTimestamp()
    {
        return $this->expirationTimestamp;
    }

    /**
     * @param string $expirationTimestamp
     *
     * @return Charge
     */
    public function setExpirationTimestamp($expirationTimestamp)
    {
        $this->expirationTimestamp = $expirationTimestamp;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\StatusDetails
     */
    public function getStatusDetails()
    {
        return $this->statusDetails;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\StatusDetails $statusDetails
     *
     * @return Charge
     */
    public function setStatusDetails($statusDetails)
    {
        $this->statusDetails = $statusDetails;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\ConvertedAmount
     */
    public function getConvertedAmount()
    {
        return $this->convertedAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\ConvertedAmount $convertedAmount
     *
     * @return Charge
     */
    public function setConvertedAmount($convertedAmount)
    {
        $this->convertedAmount = $convertedAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getConversionRate()
    {
        return $this->conversionRate;
    }

    /**
     * @param float $conversionRate
     *
     * @return Charge
     */
    public function setConversionRate($conversionRate)
    {
        $this->conversionRate = $conversionRate;

        return $this;
    }

    /**
     * @return string
     */
    public function getReleaseEnvironment()
    {
        return $this->releaseEnvironment;
    }

    /**
     * @param string $releaseEnvironment
     *
     * @return Charge
     */
    public function setReleaseEnvironment($releaseEnvironment)
    {
        $this->releaseEnvironment = $releaseEnvironment;

        return $this;
    }


}
