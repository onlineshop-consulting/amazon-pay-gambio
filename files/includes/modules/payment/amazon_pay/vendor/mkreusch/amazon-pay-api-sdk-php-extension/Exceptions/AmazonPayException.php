<?php
namespace AmazonPayApiSdkExtension\Exceptions;
class AmazonPayException extends \Exception{
    private $details;

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $details
     * @return AmazonPayException
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }
}
