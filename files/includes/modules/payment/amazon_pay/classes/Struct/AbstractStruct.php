<?php
declare(strict_types=1);

namespace OncoAmazonPay\Struct;

abstract class AbstractStruct
{
    public function __construct($dataArray = null)
    {
        if (is_array($dataArray)) {
            $this->setFromArray($dataArray);
        }
        return $this;
    }

    public function setFromArray($dataArray)
    {
        foreach ($dataArray as $fieldName => $fieldValue) {
            if (!is_string($fieldName)) {
                continue;
            }
            if (substr($fieldName, 0, 2) === 'is') {
                if ($fieldValue === 'true') {
                    $fieldValue = true;
                } elseif ($fieldValue === 'false') {
                    $fieldValue = false;
                }
            }
            $methodName = 'set' . ucfirst($fieldName);
            if (!method_exists($this, $methodName)) {
                continue;
            }
            if (is_array($fieldValue)) {
                $className = __NAMESPACE__ . '\\' . ucfirst($fieldName);
                if (class_exists($className)) {
                    $fieldValue = new $className($fieldValue);
                }
            }
            $this->{$methodName}($fieldValue);
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $return = [];
        foreach (array_keys(get_object_vars($this)) as $property) {
            if (isset($this->{$property})) {
                if (is_object($this->{$property}) && method_exists($this->{$property}, 'toArray')) {
                    $value = $this->{$property}->toArray();
                } else {
                    $value = $this->{$property};
                }
                $return[$property] = $value;
            }
        }
        return $return;
    }
}
