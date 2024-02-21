<?php

namespace AmazonPayApiSdkExtension\Struct;

class CheckoutSession extends StructBase
{
    const CHARGE_PERMISSION_TYPE_ONE_TIME = 'OneTime';
    const CHARGE_PERMISSION_TYPE_RECURRING = 'Recurring';
    /**
     * @var string
     */
    protected $checkoutSessionId;

    /**
     * @var WebCheckoutDetails
     */
    protected $webCheckoutDetails;
    /**
     * @var string
     */
    protected $productType;

    /**
     * @var PaymentDetails
     */
    protected $paymentDetails;

    /**
     * @var AddressDetails
     */
    protected $addressDetails;

    /**
     * @var MerchantMetadata
     */
    protected $merchantMetadata;

    /**
     * @var string
     */
    protected $platformId;

    /**
     * @var ProviderMetadata
     */
    protected $providerMetadata;

    /**
     * @var RecurringMetadata
     */
    protected $recurringMetadata;

    /**
     * @var Buyer
     */
    protected $buyer;

    /**
     * @var ShippingAddress
     */
    protected $shippingAddress;

    /**
     * @var BillingAddress
     */
    protected $billingAddress;

    protected $supplementaryData;

    /**
     * @var string
     */

    protected $reasonCode;
    /**
     * @var string
     */
    protected $message;

    /**
     * @var ???
     */
    protected $paymentPreferences;

    /**
     * @var StatusDetails
     */
    protected $statusDetails;

    /**
     * @var ???
     */
    protected $constraints;

    /**
     * @var string
     */
    protected $creationTimestamp;

    /**
     * @var string
     */
    protected $expirationTimestamp;
    /**
     * @var string
     */
    protected $chargePermissionId;
    /**
     * @var string
     */
    protected $chargeId;
    /**
     * @var string
     */
    protected $storeId;

    /**
     * @var DeliverySpecifications
     */
    protected $deliverySpecifications;

    /**
     * @var string
     */
    protected $releaseEnvironment;

    /**
     * @var string
     */
    protected $chargePermissionType;

    /**
     * @return string
     */
    public function getCheckoutSessionId()
    {
        return $this->checkoutSessionId;
    }

    /**
     * @param string $checkoutSessionId
     *
     * @return CheckoutSession
     */
    public function setCheckoutSessionId($checkoutSessionId)
    {
        $this->checkoutSessionId = $checkoutSessionId;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\WebCheckoutDetails
     */
    public function getWebCheckoutDetails()
    {
        return $this->webCheckoutDetails;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\WebCheckoutDetails $webCheckoutDetails
     *
     * @return CheckoutSession
     */
    public function setWebCheckoutDetails($webCheckoutDetails)
    {
        $this->webCheckoutDetails = $webCheckoutDetails;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @param string $productType
     *
     * @return CheckoutSession
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\PaymentDetails
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\PaymentDetails $paymentDetails
     *
     * @return CheckoutSession
     */
    public function setPaymentDetails($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
    }

    /**
     * @return AddressDetails
     */
    public function getAddressDetails()
    {
        return $this->addressDetails;
    }

    /**
     * @param AddressDetails $addressDetails
     * @return CheckoutSession
     */
    public function setAddressDetails($addressDetails)
    {
        $this->addressDetails = $addressDetails;
        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\MerchantMetadata
     */
    public function getMerchantMetadata()
    {
        return $this->merchantMetadata;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\MerchantMetadata $merchantMetadata
     *
     * @return CheckoutSession
     */
    public function setMerchantMetadata($merchantMetadata)
    {
        $this->merchantMetadata = $merchantMetadata;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * @param string $platformId
     *
     * @return CheckoutSession
     */
    public function setPlatformId($platformId)
    {
        $this->platformId = $platformId;

        return $this;
    }

    /**
     * @return RecurringMetadata
     */
    public function getRecurringMetadata()
    {
        return $this->recurringMetadata;
    }

    /**
     * @param RecurringMetadata|NullStruct $recurringMetadata
     * @return CheckoutSession
     */
    public function setRecurringMetadata($recurringMetadata)
    {
        $this->recurringMetadata = $recurringMetadata;

        return $this;
    }

    /**
     * @return ProviderMetadata
     */
    public function getProviderMetadata()
    {
        return $this->providerMetadata;
    }

    /**
     * @param ProviderMetadata $providerMetadata
     *
     * @return CheckoutSession
     */
    public function setProviderMetadata($providerMetadata)
    {
        $this->providerMetadata = $providerMetadata;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\Buyer
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\Buyer $buyer
     *
     * @return CheckoutSession
     */
    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\Address $shippingAddress
     *
     * @return CheckoutSession
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentPreferences()
    {
        return $this->paymentPreferences;
    }

    /**
     * @param mixed $paymentPreferences
     *
     * @return CheckoutSession
     */
    public function setPaymentPreferences($paymentPreferences)
    {
        $this->paymentPreferences = $paymentPreferences;

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
     * @return CheckoutSession
     */
    public function setStatusDetails($statusDetails)
    {
        $this->statusDetails = $statusDetails;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param mixed $constraints
     *
     * @return CheckoutSession
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

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
     * @return CheckoutSession
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
     * @return CheckoutSession
     */
    public function setExpirationTimestamp($expirationTimestamp)
    {
        $this->expirationTimestamp = $expirationTimestamp;

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
     * @return CheckoutSession
     */
    public function setChargePermissionId($chargePermissionId)
    {
        $this->chargePermissionId = $chargePermissionId;

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
     * @return CheckoutSession
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param string $storeId
     *
     * @return CheckoutSession
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\DeliverySpecifications
     */
    public function getDeliverySpecifications()
    {
        return $this->deliverySpecifications;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\DeliverySpecifications $deliverySpecifications
     *
     * @return CheckoutSession
     */
    public function setDeliverySpecifications($deliverySpecifications)
    {
        $this->deliverySpecifications = $deliverySpecifications;

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
     * @return CheckoutSession
     */
    public function setReleaseEnvironment($releaseEnvironment)
    {
        $this->releaseEnvironment = $releaseEnvironment;

        return $this;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\BillingAddress $billingAddress
     *
     * @return CheckoutSession
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\BillingAddress
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param mixed $supplementaryData
     *
     * @return CheckoutSession
     */
    public function setSupplementaryData($supplementaryData)
    {
        $this->supplementaryData = $supplementaryData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSupplementaryData()
    {
        return $this->supplementaryData;
    }

    /**
     * @param mixed $reasonCode
     *
     * @return CheckoutSession
     */
    public function setReasonCode($reasonCode)
    {
        $this->reasonCode = $reasonCode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getReasonCode()
    {
        return $this->reasonCode;
    }

    /**
     * @param mixed $message
     *
     * @return CheckoutSession
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getChargePermissionType()
    {
        return $this->chargePermissionType;
    }

    /**
     * @param string $chargePermissionType
     *
     * @return CheckoutSession
     */
    public function setChargePermissionType($chargePermissionType)
    {
        $this->chargePermissionType = $chargePermissionType;

        return $this;
    }


}
