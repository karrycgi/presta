<?php

class DemoPayPayModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        Tools::redirect('https://www.google.com');
    }
}