<?php

class BlockcypherPaymentModuleFrontController extends ModuleFrontController
{
    public function initContent() {
        parent::initContent();

        // cheking payment

        $this->setTemplate('module:blockcypher/views/templates/front/payment_execution.tpl');
    }

    /**
     * Register JS for ajax request
     */
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