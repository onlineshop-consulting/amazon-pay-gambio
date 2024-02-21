<?php

namespace AmazonPayApiSdkExtension\Struct;

class ProviderMetadata extends StructBase
{
    /**
     * @var string
     */
    protected $providerReferenceId;

    /**
     * @return string
     */
    public function getProviderReferenceId()
    {
        return $this->providerReferenceId;
    }

    /**
     * @param string $providerReferenceId
     *
     * @return ProviderMetadata
     */
    public function setProviderReferenceId($providerReferenceId)
    {
        $this->providerReferenceId = $providerReferenceId;

        return $this;
    }


}
