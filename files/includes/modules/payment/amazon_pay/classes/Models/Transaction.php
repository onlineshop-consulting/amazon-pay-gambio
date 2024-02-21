<?php
declare(strict_types=1);

namespace OncoAmazonPay\Models;

class Transaction
{
    const TABLE_NAME = 'amazon_pay_transactions';
    const TRANSACTION_TYPE_CHARGE = 'Charge';
    const TRANSACTION_TYPE_CHARGE_PERMISSION = 'ChargePermission';
    const TRANSACTION_TYPE_REFUND = 'Refund';

    public $id;
    public $parent_id;
    public $reference;
    public $merchant_id;
    public $mode;
    public $type;
    public $time;
    public $expiration;
    public $amount;
    public $charge_amount;
    public $captured_amount;
    public $refunded_amount;
    public $currency;
    public $status;
    public $last_change;
    public $last_update;
    public $order_id;
    public $customer_informed;
    public $admin_informed;

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
            if (property_exists($this, $fieldName)) {
                $this->{$fieldName} = $fieldValue;
            }
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
                $return[$property] = $this->{$property};
            }
        }
        return $return;
    }
}