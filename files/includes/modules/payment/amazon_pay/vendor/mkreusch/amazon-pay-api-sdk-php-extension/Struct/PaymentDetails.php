<?php

namespace AmazonPayApiSdkExtension\Struct;

class PaymentDetails extends StructBase
{
    /**
     * @var string
     * @comment
     * 'Confirm' - Create a Charge Permission to authorize and capture funds at a later time
     * 'Authorize' - Authorize funds immediately and capture at a later time
     * 'AuthorizeWithCapture' - Authorize and capture funds immediately. You must set` a softDescriptor if you use this paymentIntent and you can't set canHandlePendingAuthorization to true
     */
    protected $paymentIntent;
    /**
     * @var bool
     */
    protected $canHandlePendingAuthorization;
    /**
     * @var Price
     */
    protected $chargeAmount;
    /**
     * @var string
     */
    protected $presentmentCurrency;
    /**
     * @var string
     */
    protected $softDescriptor;
    /**
     * @var string
     */
    /**
     * @var Price
     */
    protected $totalOrderAmount;

    /**
     * @var bool
     */
protected $allowOvercharge;



    /**
     * @var mixed
     */
    protected $extendExpiration;

    /**
     * @return string
     */
    public function getPaymentIntent()
    {
        return $this->paymentIntent;
    }

    /**
     * @param string $paymentIntent
     *
     * @return PaymentDetails
     */
    public function setPaymentIntent($paymentIntent)
    {
        $this->paymentIntent = $paymentIntent;

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
     * @return PaymentDetails
     */
    public function setCanHandlePendingAuthorization($canHandlePendingAuthorization)
    {
        $this->canHandlePendingAuthorization = $canHandlePendingAuthorization;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\Price
     */
    public function getChargeAmount()
    {
        return $this->chargeAmount;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\Price $chargeAmount
     *
     * @return PaymentDetails
     */
    public function setChargeAmount($chargeAmount)
    {
        $this->chargeAmount = $chargeAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPresentmentCurrency()
    {
        return $this->presentmentCurrency;
    }

    /**
     * @param string $presentmentCurrency
     *
     * @return PaymentDetails
     */
    public function setPresentmentCurrency($presentmentCurrency)
    {
        $this->presentmentCurrency = $presentmentCurrency;

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
     * @return PaymentDetails
     */
    public function setSoftDescriptor($softDescriptor)
    {
        $this->softDescriptor = $softDescriptor;

        return $this;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\Price $totalOrderAmount
     *
     * @return PaymentDetails
     */
    public function setTotalOrderAmount($totalOrderAmount)
    {
        $this->totalOrderAmount = $totalOrderAmount;

        return $this;
}

    /**
     * @return \AmazonPayApiSdkExtension\Struct\Price
     */
    public function getTotalOrderAmount()
    {
        return $this->totalOrderAmount;
    }

    /**
     * @return bool
     */
    public function isAllowOvercharge()
    {
        return $this->allowOvercharge;
    }

    /**
     * @param bool $allowOvercharge
     *
     * @return PaymentDetails
     */
    public function setAllowOvercharge($allowOvercharge)
    {
        $this->allowOvercharge = $allowOvercharge;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtendExpiration()
    {
        return $this->extendExpiration;
    }

    /**
     * @param mixed $extendExpiration
     *
     * @return PaymentDetails
     */
    public function setExtendExpiration($extendExpiration)
    {
        $this->extendExpiration = $extendExpiration;

        return $this;
    }
}
