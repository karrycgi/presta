<?php

class DemoPayPayErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            "title" => $this->l("Error during payment"),
            "text" => $this->l("An error in payment process. Please try later again.")
        ]);

        $this->setTemplate('module:demopay/views/templates/front/payError.tpl');
    }
}