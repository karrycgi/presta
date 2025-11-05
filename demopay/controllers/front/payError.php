<?php

class DemoPayPayErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            "title" => $this->trans("Error during payment", [], 'Modules.Demopay.Front'),
            "text" => $this->trans("An error in payment process. Please try later again.", [], 'Modules.Demopay.Front')
        ]);
        $this->setTemplate('module:demopay/views/templates/front/payError.tpl');
    }
}