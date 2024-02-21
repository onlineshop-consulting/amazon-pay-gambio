<?php

namespace AmazonPayApiSdkExtension\Struct;

class DeliverySpecifications extends StructBase
{
    const RESTRICT_PACKSTATIONS = 'RestrictPackstations';
    const RESTRICT_PO_BOXES = 'RestrictPOBoxes';

    /**
     * @var array
     */
    protected $specialRestrictions = [];
    /**
     * @var AddressRestrictions
     */
    protected $addressRestrictions;

    /**
     * @return array
     */
    public function getSpecialRestrictions()
    {
        return $this->specialRestrictions;
    }

    /**
     * @param array $specialRestrictions
     *
     * @return DeliverySpecifications
     */
    public function setSpecialRestrictions($specialRestrictions)
    {
        $this->specialRestrictions = $specialRestrictions;

        return $this;
    }

    /**
     * @param string $specialRestriction
     *
     * @return DeliverySpecifications
     */
    public function addSpecialRestriction($specialRestriction)
    {
        $this->specialRestrictions[] = $specialRestriction;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\AddressRestrictions
     */
    public function getAddressRestrictions()
    {
        return $this->addressRestrictions;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\AddressRestrictions $addressRestrictions
     *
     * @return DeliverySpecifications
     */
    public function setAddressRestrictions($addressRestrictions)
    {
        $this->addressRestrictions = $addressRestrictions;

        return $this;
    }


}
