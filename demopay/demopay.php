<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class DemoPay extends PaymentModule
{
    const DEMO_PAY_NAME = "demopay";
    public const DEMO_PAY_NAME_KEY = "DEMO_PAY_NAME";
    const DEMO_PAY_STORE_ID = "";
    public const DEMO_PAY_STORE_ID_KEY = "DEMO_PAY_STORE_ID";
    const DEMO_PAY_API_KEY = "";
    public const DEMO_PAY_API_KEY_KEY = "DEMO_PAY_API_KEY";
    const DEMO_PAY_SECRET = "";
    public const DEMO_PAY_SANDBOX_KEY = "DEMO_PAY_SANDBOX";
    const DEMO_PAY_SANDBOX = true;
    public const DEMO_PAY_SECRET_KEY = "DEMO_PAY_SECRET";

    const DEMO_PAY_GATEWAY_NAME_KEY = "DEMO_PAY_GATEWAY_NAME";
    public const DEMO_PAY_GATEWAY_NAME_DEFAULT = "Generic Payment";
    const DEMO_PAY_GATEWAY_DESCRIPTION_KEY = "DEMO_PAY_GATEWAY_DESCRIPTION";
    public const DEMO_PAY_GATEWAY_DESCRIPTION_DEFAULT = "Generic Payment";
    public function __construct()
    {
        $this->name = DemoPay::DEMO_PAY_NAME;
        $this->tab = DemoPay::DEMO_PAY_NAME;
        $this->version = '1.0.0';
        $this->author = 'Martin Karry';
        $this->need_instance = 1;
        $this->is_configurable = 1;

        parent::__construct();

        $this->displayName = $this->l('DemoPay');
        $this->description = $this->l('DemoPay by Martin Karry');
    }

    public function displayForm()
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings ' . DemoPay::DEMO_PAY_STORE_ID),
                ],
                'input' => [
                    [
                        'type' => 'select',                              
                        'label' => $this->l('Mode'),         
                        'desc' => $this->l('Sandbox is for testing purposes.'),  
                        'name' => DemoPay::DEMO_PAY_SANDBOX_KEY,                     
                        'required' => true,                              
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',       
                                    'name' => 'Sandbox'   
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => 'Production'
                                ],
                            ],
                            'id' => 'id_option',                        
                            'name' => 'name'                            
                        )
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Store ID'),
                        'name' => DemoPay::DEMO_PAY_STORE_ID_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('API key'),
                        'name' => DemoPay::DEMO_PAY_API_KEY_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('API secret'),
                        'name' => DemoPay::DEMO_PAY_SECRET_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Gateway name'),
                        'name' => DemoPay::DEMO_PAY_GATEWAY_NAME_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Gateway description'),
                        'name' => DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY,
                        'size' => 20,
                        'required' => true
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false, [], ['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value[DemoPay::DEMO_PAY_STORE_ID_KEY] = Tools::getValue(DemoPay::DEMO_PAY_STORE_ID_KEY, Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY));
        $helper->fields_value[DemoPay::DEMO_PAY_API_KEY_KEY] = Tools::getValue(DemoPay::DEMO_PAY_API_KEY_KEY, Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY));
        $helper->fields_value[DemoPay::DEMO_PAY_SECRET_KEY] = Tools::getValue(DemoPay::DEMO_PAY_SECRET_KEY, Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY));
        $helper->fields_value[DemoPay::DEMO_PAY_SANDBOX_KEY] = Tools::getValue(DemoPay::DEMO_PAY_SANDBOX_KEY, Configuration::get(DemoPay::DEMO_PAY_SANDBOX_KEY));
        $helper->fields_value[DemoPay::DEMO_PAY_GATEWAY_NAME_KEY] = Tools::getValue(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY, Configuration::get(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY));
        $helper->fields_value[DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY] = Tools::getValue(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY, Configuration::get(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY));

        return $helper->generateForm([$form]);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $storeId = (string) Tools::getValue(DemoPay::DEMO_PAY_STORE_ID_KEY);
            $apiKey = (string) Tools::getValue(DemoPay::DEMO_PAY_API_KEY_KEY);
            $secret = (string) Tools::getValue(DemoPay::DEMO_PAY_SECRET_KEY);
            $sandbox = (string) Tools::getValue(DemoPay::DEMO_PAY_SANDBOX_KEY);
            $gatewayName = (string) Tools::getValue(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY);
            $gatewayDescription = (string) Tools::getValue(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY);

            $this->displayError($this->l('Invalid Configuration value'));

            if (empty($storeId) || empty($apiKey) || empty($secret) || empty($sandbox)) {
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue(DemoPay::DEMO_PAY_STORE_ID_KEY, trim($storeId));
                Configuration::updateValue(DemoPay::DEMO_PAY_API_KEY_KEY, trim($apiKey));
                Configuration::updateValue(DemoPay::DEMO_PAY_SECRET_KEY, trim($secret));
                Configuration::updateValue(DemoPay::DEMO_PAY_SANDBOX_KEY, $sandbox);
                Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY, $gatewayName);
                Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY, $gatewayDescription);

                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->displayForm();
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        // create a PaymentOption of type Offline
        $genericOption = new PaymentOption();
        $genericOption->setModuleName($this->name);
        $genericOption->setCallToActionText($this->l('DemoPay - '.Configuration::get(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY).'('.Configuration::get(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY).')'));
        $genericOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'generic']));
        $genericOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $genericOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        $debitOption = new PaymentOption();
        $debitOption->setModuleName($this->name);
        $debitOption->setCallToActionText($this->l('DemoPay - Credit / Debit'));
        $debitOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'debit']));
        $debitOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $debitOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        $appleOption = new PaymentOption();
        $appleOption->setModuleName($this->name);
        $appleOption->setCallToActionText($this->l('DemoPay - Apple Pay'));
        $appleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'apple']));
        $appleOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $appleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        $googleOption = new PaymentOption();
        $googleOption->setModuleName($this->name);
        $googleOption->setCallToActionText($this->l('DemoPay - Google Pay'));
        $googleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'google']));
        $googleOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $googleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        $bizumOption = new PaymentOption();
        $bizumOption->setModuleName($this->name);
        $bizumOption->setCallToActionText($this->l('DemoPay - Bizum'));
        $bizumOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'bizum']));
        $bizumOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $bizumOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        return [$genericOption, $debitOption, $appleOption, $googleOption, $bizumOption];
    }

    public function createTable() {
        DB::getInstance()->execute('CREATE TABLE '._DB_PREFIX_.DemoPay::DEMO_PAY_NAME.'_transactions (cart_id INT(11) NOT NULL, transaction_id VARCHAR(255) NOT NULL) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
        return true;
    }

    public function dropTable() {
        DB::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.DemoPay::DEMO_PAY_NAME.'_transactions;');
        return true;
    }

    public function install()
    {
        return (
            parent::install()
            && $this->createTable()
            && $this->registerHook('paymentOptions')
            && Configuration::updateValue(DemoPay::DEMO_PAY_NAME_KEY, DemoPay::DEMO_PAY_NAME)
            && Configuration::updateValue(DemoPay::DEMO_PAY_STORE_ID_KEY, DemoPay::DEMO_PAY_STORE_ID)
            && Configuration::updateValue(DemoPay::DEMO_PAY_API_KEY_KEY, DemoPay::DEMO_PAY_API_KEY)
            && Configuration::updateValue(DemoPay::DEMO_PAY_SECRET_KEY, DemoPay::DEMO_PAY_SECRET)
            && Configuration::updateValue(DemoPay::DEMO_PAY_SANDBOX_KEY, DemoPay::DEMO_PAY_SANDBOX)
            && Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY, DemoPay::DEMO_PAY_GATEWAY_NAME_DEFAULT)
            && Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY, DemoPay::DEMO_PAY_GATEWAY_NAME_DEFAULT)
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && $this->dropTable()
            && $this->unregisterHook('paymentOptions')
            && Configuration::deleteByName(DemoPay::DEMO_PAY_NAME_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_STORE_ID_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_API_KEY_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_SECRET_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_SANDBOX_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY)
        );
    }
}