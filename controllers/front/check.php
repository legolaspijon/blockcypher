<?php


class blockcypherCheckModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
        parent::postProcess();

        die('check payment');
    }
}