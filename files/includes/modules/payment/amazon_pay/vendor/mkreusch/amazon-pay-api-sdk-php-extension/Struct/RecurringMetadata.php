<?php

namespace AmazonPayApiSdkExtension\Struct;

class RecurringMetadata extends StructBase
{
    /**
     * @var Price
     */
    protected $amount;

    /**
     * @var Frequency
     */
    protected $frequency;

    /**
     * @return Price
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Price $amount
     * @return RecurringMetadata
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Frequency
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * @param Frequency $frequency
     * @return RecurringMetadata
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }


}
