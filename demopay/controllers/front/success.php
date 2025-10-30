<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPaySuccessModuleFrontController extends ModuleFrontController
{
    public $module;
    private $handler;

    public function init()
    {
        parent::init();
        $this->handler = new CheckoutRequestHandler(
            Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY),
            Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY),
            Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY)
        );
    }
    public function initContent()
    {
        parent::initContent();

        $cart_id = (int) Tools::getValue('id');

        if ((int) $cart_id != (int) $this->context->cart->id) {
            dump($cart_id . '---' . $this->context->cart->id);
            dump("A problem occured. Ids are not matching");
            die;
        }

        $customer = new Customer($this->context->cart->id_customer);

        $transaction_id = DB::getInstance()->getRow('SELECT transaction_id FROM ' . _DB_PREFIX_ . DemoPay::DEMO_PAY_NAME . '_transactions WHERE cart_id = ' . (int) $this->context->cart->id)["transaction_id"];

        $status = json_decode($this->handler->checkoutStatus($transaction_id), true);

        $amount = $status['approvedAmount']['total'];

        // $amountCurrency = $status['approvedAmount']['currency'];

        $this->module->validateOrder(
            (int) $cart_id,
            (int) Configuration::get("PS_OS_PAYMENT"),
            (float) $amount,
            "DemoPay Test!!!",
            null,
            [
                "transaction_id" => $transaction_id
            ]
        );

        try {
            DB::getInstance()->delete(DemoPay::DEMO_PAY_NAME . '_transactions', 'cart_id = ' . (int) $cart_id);
        } catch (Exception $e) {
            dump($e->getMessage()); die;
        }

        Tools::redirect($this->context->link->getPageLink(
            'order-confirmation',
            false,
            (int) $this->context->language->id,
            [
                'id_cart' => (int) $cart_id,
                'id_module' => (int) $this->module->id,
                'id_order' => (int) $this->module->currentOrder,
                'key' => $customer->secure_key,
            ]
        ));
    }
}