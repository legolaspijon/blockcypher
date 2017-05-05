<?php

if (!defined('_PS_VERSION_')) {
    exit();
}

class BlockcypherPaymentModuleFrontController extends ModuleFrontController
{

    public function initContent() {
        parent::initContent();

        $order_id = (int) Tools::getValue('order_id');
        if(!$order_id){
            Tools::redirect('index.php');
        }

        $blockcypherOrder = BlockcypherOrders::getBlockcypherOrderByColumnName($order_id, 'id_order');
        $timeLeft = (strtotime($blockcypherOrder->time_expired) - time());

        if($timeLeft <= 0) {
            exit('expired order');
        }

        $this->context->smarty->assign([
            'order_total' => $blockcypherOrder->crypto_amount,
            'payment_address' => $blockcypherOrder->addr,
            'amount_receive' => $blockcypherOrder->received_confirmed,
            'amount_unconfirmed' => $blockcypherOrder->received_unconfirmed,
            'timeLeft' => $timeLeft
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