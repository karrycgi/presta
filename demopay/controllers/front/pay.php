<?php

class DemoPayPayFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        Tools::redirect('https://www.google.com');
    }
}