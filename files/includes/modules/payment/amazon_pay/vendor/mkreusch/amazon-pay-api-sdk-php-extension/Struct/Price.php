<?php

namespace AmazonPayApiSdkExtension\Struct;

class Price extends StructBase
{
    /**
     * @var string
     */
    protected $amount;
    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string|float $amount
     *
     * @return Price
     */
    public function setAmount($amount)
    {
        $this->amount = number_format(round($amount, 2), 2, '.', '');

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     *
     * @return Price
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

}
