<?php

use GuzzleHttp\Exception\ClientException;
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayWebhookModuleFrontController extends ModuleFrontController
{
    public $module;
    private $handler;

    public function postProcess()
    {
        parent::postProcess();
    }
    public function initContent()
    {
        parent::initContent();
        try {
            $cart_id = (int) Tools::getValue('id');

            $cart = new Cart((int) $cart_id);

            if (!$cart->orderExists()) {
                $transaction_id = DB::getInstance()->getRow('SELECT transaction_id FROM ' . _DB_PREFIX_ . DemoPay::DEMO_PAY_NAME . '_transactions WHERE cart_id = ' . (int) $this->context->cart->id)["transaction_id"];

                $status = json_decode(CheckoutRequestHandler::getInstance()->checkoutStatus($transaction_id), true);

                $amount = $status['approvedAmount']['total'];


                $this->module->validateOrder(
                    (int) $cart_id,
                    (int) Configuration::get("PS_OS_PAYMENT"),
                    (float) $amount,
                    DemoPay::PAYMENT_MATHOD_NAME,
                    null,
                    [
                        "transaction_id" => $transaction_id
                    ]
                );

                DB::getInstance()->delete(DemoPay::DEMO_PAY_NAME . '_transactions', 'cart_id = ' . (int) $cart_id);
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog($e->getMessage(), 3);
        }
        die;
    }
}