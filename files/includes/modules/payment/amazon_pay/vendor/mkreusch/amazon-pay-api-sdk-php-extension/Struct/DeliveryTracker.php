<?php

namespace AmazonPayApiSdkExtension\Struct;

class DeliveryTracker extends StructBase
{
    /**
     * @var string
     */
    protected $chargePermissionId;

    /**
     * @var DeliveryDetail[]
     */
    protected $deliveryDetails = [];

    /**
     * @return string
     */
    public function getChargePermissionId()
    {
        return $this->chargePermissionId;
    }

    /**
     * @param string $chargePermissionId
     * @return DeliveryTracker
     */
    public function setChargePermissionId($chargePermissionId)
    {
        $this->chargePermissionId = $chargePermissionId;
        return $this;
    }

    /**
     * @return DeliveryDetail[]
     */
    public function getDeliveryDetails()
    {
        return $this->deliveryDetails;
    }

    /**
     * @param DeliveryDetail[] $deliveryDetails
     * @return DeliveryTracker
     */
    public function setDeliveryDetails($deliveryDetails)
    {
        $this->deliveryDetails = $deliveryDetails;
        return $this;
    }

    /**
     * @param DeliveryDetail $deliveryDetail
     * @return DeliveryTracker
     */
    public function addDeliveryDetail($deliveryDetail)
    {
        $this->deliveryDetails[] = $deliveryDetail;
        return $this;
    }


}
