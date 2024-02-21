<?php

namespace AmazonPayApiSdkExtension\Struct;

class Frequency extends StructBase
{

    const FREQUENCY_UNIT_YEAR = 'Year';
    const FREQUENCY_UNIT_MONTH = 'Month';
    const FREQUENCY_UNIT_WEEK = 'Week';
    const FREQUENCY_UNIT_DAY = 'Day';
    const FREQUENCY_UNIT_VARIABLE = 'Variable';
    /**
     * @var string
     */
    protected $unit;
    /**
     * @var string
     */
    protected $value;

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return Frequency
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return Frequency
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


}
