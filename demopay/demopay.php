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

        return $helper->generateForm([$form]);
    }

    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $storeId = (string) Tools::getValue(DemoPay::DEMO_PAY_STORE_ID_KEY);
            $apiKey = (string) Tools::getValue(DemoPay::DEMO_PAY_API_KEY_KEY);
            $secret = (string) Tools::getValue(DemoPay::DEMO_PAY_SECRET_KEY);
            $sandbox = (string) Tools::getValue(DemoPay::DEMO_PAY_SANDBOX_KEY);

            $this->displayError($this->l('Invalid Configuration value'));

            // check that the value is valid
            if (empty($storeId) || empty($apiKey) || empty($secret) || empty($sandbox)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue(DemoPay::DEMO_PAY_STORE_ID_KEY, $storeId);
                Configuration::updateValue(DemoPay::DEMO_PAY_API_KEY_KEY, $apiKey);
                Configuration::updateValue(DemoPay::DEMO_PAY_SECRET_KEY, $secret);
                Configuration::updateValue(DemoPay::DEMO_PAY_SANDBOX_KEY, $sandbox);

                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
        /*
        if(!$this->active) {
            return "<div>Inactive</div>";
        }
        return '<div>Hello World!!!(Const: '.DemoPay::DEMO_PAY_NAME_KEY.')(Const value: '.DemoPay::DEMO_PAY_NAME.')(Const key: '.Configuration::get(DemoPay::DEMO_PAY_NAME_KEY).')</div>'; // TODO Implement configuration form
        */
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        // create a PaymentOption of type Offline
        $offlineOption = new PaymentOption();
        $offlineOption->setModuleName($this->name);
        $offlineOption->setCallToActionText($this->l('DemoPay test'));
        $offlineOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => 'offline']));
        $offlineOption->setAdditionalInformation($this->context->smarty->fetch('module:demopay/views/templates/front/paymentOptions.tpl'));
        $offlineOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));

        return [$offlineOption];
    }

    public function install()
    {
        return (
            parent::install()
            && $this->registerHook('paymentOptions')
            && Configuration::updateValue(DemoPay::DEMO_PAY_NAME_KEY, DemoPay::DEMO_PAY_NAME)
            && Configuration::updateValue(DemoPay::DEMO_PAY_STORE_ID_KEY, DemoPay::DEMO_PAY_STORE_ID)
            && Configuration::updateValue(DemoPay::DEMO_PAY_API_KEY_KEY, DemoPay::DEMO_PAY_API_KEY)
            && Configuration::updateValue(DemoPay::DEMO_PAY_SECRET_KEY, DemoPay::DEMO_PAY_SECRET)
            && Configuration::updateValue(DemoPay::DEMO_PAY_SANDBOX_KEY, DemoPay::DEMO_PAY_SANDBOX)
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && $this->unregisterHook('paymentOptions')
            && Configuration::deleteByName(DemoPay::DEMO_PAY_NAME_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_STORE_ID_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_API_KEY_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_SECRET_KEY)
            && Configuration::deleteByName(DemoPay::DEMO_PAY_SANDBOX_KEY)
        );
    }
}