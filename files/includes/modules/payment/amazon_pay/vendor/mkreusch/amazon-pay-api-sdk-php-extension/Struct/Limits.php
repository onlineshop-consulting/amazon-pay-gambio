<?php

namespace AmazonPayApiSdkExtension\Struct;

class Limits extends StructBase {

    /**
     * @var \AmazonPayApiSdkExtension\Struct\AmountLimit
     */
    protected $amountLimit;

    /**
     * @var \AmazonPayApiSdkExtension\Struct\AmountBalance
     */
    protected $amountBalance;

    /**
     * @return \AmazonPayApiSdkExtension\Struct\AmountLimit
     */
    public function getAmountLimit()
    {
        return $this->amountLimit;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\AmountLimit $amountLimit
     *
     * @return Limits
     */
    public function setAmountLimit($amountLimit)
    {
        $this->amountLimit = $amountLimit;

        return $this;
    }

    /**
     * @return \AmazonPayApiSdkExtension\Struct\AmountBalance
     */
    public function getAmountBalance()
    {
        return $this->amountBalance;
    }

    /**
     * @param \AmazonPayApiSdkExtension\Struct\AmountBalance $amountBalance
     *
     * @return Limits
     */
    public function setAmountBalance($amountBalance)
    {
        $this->amountBalance = $amountBalance;

        return $this;
    }


}