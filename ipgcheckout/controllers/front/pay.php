<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class IPGCheckoutPayModuleFrontController extends ModuleFrontController
{
    private function preparePaymentLink(Cart $cart, string $paymentMethode)
    {
        $handler = $this->getCheckoutRequestHandler();
        $result = $handler->createCheckout(
            $this->context->cart,
            $this->context->link->getModuleLink($this->module->name, 'webhook'),
            $this->context->link->getModuleLink($this->module->name, 'success'),
            $this->context->link->getModuleLink($this->module->name, 'payError'),
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
            Configuration::get(IPGCheckout:: STORE_ID_KEY),
            Configuration::get(IPGCheckout:: API_KEY_KEY),
            Configuration::get(IPGCheckout:: SECRET_KEY)
        );
    }

    public function initContent()
    {
        try {
            parent::initContent();
            $option = Tools::getValue('option');
            DB::getInstance()->delete(IPGCheckout:: NAME . '_transactions', 'cart_id = ' . (int) $this->context->cart->id);
            $link = $this->preparePaymentLink($this->context->cart, $option);
            DB::getInstance()->insert(
                IPGCheckout:: NAME . '_transactions',
                [
                    'cart_id' => (int) $this->context->cart->id,
                    'transaction_id' => pSQL($link['transaction_id'])
                ]
            );
            Tools::redirect($link['link']);
        } catch(Exception $e) {
            PrestaShopLogger::addLog($e->getMessage(), 3, 0, IPGCheckout:: NAME);
            Tools::redirect($this->context->link->getModuleLink(IPGCheckout:: NAME, 'payError'));
        }
    }
}