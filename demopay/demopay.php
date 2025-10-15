<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class DemoPay extends PaymentModule {
    public function __construct() {
        $this->name = 'demopay';
        $this->tab = 'demopay';
        $this->version = '1.0.0';
        $this->author = 'Martin Karry';
        $this->need_instance = 0;
        $this->is_configurable = 1;

        parent::__construct();

        $this->displayName = $this->l('DemoPay');
        $this->description = $this->l('DemoPay by Martin Karry');
    }

    public function hookPaymentOptions($params) {
        if (!$this->active) {
            return;
        }
        // create a PaymentOption of type Offline
        $offlineOption = new PaymentOption();
        $offlineOption->setModuleName($this->name);
        $offlineOption->setCallToActionText($this->l('DemoPay test'));
        $offlineOption->setAction($this->context->link->getModuleLink($this->name, 'validation', ['option' => 'offline'], true));
        $offlineOption->setAdditionalInformation($this->context->smarty->fetch('module:pay/views/templates/front/paymentOptions.tpl'));
        $offlineOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/logo.png'));
        
        return [$offlineOption];
    }

    public function install() {
        return parent::install() && $this->registerHook('paymentOptions');
    }

    public function uninstall() {
        return parent::uninstall();
    }
}