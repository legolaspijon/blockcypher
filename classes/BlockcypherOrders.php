<?php

class BlockcypherOrders extends ObjectModel
{
    public $id;
    public $id_order;
    public $timestamp;
    public $addr;
    public $txid;
    public $status;
    public $value;
    public $coins;
    public $coins_payed;

    public static $definition = array(
        'table' => 'blockcypher_orders',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'timestamp' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'addr' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'txid' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'value' => array('type' => self::TYPE_FLOAT),
            'coins' => array('type' => self::TYPE_FLOAT),
            'coins_payed' => array('type' => self::TYPE_FLOAT),
        )
    );

    public function getPassedTime()
    {
        return time() - $this->timestamp;
    }

    public function getRemainingPayment()
    {
        return $this->coins - $this->coins_payed;
    }

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