<?php

class DemoPayPayModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        //dump($this);
        //$this->setTemplate("module:demopay/views/templates/front/test.tpl");
        Tools::redirect('https://www.google.com');
    }
}