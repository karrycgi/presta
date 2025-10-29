<?php
require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class DemoPayWebhookModuleFrontController extends ModuleFrontController
{
    public function postProcess() {
        parent::postProcess();
        dump('Hello PostProcess!!!');
    }
    public function initContent() {
        parent::initContent();
        dump("Hello World!!!");
    }
}