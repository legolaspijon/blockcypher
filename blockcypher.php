<?php

include dirname(__FILE__). '/vendor/autoload.php';
if (!defined('_PS_VERSION_'))
    exit;

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use BlockCypher\Rest\ApiContext;
use BlockCypher\Auth\SimpleTokenCredential;
use \BlockCypher\Client\PaymentForwardClient;

class BlockCypher extends PaymentModule
{
    const CHEIN_TYPE_TEST = 1;
    const CHEIN_TYPE_MAIN = 2;

    protected $_html = '';
    protected $_postErrors = array();

    protected $apiContext;
    protected $token;

    protected $coin = 'bcy';
    protected $chain = 'test';

    public $wallet_address;
    public $block_confirmations;


    public function __construct()
    {
        $this->name = 'blockcypher';
        $this->tab = 'payments_gateways';
        $this->version = '1.0';
        $this->author = 'Stableflow';
        $this->controllers = array('payment', 'validation');
        $this->currencies = true;
        $this->bootstrap = true;
        $this->currencies_mode = 'checkbox';

        $config = Configuration::getMultiple([
            'BLOCKCYPHER_MERCHANT_WALLET_ADDRESS',
            'BLOCKCYPHER_BLOCK_CONFIRMATIONS',
            'BLOCKCYPHER_TOKEN',
            'BLOCKCYPHER_CHAIN_TYPE',
        ]);

        if (!empty($config['BLOCKCYPHER_MERCHANT_WALLET_ADDRESS'])){
            $this->wallet_address = $config['BLOCKCYPHER_MERCHANT_WALLET_ADDRESS'];
        }
        if (!empty($config['BLOCKCYPHER_BLOCK_CONFIRMATIONS'])){
            $this->block_confirmations = $config['BLOCKCYPHER_BLOCK_CONFIRMATIONS'];
        }
        if (!empty($config['BLOCKCYPHER_TOKEN'])){
            $this->token = $config['BLOCKCYPHER_TOKEN'];
        }
        if(!empty($config['BLOCKCYPHER_CHAIN_TYPE'])){
            $this->_setChain($config['BLOCKCYPHER_CHAIN_TYPE']);
        }

        parent::__construct();

        $this->displayName = $this->l('BlockCypher');
        $this->description = $this->l('Accept payments for your products via BlockCypher dash transfer.');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');

        if (!isset($this->wallet_address) || !isset($this->block_confirmations) || !isset($this->token)) {
            $this->warning = $this->trans('Module BlockCypher must be configure before using', array(), 'Modules.WirePayment.Admin');
        }

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }

        $this->apiContext = ApiContext::create(
            $this->chain, $this->coin, 'v1',
            new SimpleTokenCredential($this->token),
            array('log.LogEnabled' => true, 'log.FileName' => 'BlockCypher.log', 'log.LogLevel' => 'INFO')
        );
    }

    public function install()
    {
        if(!parent::install()
            || !$this->registerHook('paymentReturn')
            || !$this->registerHook('paymentOptions')
            || !$this->createOrderStatuses()
            || !$this->installDB()
        ){
            return false;
        }

        return true;
    }

    protected function installDB()
    {
        return DB::getInstance()->execute("
            CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."blockcypher_orders (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                id_order INT UNSIGNED NOT NULL,
                timestamp INT(8) NOT NULL,
                addr varchar(100) NOT NULL,
                txid varchar(100) NOT NULL,
                status TINYINT(1) NOT NULL,
                value double(10,2) NOT NULL,
                coins decimal(10,8) NOT NULL,
                coins_payed decimal(10,8) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY order_table (addr))");
    }

    protected function createOrderStatuses()
    {
        $isOk = $this->createOrderStatus('PAYMENT_WAIT', "Wait Payment", array(
                'color' => '#FF8C00',
                'paid' => false,
            ))
            && $this->createOrderStatus('PAYMENT_WAIT_CONFIRMATIONS', "Wait 2 Confirmations", array(
                'color' => '#FF8C00',
                'paid' => false,
            ))
            && $this->createOrderStatus('PAYMENT_RECEIVED', "Received", array(
                'color' => '#FF8C00',
                'paid' => true,
            ));

        return $isOk;
    }

    protected function createOrderStatus($key, $title, $options = array())
    {
        $osKeyName = 'BLOCKCYPHER_'.$key;
        $orderState = new OrderState();
        $orderState->name = array_fill(0, 10, $title);
        $orderState->color = '#add8e6';
        $orderState->send_email = false;
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = false;
        $orderState->invoice = false;
        $orderState->module_name = $this->name;

        foreach ($options as $option => $value){
            if(property_exists($orderState, $option)){
                $orderState->$option = $value;
            }
        }

        if (!$orderState->add()) {
            return false;
        }

        Configuration::updateValue($osKeyName, (int) $orderState->id);

        return true;
    }

    public function uninstall()
    {
        if(!parent::uninstall()
            || !Configuration::deleteByName('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS')
            || !Configuration::deleteByName('BLOCKCYPHER_BLOCK_CONFIRMATIONS')
            || !Configuration::deleteByName('BLOCKCYPHER_TOKEN')
            || !$this->deleteOrderStatuses()
            || !$this->uninstallDB()
        ) {
            return false;
        }

        return true;
    }

    protected function uninstallDB()
    {
        return DB::getInstance()->execute("DROP TABLE "._DB_PREFIX_."blokcypher_orders");
    }

    protected function deleteOrderStatuses()
    {
        $isOk = $this->deleteOrderStatus('PAYMENT_WAIT')
            && $this->deleteOrderStatus('PAYMENT_WAIT_CONFIRMATIONS')
            && $this->deleteOrderStatus('PAYMENT_RECEIVED');

        return $isOk;
    }

    protected function deleteOrderStatus($key)
    {
        $osKeyName = 'BLOCKCYPHER_' . $key;
        $orderState = new OrderState();
        $orderState->id = (int) Configuration::get($osKeyName);
        if(!$orderState->delete()) {
            return false;
        }
        Configuration::deleteByName($osKeyName);

        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_validationProcess();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->displayConfirmation('Module success confugure.');
        $this->_renderForm();

        return $this->_html;
    }

    private function _renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('BlockCypher Payment Configurations', array(), 'Modules.WirePayment.Admin'),
                    'icon' => 'icon-envelope'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Token', array(), 'Modules.WirePayment.Admin'),
                        'name' => 'BLOCKCYPHER_TOKEN',
                        'required' => true,
                        'desc' => 'BlockCypher API token.'
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->trans('Chain', array(), 'Modules.WirePayment.Admin'),
                        'name' => 'BLOCKCYPHER_CHAIN_TYPE',
                        'required' => false,
                        'desc' => 'Chose what kind of blockchain will be use. Test only for bitcoin currency',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => self::CHEIN_TYPE_TEST,
                                    'name' => 'Test'
                                ),
                                array(
                                    'id' => self::CHEIN_TYPE_MAIN,
                                    'name' => 'Main'
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Merchant address in wallet', array(), 'Modules.WirePayment.Admin'),
                        'name' => 'BLOCKCYPHER_MERCHANT_WALLET_ADDRESS',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Block Confirmations', array(), 'Modules.WirePayment.Admin'),
                        'name' => 'BLOCKCYPHER_BLOCK_CONFIRMATIONS',
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? : 0;
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='
            .$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        $this->_html .= $helper->generateForm(array($fields_form));
    }

    protected function getConfigFieldsValues()
    {
        return array(
            'BLOCKCYPHER_MERCHANT_WALLET_ADDRESS' => Tools::getValue('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS', Configuration::get('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS')),
            'BLOCKCYPHER_BLOCK_CONFIRMATIONS' => Tools::getValue('BLOCKCYPHER_BLOCK_CONFIRMATIONS', Configuration::get('BLOCKCYPHER_BLOCK_CONFIRMATIONS')),
            'BLOCKCYPHER_TOKEN' => Tools::getValue('BLOCKCYPHER_TOKEN', Configuration::get('BLOCKCYPHER_TOKEN')),
            'BLOCKCYPHER_CHAIN_TYPE' => Tools::getValue('BLOCKCYPHER_CHAIN_TYPE', Configuration::get('BLOCKCYPHER_CHAIN_TYPE')),
        );
    }

    protected function _validationProcess()
    {
        if(Tools::getValue('btnSubmit')){
            if (!Tools::getValue('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS')
                || !Tools::getValue('BLOCKCYPHER_BLOCK_CONFIRMATIONS')
                || !Tools::getValue('BLOCKCYPHER_TOKEN')
            ) {
                $this->_postErrors[] = $this->trans('BlockCypher plugin must be configure', array(), 'Modules.WirePayment.Admin');
            }
        }
    }

    protected function _postProcess()
    {
        Configuration::updateValue('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS', Tools::getValue('BLOCKCYPHER_MERCHANT_WALLET_ADDRESS'));
        Configuration::updateValue('BLOCKCYPHER_BLOCK_CONFIRMATIONS', Tools::getValue('BLOCKCYPHER_BLOCK_CONFIRMATIONS'));
        Configuration::updateValue('BLOCKCYPHER_TOKEN', Tools::getValue('BLOCKCYPHER_TOKEN'));
        Configuration::updateValue('BLOCKCYPHER_CHAIN_TYPE', Tools::getValue('BLOCKCYPHER_CHAIN_TYPE'));
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $newOption = new PaymentOption();
        $newOption->setCallToActionText($this->trans('Pay by BlockCypher', array(), 'Modules.BlockCypher.Shop'))
            ->setLogo(_MODULE_DIR_ . 'blockcypher/views/img/dash.png')
            ->setAction($this->context->link->getModuleLink($this->name, 'validation'));

        return [$newOption];
    }

    public function hookPaymentReturn($params)
    {
//        return $this->fetch('module:blockcypher/views/templates/hook/payment_return.tpl');
    }

    protected function _setChain($type)
    {
        switch ($type) {
            case self::CHEIN_TYPE_TEST:
                $this->chain = 'test3';
                $this->coin = 'btc';
                break;
            case self::CHEIN_TYPE_MAIN:
                $this->chain = 'main';
                $this->coin = 'dash';

                break;
        }
    }

    public function createPayment($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, $currency_id, $dont_touch_amount, $secure_key)
    {
        if($this->validateOrder($id_cart, $id_order_state, $amount_paid, $payment_method, $message, $extra_vars, (int) $currency_id, $dont_touch_amount, $secure_key)){
            $paymentForwardClient = new PaymentForwardClient($this->apiContext);
            $options = array(
                'callback_url' => "http://requestb.in/1o5pndd1?order_id={$this->currentOrder}&customer_secure_key={$secure_key}"
            );

            $paymentForward = $paymentForwardClient->createForwardingAddress($this->wallet_address, $options);
            $timestamp = time();

            DB::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."blockcypher_orders` (`id_order`, `timestamp`, `addr`, `txid`, `status`, `value`, `coins`, `coins_payed`) 
                                        VALUES ({$this->currentOrder}, $timestamp, '{$paymentForward->getInputAddress()}', '', {$id_order_state}, '', {$amount_paid}, 0)");
            var_exit($paymentForward);
        }
    }
}