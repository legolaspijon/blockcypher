<?php

class BlockCypherCallbackModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->_webHook();
    }

    protected function _webhook()
    {
        $raw_body = file_get_contents('php://input');

        //...
    }

}