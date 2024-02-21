<?php

namespace AmazonPayApiSdkExtension\Struct;

class AddressRestrictions extends StructBase
{
    /**
     * @var string
     * @comment Allowed|NotAllowed
     */
    protected $type;
    /**
     * @var array
     */
    protected $restrictions;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return AddressRestrictions
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param array $restrictions
     *
     * @return AddressRestrictions
     */
    public function setRestrictions($restrictions)
    {
        $this->restrictions = $restrictions;

        return $this;
    }

}
