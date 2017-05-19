<?php


class blockcypherCheckModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
        parent::postProcess();

        $reload = false;

        /** @var BlockCypher $module */
        $module = $this->module;

        $address = strval($_REQUEST['address']);
        if (!isset($address)) {
            echo json_encode(array(
                'received' => sprintf('%.8f', 0),
                'unconfirmed' => sprintf('%.8f', 0),
                'reload' => false,
            ));
            die();
        }

        // get order by address
        $order = BlockcypherOrders::getBlockcypherOrderByColumnName($address, 'addr');
        if($order->timeLeft() <= 0 && $order->received_confirmed <= 0){
            $module->updateStatus($order, Configuration::get('BLOCKCYPHER_PAYMENT_EXPIRED'));
            $reload = true;
        }

        echo json_encode(array(
            'received' => $order->received_confirmed > 0 ? $order->received_confirmed : sprintf('%.8f', 0),
            'unconfirmed' => $order->received_unconfirmed > 0 ? $order->received_unconfirmed : sprintf('%.8f', 0),
            'reload' => $reload,
        ));
        die();
    }

}