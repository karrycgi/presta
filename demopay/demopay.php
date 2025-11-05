<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

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

        $this->displayName = $this->trans('DemoPay', [], 'Modules.Demopay.Admin');
        $this->description = $this->trans('DemoPay by Martin Karry', [], 'Modules.Demopay.Admin');
    }

    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Demopay.Admin'),
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans('Mode', [], 'Modules.Demopay.Admin'),
                        'desc' => $this->trans('Sandbox is for testing purposes.', [], 'Modules.Demopay.Admin'),
                        'name' => DemoPay::DEMO_PAY_SANDBOX_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Sandbox', [], 'Modules.Demopay.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Production', [], 'Modules.Demopay.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Store ID', [], 'Modules.Demopay.Admin'),
                        'name' => DemoPay::DEMO_PAY_STORE_ID_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('API key', [], 'Modules.Demopay.Admin'),
                        'name' => DemoPay::DEMO_PAY_API_KEY_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('API secret', [], 'Modules.Demopay.Admin'),
                        'name' => DemoPay::DEMO_PAY_SECRET_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Gateway name', [], 'Modules.Demopay.Admin'),
                        'name' => $this->trans(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY),
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Gateway description', [], 'Modules.Demopay.Admin'),
                        'name' => $this->trans(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY),
                        'size' => 20,
                        'required' => true
                    ]
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Demopay.Admin'),
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

            $this->displayError($this->trans('Invalid Configuration value', [], 'Modules.Demopay.Admin'));

            if (empty($storeId) || empty($apiKey) || empty($secret) || empty($sandbox)) {
                $output = $this->displayError($this->trans('Invalid Configuration value', [], 'Modules.Demopay.Admin'));
            } else {
                Configuration::updateValue(DemoPay::DEMO_PAY_STORE_ID_KEY, trim($storeId));
                Configuration::updateValue(DemoPay::DEMO_PAY_API_KEY_KEY, trim($apiKey));
                Configuration::updateValue(DemoPay::DEMO_PAY_SECRET_KEY, trim($secret));
                Configuration::updateValue(DemoPay::DEMO_PAY_SANDBOX_KEY, $sandbox);
                Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY, trim($gatewayName));
                Configuration::updateValue(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY, trim($gatewayDescription));

                $output = $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Demopay.Admin'));

                if (!$this->validateCredentials($sandbox, $storeId, $apiKey, $secret)) {
                    $output .= $this->displayError($this->trans('Provided credentials are not valid', [], 'Modules.Demopay.Admin'));
                }
            }
        }

        return $output . $this->displayForm();
    }

    private function validateCredentials(string $sandbox, string $storeId, string $apiKey, string $secret): bool
    {
        return ValidateRequest::getInstance()->validateCredentials();
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }
        // create a PaymentOption of type Offline
        $genericOption = new PaymentOption();
        $genericOption->setModuleName($this->name);
        $genericOption->setCallToActionText($this->trans('DemoPay - %name% (%decription%)',[ 
            '%name%' => Configuration::get(DemoPay::DEMO_PAY_GATEWAY_NAME_KEY) , 
            '%decription%' => Configuration::get(DemoPay::DEMO_PAY_GATEWAY_DESCRIPTION_KEY)
        ], 'Modules.Demopay.Front'));
        $genericOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "generic"]));
        $genericOption->setAdditionalInformation($this->additionalInformationGeneric());
        $genericOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-gateway-generic.svg'));

        $debitOption = new PaymentOption();
        $debitOption->setModuleName($this->name);
        $debitOption->setCallToActionText($this->trans('DemoPay - Credit / Debit', [], 'Modules.Demopay.Front'));
        $debitOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "cards"]));
        $debitOption->setAdditionalInformation($this->additionalInformation());
        $debitOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-credit-card.svg'));

        $appleOption = new PaymentOption();
        $appleOption->setModuleName($this->name);
        $appleOption->setCallToActionText($this->trans('DemoPay - Apple Pay', [], 'Modules.Demopay.Front'));
        $appleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "applepay"]));
        $appleOption->setAdditionalInformation($this->additionalInformation());
        $appleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-apple-pay.svg'));

        $googleOption = new PaymentOption();
        $googleOption->setModuleName($this->name);
        $googleOption->setCallToActionText($this->trans('DemoPay - Google Pay', [], 'Modules.Demopay.Front'));
        $googleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "googlepay"]));
        $googleOption->setAdditionalInformation($this->additionalInformation());
        $googleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-google-pay.svg'));

        $bizumOption = new PaymentOption();
        $bizumOption->setModuleName($this->name);
        $bizumOption->setCallToActionText($this->trans('DemoPay - Bizum', [], 'Modules.Demopay.Front'));
        $bizumOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "bizum"]));
        $bizumOption->setAdditionalInformation($this->additionalInformation());
        $bizumOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-bizum-pay.png'));

        return [$genericOption, $debitOption, $appleOption, $googleOption, $bizumOption];
    }

    private function additionalInformation()
    {
        return '<p>' . $this->trans('You will be redirected to an external checkout page.', [], 'Modules.Demopay.Front') . '</p>';
    }

    private function additionalInformationGeneric()
    {
        return '<p>' . $this->trans('You will be redirected to an external checkout page where you will be able to select the other payment methods.', [], 'Modules.Demopay.Front') . '</p>';
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function createTable()
    {
        DB::getInstance()->execute('CREATE TABLE ' . _DB_PREFIX_ . DemoPay::DEMO_PAY_NAME . '_transactions (cart_id INT(11) NOT NULL, transaction_id VARCHAR(255) NOT NULL) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
        return true;
    }

    public function dropTable()
    {
        DB::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . DemoPay::DEMO_PAY_NAME . '_transactions;');
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