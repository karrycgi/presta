<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        parent::postProcess();
    }
    public function initContent()
    {
        parent::initContent();
        $id = Tools::getValue('id');
        $status = CheckoutRequestHandler::getInstance()->checkoutStatus($id);
        dump($status); die;
    }
}