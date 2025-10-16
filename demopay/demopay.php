<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class DemoPay extends PaymentModule {
    const DEMO_PAY_NAME_KEY = "DEMO_PAY_NAME";
    const DEMO_PAY_NAME = "demopay";
    public function __construct() {
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

    public function getContent() {
        if(!$this->active) {
            return "<div>Inactive</div>";
        }
        return '<div>Hello World!!!(Const: '.DemoPay::DEMO_PAY_NAME_KEY.')(Const value: '.DemoPay::DEMO_PAY_NAME.')(Const key: '.Configuration::get(DemoPay::DEMO_PAY_NAME_KEY).')</div>'; // TODO Implement configuration form
    }

    public function hookPaymentOptions($params) {
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

    public function install() {
        return (
            parent::install() 
            && $this->registerHook('paymentOptions')
            && Configuration::updateValue(DemoPay::DEMO_PAY_NAME_KEY, DemoPay::DEMO_PAY_NAME)
        );
    }

    public function uninstall() {
        return (
            parent::uninstall()
            && $this->unregisterHook('paymentOptions')
            && Configuration::deleteByName(DemoPay::DEMO_PAY_NAME_KEY)
        );
    }
}