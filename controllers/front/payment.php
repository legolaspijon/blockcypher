<?php

if (!defined('_PS_VERSION_')) {
    exit();
}

class BlockcypherPaymentModuleFrontController extends ModuleFrontController
{

    public function initContent() {
        parent::initContent();

        $order_id = (int) Tools::getValue('order_id');
        $blockcypherOrder = BlockcypherOrders::getBlockcypherOrderByColumnName($order_id, 'id_order');

        if(!$blockcypherOrder){
            Tools::redirect('index.php');
        }

        $order_statuses = Configuration::getMultiple(['BLOCKCYPHER_PAYMENT_RECEIVED', 'BLOCKCYPHER_PAYMENT_EXPIRED', 'BLOCKCYPHER_PAYMENT_WAIT']);

        if($blockcypherOrder->status == $order_statuses['BLOCKCYPHER_PAYMENT_RECEIVED']){

            $order = new Order($order_id);
            $history = new OrderHistory();
            $history->id_order = (int)$order->id;
            $history->changeIdOrderState(Configuration::get('BLOCKCYPHER_PAYMENT_RECEIVED'), (int)($order->id));

            $cart = $this->context->cart;
            $customer = $this->context->customer;

            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
            die();
        }

        $this->context->smarty->assign([
            'status' => $blockcypherOrder->status,
            'statuses' => $order_statuses,
            'order_total' => $blockcypherOrder->crypto_amount,
            'payment_address' => $blockcypherOrder->addr,
            'amount_receive' => $blockcypherOrder->received_confirmed,
            'amount_unconfirmed' => $blockcypherOrder->received_unconfirmed,
            'timeLeft' => $blockcypherOrder->timeLeft()
        ]);

        $this->setTemplate('module:blockcypher/views/templates/front/payment.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerJavascript('qrcode',"modules/".$this->module->name."/views/js/qrcode.min.js", array('priority' => 100));
        $this->registerJavascript('blockcypher-js',"modules/".$this->module->name."/views/js/blockcypher-js.js", array('priority' => 110));
        $this->registerStylesheet('blockcypher-css',"modules/".$this->module->name."/views/css/blockcypher-css.css");
    }
}