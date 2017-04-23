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
        $module->createPayment($cart->id, Configuration::get('BLOCKCYPHER_PAYMENT_WAIT'), $total, $this->module->displayName, NULL, array(), (int)$currency->id, false, $customer->secure_key);

        Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
    }
}