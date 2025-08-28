<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class Pay extends PaymentModule {
    public function __construct() {
        $this->name = 'pay';
        $this->tab = 'pay';
        $this->version = '1.0.0';
        $this->author = 'Martin Karry';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Pay');
        $this->description = $this->l('Pay by Martin Karry');
    }

    public function hookPaymentOptions($params) {
        if (!$this->active) {
            return;
        }
        // create a PaymentOption of type Offline
        $offlineOption = new PaymentOption();
        $offlineOption->setModuleName($this->name);
        $offlineOption->setCallToActionText($this->l('Pay test'));
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