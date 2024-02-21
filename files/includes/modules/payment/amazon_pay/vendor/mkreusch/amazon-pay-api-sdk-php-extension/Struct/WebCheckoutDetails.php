<?php

namespace AmazonPayApiSdkExtension\Struct;

class WebCheckoutDetails extends StructBase
{
    const CHECKOUT_MODE_PROCESS_ORDER = 'ProcessOrder';

    /**
     * @var string
     */
    protected $checkoutReviewReturnUrl;

    /**
     * @var string
     */
    protected $checkoutResultReturnUrl;

    /**
     * @var string
     */
    protected $amazonPayRedirectUrl;

    /**
     * @var string
     */
    protected $checkoutMode;

    /**
     * @var string
     */
    protected $checkoutCancelUrl;

    /**
     * @return string
     */
    public function getCheckoutCancelUrl()
    {
        return $this->checkoutCancelUrl;
    }

    /**
     * @param string $checkoutCancelUrl
     * @return WebCheckoutDetails
     */
    public function setCheckoutCancelUrl($checkoutCancelUrl)
    {
        $this->checkoutCancelUrl = $checkoutCancelUrl;
        return $this;
    }



    /**
     * @return string
     */
    public function getCheckoutReviewReturnUrl()
    {
        return $this->checkoutReviewReturnUrl;
    }

    /**
     * @param string $checkoutReviewReturnUrl
     *
     * @return WebCheckoutDetails
     */
    public function setCheckoutReviewReturnUrl($checkoutReviewReturnUrl)
    {
        $this->checkoutReviewReturnUrl = $checkoutReviewReturnUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutResultReturnUrl()
    {
        return $this->checkoutResultReturnUrl;
    }

    /**
     * @param string $checkoutResultReturnUrl
     *
     * @return WebCheckoutDetails
     */
    public function setCheckoutResultReturnUrl($checkoutResultReturnUrl)
    {
        $this->checkoutResultReturnUrl = $checkoutResultReturnUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmazonPayRedirectUrl()
    {
        return $this->amazonPayRedirectUrl;
    }

    /**
     * @param string $amazonPayRedirectUrl
     *
     * @return WebCheckoutDetails
     */
    public function setAmazonPayRedirectUrl($amazonPayRedirectUrl)
    {
        $this->amazonPayRedirectUrl = $amazonPayRedirectUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckoutMode()
    {
        return $this->checkoutMode;
    }

    /**
     * @param string $checkoutMode
     * @return WebCheckoutDetails
     */
    public function setCheckoutMode($checkoutMode)
    {
        $this->checkoutMode = $checkoutMode;
        return $this;
    }

}