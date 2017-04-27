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
//        $data = $this->_getRawData();
//        $data = [
//
//        ];
    }

    private function _getRawData()
    {
        $raw_data = file_get_contents('php://input');
        return json_encode($raw_data);
    }

}