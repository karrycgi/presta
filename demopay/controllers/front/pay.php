<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayPayModuleFrontController extends ModuleFrontController
{
    private function preparePaymentLink(Cart $cart, string $paymentMethode)
    {
        $handler = $this->getCheckoutRequestHandler();
        $result = $handler->createCheckout(
            $this->context->cart,
            $this->context->link->getModuleLink($this->module->name, 'webhook'),
            $this->context->link->getModuleLink($this->module->name, 'success'),
            $this->context->link->getModuleLink($this->module->name, 'error'),
            $paymentMethode
        );
        $obj = json_decode($result, true);

        return [
            "link" => $obj['checkout']['redirectionUrl'],
            "transaction_id" => $obj['checkout']['checkoutId']
        ];
    }

    private static function getCheckoutRequestHandler()
    {
        return new CheckoutRequestHandler(
            Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY),
            Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY),
            Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY)
        );
    }

    public function initContent()
    {
        try {
            parent::initContent();
            $option = Tools::getValue('option');
            DB::getInstance()->delete(DemoPay::DEMO_PAY_NAME . '_transactions', 'cart_id = ' . (int) $this->context->cart->id);
            $link = $this->preparePaymentLink($this->context->cart, $option);
            DB::getInstance()->insert(
                DemoPay::DEMO_PAY_NAME . '_transactions',
                [
                    'cart_id' => (int) $this->context->cart->id,
                    'transaction_id' => pSQL($link['transaction_id'])
                ]
            );
            Tools::redirect($link['link']);
        } catch(Exception $e) {
            PrestaShopLogger::addLog($e->getMessage(), 3);
            Tools::redirect($this->context->link->getPageLink('cart'));
        }
    }
}