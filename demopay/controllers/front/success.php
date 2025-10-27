<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPaySuccessModuleFrontController extends ModuleFrontController {
    public $module;
    private $handler;

    public function init() {
        parent::init();
        $this->handler = new CheckoutRequestHandler(
            Configuration::get('DEMOPAY_STORE_ID'),
            Configuration::get('DEMOPAY_API_KEY'),
            Configuration::get('DEMOPAY_SECRET')
        );  
    }
    public function initContent() {
        parent::initContent();

        $customer = new Customer($this->context->cart->id_customer);

        $this->module->validateOrder(
            (int) $this->context->cart->id,
            (int) Configuration::get("PS_OS_PAYMENT"),
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            "DemoPay Test!!!"
        );
        
        Tools::redirect($this->context->link->getPageLink(
            'order-confirmation',
            false,
            (int) $this->context->language->id,
            [
                'id_cart' => (int) $this->context->cart->id,
                'id_module' => (int) $this->module->id,
                'id_order' => (int) $this->module->currentOrder,
                'key' => $customer->secure_key,
            ]
        ));
    }
}