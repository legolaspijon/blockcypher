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

        $blockcypherOrder = BlockcypherOrders::getBlockcypherOrderByOrderId($order_id);

        $this->context->smarty->assign([
            'order_total' => $blockcypherOrder->coins,
            'amount_receive' => $blockcypherOrder->coins_payed,
            'payment_address' => $blockcypherOrder->addr,
            'amount_missing' => $blockcypherOrder->getRemainingPayment(),
            'timer' => $blockcypherOrder->getPassedTime(),
            'unconfirmed' => '0.00',
            'confirmed' => '0.00',
        ]);

        $this->setTemplate('module:blockcypher/views/templates/front/payment_execution.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerJavascript('qrcode',"modules/".$this->module->name."/views/js/qrcode.min.js", array('priority' => 100));
        $this->registerJavascript('blockcypher-js',"modules/".$this->module->name."/views/js/blockcypher-js.js", array('priority' => 110));
        $this->registerStylesheet('blockcypher-css',"modules/".$this->module->name."/views/css/blockcypher-css.css");
    }


}