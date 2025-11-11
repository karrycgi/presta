<?php

use GuzzleHttp\Exception\ClientException;
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();
    }
    public function initContent()
    {
        try {

            parent::initContent();
            $checkoutId = Tools::getValue('id');
            $transaction_id = CheckoutRequestHandler::getInstance()->getTransactionId($checkoutId);
            $response = RefundRequestHandler::getInstance()->request(new Order(6), $transaction_id, 1);
            dump(json_decode($response)); die;
        } catch(ClientException $e) {
            dump($e->getMessage());
        }
    }
}