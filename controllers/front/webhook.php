<?php

class blockcypherWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
        parent::postProcess();

        $data = file_get_contents(__DIR__ .'/data.json');
        $data = json_decode($data);

        $total_received = 0;
        $address = '';
        $update = false;

        if(isset($data->address)) {
            $total_received = $data->total_sent;
            $address = $data->address;
        } elseif(isset($data->input_address)) {
            $address = $data->input_address;
            $total_received = $data->value;
        }

        if(empty($address) || empty($total_received))
            exit();

        $order = BlockcypherOrders::getBlockcypherOrderByColumnName($address, 'addr');
        $order->plus($total_received);
        $order->last_update = time();
        if($order->received_confirmed == 0 && $order->isExpired()) {
            $order->status = Configuration::get('BLOCKCYPHER_PAYMENT_EXPIRED');
            $update = true;
        } elseif ($order->received_confirmed >= $order->crypto_amount) {
            $order->status = Configuration::get('BLOCKCYPHER_PAYMENT_RECEIVED');
            $update = true;
        }

        if ($update)
            $order->update();

        exit();
    }
}