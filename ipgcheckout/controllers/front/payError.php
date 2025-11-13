<?php

class IPGCheckoutPayErrorModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign([
            "title" => $this->trans("Error during payment", [], 'Modules.Ipgcheckout.Front'),
            "text" => $this->trans("An error in payment process. Please try later again.", [], 'Modules.Ipgcheckout.Front')
        ]);
        $this->setTemplate('module:ipgcheckout/views/templates/front/payError.tpl');
    }
}