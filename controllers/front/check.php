<?php


class blockcypherCheckModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
        parent::postProcess();

        $address = strval($_REQUEST['address']);
        $redirect = false;

        if (!isset($address)) {
            echo json_encode(array(
                'received' => sprintf('%.8f', 0),
                'unconfirmed' => sprintf('%.8f', 0),
                'redirect' => false,
            ));
            die();
        }

        // get order by address
        $order = BlockcypherOrders::getBlockcypherOrderByColumnName($address, 'addr');

        if($order->isReceived() || !$order->isExpired()){
            exit('order received redirect now to order-confirmation');
        }

        echo json_encode(array(
            'received' => $order->received_confirmed > 0 ? $order->received_confirmed : sprintf('%.8f', 0),
            'unconfirmed' => $order->received_unconfirmed > 0 ? $order->received_unconfirmed : sprintf('%.8f', 0),
            'redirect' => $redirect,
        ));
        die();
    }

}