<?php

namespace AmazonPayApiSdkExtension\Struct;

class DeliveryDetail extends StructBase
{
    /**
     * @var string
     */
    protected $carrierCode;

    /**
     * @var string
     */
    protected $trackingNumber;

    /**
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->carrierCode;
    }

    /**
     * @param string $carrierCode
     * @return DeliveryDetail
     */
    public function setCarrierCode($carrierCode)
    {
        $this->carrierCode = $carrierCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     * @return DeliveryDetail
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }


}
