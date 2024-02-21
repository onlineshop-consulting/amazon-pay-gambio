<?php
namespace AmazonPayApiSdkExtension\Struct;

class Refund extends StructBase
{
    /**
     * @var string
     */
    protected $refundId;

    /**
     * @var string
     */
    protected $chargeId;

    /**
     * @var RefundAmount
     */
    protected $refundAmount;
    /**
     * @var string
     */
    protected $softDescriptor;

    /**
     * @var string
     */
    protected $creationTimestamp;
    /**
     * @var StatusDetails
     */
    protected $statusDetails;
    /**
     * @var string
     */
    protected $releaseEnvironment;

    /**
     * @return string
     */
    public function getRefundId()
    {
        return $this->refundId;
    }

    /**
     * @param string $refundId
     *
     * @return Refund
     */
    public function setRefundId($refundId)
    {
        $this->refundId = $refundId;

        return $this;
    }

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
     * @return Refund
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\RefundAmount
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\RefundAmount $refundAmount
     *
     * @return Refund
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;

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
     * @return Refund
     */
    public function setSoftDescriptor($softDescriptor)
    {
        $this->softDescriptor = $softDescriptor;

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
     * @return Refund
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

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
     * @return Refund
     */
    public function setStatusDetails($statusDetails)
    {
        $this->statusDetails = $statusDetails;

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
     * @return Refund
     */
    public function setReleaseEnvironment($releaseEnvironment)
    {
        $this->releaseEnvironment = $releaseEnvironment;

        return $this;
    }

}
