<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayPayModuleFrontController extends ModuleFrontController {
    private function preparePaymentLink(Cart $cart) {
        $handler = $this->getCheckoutRequestHandler();
        $result = $handler->createCheckout(
            $this->context->cart,
            $this->context->link->getModuleLink($this->module->name, 'success'),
            $this->context->link->getModuleLink($this->module->name, 'error')
        );
        $obj = json_decode($result, true);
        return $obj['checkout']['redirectionUrl'];
    }

    private static function getCheckoutRequestHandler() {
        return new CheckoutRequestHandler(
            Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY), 
            Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY), 
            Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY));
    }

    public function initContent() {
        parent::initContent();
        $link = $this->preparePaymentLink($this->context->cart);
        Tools::redirect($link);
    }
}