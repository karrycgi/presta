<?php
class DemoPaySuccessModuleFrontController extends ModuleFrontController {
    public function initContent() {
        parent::initContent();
        Tools::redirect('https://www.whitescreen.online');
    }
}