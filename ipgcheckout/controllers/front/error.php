<?php

class IPGCheckoutErrorModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        Tools::redirect('https://www.disney.com');
    }
}