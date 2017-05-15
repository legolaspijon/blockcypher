<?php

class blockcypherWebhookModuleFrontController extends ModuleFrontController
{
    // UPDATE BLOCKCYPHER_ORDER_TABLE
    public function postProcess() {
        parent::postProcess();
        $data = file_get_contents(__DIR__ .'/data.json');
        $data = json_decode($data);

        $order_id = 14;//$_REQUEST['order_id'];
        $order = BlockcypherOrders::getBlockcypherOrderByColumnName($order_id, 'id_order');




exit();
    }
}