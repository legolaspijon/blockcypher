<?php

class BlockcypherOrders extends ObjectModel
{
    public $id;
    public $id_order;
    public $crypto_amount;
    public $currency_amount;
    public $payment_currency;
    public $addr;
    public $txid;
    public $status;
    public $received_confirmed;
    public $received_unconfirmed;
    public $created_at;
    public $last_update;
    public $time_expired;

    public static $definition = array(
        'table' => 'blockcypher_orders',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'crypto_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'currency_amount' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
            'payment_currency' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'addr' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'txid' => array('type' => self::TYPE_STRING),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'received_confirmed' => array('type' => self::TYPE_FLOAT),
            'received_unconfirmed' => array('type' => self::TYPE_FLOAT),
            'created_at' => array('type' => self::TYPE_DATE),
            'last_update' => array('type' => self::TYPE_DATE),
            'time_expired' => array('type' => self::TYPE_DATE),
        )
    );


    /**
     * Get blockcypher order by column name
     *
     * @param mixed $column name
     * @param string $where value
     * @return BlockcypherOrders|false
     */
    static public function getBlockcypherOrderByColumnName($column, $where)
    {
        $orderData = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."blockcypher_orders WHERE {$where} = '{$column}'");
        if(!$orderData){
            return false;
        }
        $order = self::_initObj($orderData);

        return $order;
    }


    /**
     * @param array $data
     * @return BlockcypherOrders
     * */
    static protected function _initObj($data)
    {
        $order = new self();
        foreach ($data as $attribute => $value) {
            if(property_exists($order, $attribute)){
                $order->$attribute = $value;
            }
        }

        return $order;
    }

    /**
     * @return array
     * */
    public function getTransactions()
    {
        return unserialize($this->txid);
    }

    /**
     * @param string|array $transaction
     * */
    public function setTransactions($transaction)
    {
        $txs = $this->getTransactions();

        if(is_array($transaction)){
            $txs = array_merge($txs, $transaction);
        } else {
            array_push($txs, $transaction);
        }

        $this->txid = serialize($txs);
    }

    public function timeLeft()
    {
        return (strtotime($this->time_expired) - time());
    }

    public function paidLeft()
    {
        return bcsub($this->crypto_amount, $this->received_confirmed, 8);
    }

    public function isExpired()
    {
        return $this->timeLeft() <= 0;
    }

    public function plus($sum){
        echo (string)$sum; exit();
        $this->received_confirmed = (float)$this->received_confirmed;

        $this->received_confirmed = bcadd(0.00054000, $sum, 8);
        echo $this->received_confirmed;
        exit;
    }

}