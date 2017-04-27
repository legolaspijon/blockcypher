<?php

class BlockcypherOrders extends ObjectModel
{
    public $id;
    public $id_order;
    public $amount;
    public $currency_amount;
    public $payment_currency;
    public $addr;
    public $txid;
    public $status;
    public $receided_confirmed;
    public $received_unconfirmed;
    public $created_at;
    public $last_update;

    public static $definition = array(
        'table' => 'blockcypher_orders',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'currency_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'payment_currency' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'addr' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'txid' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'receided_confirmed' => array('type' => self::TYPE_FLOAT),
            'received_unconfirmed' => array('type' => self::TYPE_FLOAT),
            'created_at' => array('type' => self::TYPE_DATE),
            'last_update' => array('type' => self::TYPE_DATE),
        )
    );


    /**
     * Get remaining sum
     *
     * @return integer
     */
    public function getRemainingPayment()
    {
        return (int) ($this->amount - ($this->receided_confirmed + $this->received_unconfirmed));
    }

    /**
     * Get blockcypher order by order id
     *
     * @param integer $order_id
     * @return self|false
     */
    static public function getBlockcypherOrderByOrderId($order_id)
    {
        $order = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."blockcypher_orders WHERE id_order=".$order_id);

        if(!$order){
            return false;
        }

        $orderObj = new self();
        foreach ($order as $attribute => $value) {
            if(property_exists($orderObj, $attribute)){
                $orderObj->$attribute = $value;
            }
        }

        return $orderObj;
    }

}