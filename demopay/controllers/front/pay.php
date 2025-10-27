<?php

class DemoPayPayModuleFrontController extends ModuleFrontController {
    private function preparePaymentLink($type, $total) {
        return "http://www.heise.de";
    }
    public function initContent() {
        parent::initContent();
        $type = $_GET["type"];
        //dump($this);
        //$this->setTemplate("module:demopay/views/templates/front/test.tpl");
        Tools::redirect($this->preparePaymentLink($type, 100));
    }
}