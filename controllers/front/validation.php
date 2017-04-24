<?php

class BlockcypherValidationModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'blockcypher')
            {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->module->getTranslator()->trans('This payment method is not available.', array(), 'Modules.WirePayment.Shop'));

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        /** @var $module BlockCypher */
        $module = $this->module;
        $id_order = $module->createPayment($cart->id, Configuration::get('BLOCKCYPHER_PAYMENT_WAIT'), $total, $this->module->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);

        Tools::redirect($this->context->link->getModuleLink($module->name, 'payment', array('order_id' => $id_order)));
    }
}