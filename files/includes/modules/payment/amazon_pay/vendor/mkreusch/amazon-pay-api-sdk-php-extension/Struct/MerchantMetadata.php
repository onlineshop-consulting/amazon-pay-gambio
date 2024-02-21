<?php

namespace AmazonPayApiSdkExtension\Struct;

class MerchantMetadata extends StructBase
{

    /**
     * @var string
     */
    protected $merchantReferenceId;

    /**
     * @var string
     */
    protected $merchantStoreName;

    /**
     * @var string
     */
    protected $noteToBuyer;

    /**
     * @var string
     */
    protected $customInformation;

    /**
     * @return string
     */
    public function getMerchantReferenceId()
    {
        return $this->merchantReferenceId;
    }

    /**
     * @param string $merchantReferenceId
     *
     * @return MerchantMetadata
     */
    public function setMerchantReferenceId($merchantReferenceId)
    {
        $this->merchantReferenceId = $merchantReferenceId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantStoreName()
    {
        return strlen($this->merchantStoreName)>50?substr($this->merchantStoreName, 0, 47).'...':$this->merchantStoreName;
    }

    /**
     * @param string $merchantStoreName
     *
     * @return MerchantMetadata
     */
    public function setMerchantStoreName($merchantStoreName)
    {
        $this->merchantStoreName = $merchantStoreName;

        return $this;
    }

    /**
     * @return string
     */
    public function getNoteToBuyer()
    {
        return $this->noteToBuyer;
    }

    /**
     * @param string $noteToBuyer
     *
     * @return MerchantMetadata
     */
    public function setNoteToBuyer($noteToBuyer)
    {
        $this->noteToBuyer = $noteToBuyer;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomInformation()
    {
        return $this->customInformation;
    }

    /**
     * @param string $customInformation
     *
     * @return MerchantMetadata
     */
    public function setCustomInformation($customInformation)
    {
        $this->customInformation = $customInformation;

        return $this;
    }

}
