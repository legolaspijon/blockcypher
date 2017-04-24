<?php

if (!defined('_PS_VERSION_'))
{
    exit();
}

class BlockcypherPaymentModuleFrontController extends ModuleFrontController
{
    public function initContent() {
        parent::initContent();

        $order_id = (int) Tools::getValue('order_id');
        $blockcypherOrder = BlockcypherOrders::getBlockcypherOrderByOrderId($order_id);
        if(!$blockcypherOrder){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        }

        $this->context->smarty->assign([
            'order_total' => $blockcypherOrder->coins,
            'amount_receive' => $blockcypherOrder->coins_payed,
            'payment_address' => $blockcypherOrder->addr,
            'amount_missing' => $blockcypherOrder->getRemainingPayment()
        ]);

        $this->setTemplate('module:blockcypher/views/templates/front/payment_execution.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        if('blockcypher' == $this->module->name){
            $this->registerJavascript(
                'blockcypher-js',
                "modules/".$this->module->name."/views/js/blockcypher-js.js"
            );
        }
    }

}