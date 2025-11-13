<?php

use GuzzleHttp\Exception\ClientException;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/vendor/autoload.php';

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class IPGCheckout extends PaymentModule
{
    const NAME = "ipgcheckout";
    public const NAME_KEY = "IPG_CHECKOUT_NAME";
    const STORE_ID = "";
    public const STORE_ID_KEY = "IPG_CHECKOUT_STORE_ID";
    const API_KEY = "";
    public const API_KEY_KEY = "IPG_CHECKOUT_API_KEY";
    const SECRET = "";
    public const SANDBOX_KEY = "IPG_CHECKOUT_SANDBOX";
    const SANDBOX = true;
    public const SECRET_KEY = "IPG_CHECKOUT_SECRET";
    public const PAYMENT_MATHOD_NAME = "IPG Checkout";
    public const GENERIC_CHECKOUT_ACTIVE_KEY = "IPG_CHECKOUT_GENERIC_CHECKOUT_ACTIVE";
    public const DEBIT_CHECKOUT_ACTIVE_KEY = "IPG_CHECKOUT_DEBIT_CHECKOUT_ACTIVE";
    public const APPLE_CHECKOUT_ACTIVE_KEY = "IPG_CHECKOUT_APPLE_CHECKOUT_ACTIVE";
    public const GOOGLE_CHECKOUT_ACTIVE_KEY = "IPG_CHECKOUT_GOOGLE_CHECKOUT_ACTIVE";
    public const BIZUM_CHECKOUT_ACTIVE_KEY = "IPG_CHECKOUT_BIZUM_ACTIVE";
    public const TRUE = 'TRUE';
    public const FALSE = 'FALSE';
    public function __construct()
    {
        $this->name = IPGCheckout::NAME;
        $this->tab = IPGCheckout::NAME;
        $this->version = '1.0.0';
        $this->author = 'Fiserv';
        $this->need_instance = 1;
        $this->is_configurable = 1;

        parent::__construct();

        $this->displayName = $this->trans('IPG Checkout', [], 'Modules.Ipgcheckout.Admin');
        $this->description = $this->trans('IPG Checkout by Fiserv', [], 'Modules.Ipgcheckout.Admin');
    }

    public function displayForm()
    {
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Ipgcheckout.Admin'),
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans('Mode', [], 'Modules.Ipgcheckout.Admin'),
                        'desc' => $this->trans('Use Live (Production) Mode or Test (Sandbox) Mode', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::SANDBOX_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Sandbox', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Live', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Store ID', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::STORE_ID_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('API Key', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::API_KEY_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('API Secret', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::SECRET_KEY,
                        'size' => 20,
                        'required' => true
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Generic Checkout', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Active', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Deactivated', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Credit / Debit Card', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Active', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Deactivated', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Apple Pay', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Active', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Deactivated', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Google Pay', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Active', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Deactivated', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Bizum', [], 'Modules.Ipgcheckout.Admin'),
                        'name' => IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY,
                        'required' => true,
                        'options' => array(
                            'query' => [
                                [
                                    'id_option' => 'TRUE',
                                    'name' => $this->trans('Active', [], 'Modules.Ipgcheckout.Admin')
                                ],
                                [
                                    'id_option' => 'FALSE',
                                    'name' => $this->trans('Deactivated', [], 'Modules.Ipgcheckout.Admin')
                                ],
                            ],
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ]
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Ipgcheckout.Admin'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false, [], ['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value[IPGCheckout::STORE_ID_KEY] = Tools::getValue(IPGCheckout::STORE_ID_KEY, Configuration::get(IPGCheckout::STORE_ID_KEY));
        $helper->fields_value[IPGCheckout::API_KEY_KEY] = Tools::getValue(IPGCheckout::API_KEY_KEY, Configuration::get(IPGCheckout::API_KEY_KEY));
        $helper->fields_value[IPGCheckout::SECRET_KEY] = Tools::getValue(IPGCheckout::SECRET_KEY, Configuration::get(IPGCheckout::SECRET_KEY));
        $helper->fields_value[IPGCheckout::SANDBOX_KEY] = Tools::getValue(IPGCheckout::SANDBOX_KEY, Configuration::get(IPGCheckout::SANDBOX_KEY));

        $helper->fields_value[IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY] = Tools::getValue(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY, Configuration::get(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY));
        $helper->fields_value[IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY] = Tools::getValue(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY, Configuration::get(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY));
        $helper->fields_value[IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY] = Tools::getValue(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY, Configuration::get(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY));
        $helper->fields_value[IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY] = Tools::getValue(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY, Configuration::get(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY));
        $helper->fields_value[IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY] = Tools::getValue(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY, Configuration::get(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY));

        return $helper->generateForm([$form]);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            $storeId = (string) Tools::getValue(IPGCheckout::STORE_ID_KEY);
            $apiKey = (string) Tools::getValue(IPGCheckout::API_KEY_KEY);
            $secret = (string) Tools::getValue(IPGCheckout::SECRET_KEY);
            $sandbox = (string) Tools::getValue(IPGCheckout::SANDBOX_KEY);

            $genericCheckout = (string) Tools::getValue(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY);
            $debitCheckout = (string) Tools::getValue(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY);
            $appleCheckout = (string) Tools::getValue(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY);
            $googleCheckout = (string) Tools::getValue(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY);
            $bizumCheckout = (string) Tools::getValue(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY);

            $this->displayError($this->trans('Invalid Configuration value', [], 'Modules.Ipgcheckout.Admin'));

            if (empty($storeId) || empty($apiKey) || empty($secret) || empty($sandbox) || empty($genericCheckout) || empty($debitCheckout) || empty($appleCheckout) || empty($googleCheckout) || empty($bizumCheckout)) {
                $output = $this->displayError($this->trans('Invalid Configuration value', [], 'Modules.Ipgcheckout.Admin'));
            } else {
                Configuration::updateValue(IPGCheckout::STORE_ID_KEY, trim($storeId));
                Configuration::updateValue(IPGCheckout::API_KEY_KEY, trim($apiKey));
                Configuration::updateValue(IPGCheckout::SECRET_KEY, trim($secret));
                Configuration::updateValue(IPGCheckout::SANDBOX_KEY, $sandbox);

                Configuration::updateValue(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY, $genericCheckout);
                Configuration::updateValue(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY, $debitCheckout);
                Configuration::updateValue(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY, $appleCheckout);
                Configuration::updateValue(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY, $googleCheckout);
                Configuration::updateValue(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY, $bizumCheckout);

                $output = $this->displayConfirmation($this->trans('Settings updated', [], 'Modules.Ipgcheckout.Admin'));

                if (!$this->validateCredentials($sandbox, $storeId, $apiKey, $secret)) {
                    $output .= $this->displayError($this->trans('Payment method failed. Please check on settings page if API credentials are set correctly.', [], 'Modules.Ipgcheckout.Admin'));
                }
            }
        }

        return $output . '<div>
            <div style="margin-top: 5px; margin-bottom: 15px"><img src="' . Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/fiserv-logo.svg') . '" /></div>
            <p>' . $this->trans('Pay securely with Fiserv Checkout. Acquire API credetials on our portal.', [], 'Modules.Ipgcheckout.Admin') . '</p>
            <p>' . $this->trans('Visit', [], 'Modules.Ipgcheckout.Admin') . ' <a href="' . $this->trans('https://developer.fiserv.com', [], 'Modules.Ipgcheckout.Admin') . '">' . $this->trans('developer.fiserv.com', [], 'Modules.Ipgcheckout.Admin') . '</a><p/>
        </div>' . $this->displayForm();
    }

    private function validateCredentials(string $sandbox, string $storeId, string $apiKey, string $secret): bool
    {
        return ValidateRequest::getInstance()->validateCredentials();
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $paymentOptions = [];
        if (Configuration::get(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY) === IPGCheckout::TRUE) {
            // create a PaymentOption of type Offline
            $genericOption = new PaymentOption();
            $genericOption->setModuleName($this->name);
            $genericOption->setCallToActionText($this->trans('Generic Checkout', [], 'Modules.Ipgcheckout.Front'));
            $genericOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "generic"]));
            $genericOption->setAdditionalInformation($this->additionalInformationGeneric());
            //$genericOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-gateway-generic.svg'));
            $paymentOptions[] = $genericOption;
        }

        if (Configuration::get(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY) === IPGCheckout::TRUE) {
            $debitOption = new PaymentOption();
            $debitOption->setModuleName($this->name);
            $debitOption->setCallToActionText($this->trans('Credit / Debit Card', [], 'Modules.Ipgcheckout.Front'));
            $debitOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "cards"]));
            $debitOption->setAdditionalInformation($this->additionalInformation());
            //$debitOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-credit-card.svg'));
            $paymentOptions[] = $debitOption;
        }

        if (Configuration::get(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY) === IPGCheckout::TRUE) {
            $appleOption = new PaymentOption();
            $appleOption->setModuleName($this->name);
            $appleOption->setCallToActionText($this->trans('Apple Pay', [], 'Modules.Ipgcheckout.Front'));
            $appleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "applepay"]));
            $appleOption->setAdditionalInformation($this->additionalInformation());
            //$appleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-apple-pay.svg'));
            $paymentOptions[] = $appleOption;
        }

        if (Configuration::get(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY) === IPGCheckout::TRUE) {
            $googleOption = new PaymentOption();
            $googleOption->setModuleName($this->name);
            $googleOption->setCallToActionText($this->trans('Google Pay', [], 'Modules.Ipgcheckout.Front'));
            $googleOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "googlepay"]));
            $googleOption->setAdditionalInformation($this->additionalInformation());
            //$googleOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-google-pay.svg'));
            $paymentOptions[] = $googleOption;
        }

        if (Configuration::get(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY) === IPGCheckout::TRUE) {
            $bizumOption = new PaymentOption();
            $bizumOption->setModuleName($this->name);
            $bizumOption->setCallToActionText($this->trans('Bizum', [], 'Modules.Ipgcheckout.Front'));
            $bizumOption->setAction($this->context->link->getModuleLink($this->name, 'pay', ['option' => "bizum"]));
            $bizumOption->setAdditionalInformation($this->additionalInformation());
            //$bizumOption->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/option/fiserv-bizum-pay.png'));
            $paymentOptions[] = $bizumOption;
        }

        return $paymentOptions;
    }

    private function additionalInformation()
    {
        return '<p>' . $this->trans('You will be redirected to an external checkout page.', [], 'Modules.Ipgcheckout.Front') . '</p>';
    }

    private function additionalInformationGeneric()
    {
        return '<p>' . $this->trans('You will be redirected to an external checkout page where you will be able to select the other payment methods.', [], 'Modules.Ipgcheckout.Front') . '</p>';
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function createTable()
    {
        DB::getInstance()->execute('CREATE TABLE ' . _DB_PREFIX_ . IPGCheckout::NAME . '_transactions (cart_id INT(11) NOT NULL, transaction_id VARCHAR(255) NOT NULL) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
        return true;
    }

    public function dropTable()
    {
        DB::getInstance()->execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . IPGCheckout::NAME . '_transactions;');
        return true;
    }


    public function hookActionOrderSlipAdd($params)
    {
        $order = new Order($params['order']->id);
        $orderSlip = new OrderSlip($params['orderSlipCreated']->id);
        $amount = $orderSlip->amount + $orderSlip->shipping_cost_amount;

        if (count($order->getOrderPayments()) <= 0) {
            return;
        }

        if ($order->getOrderPayments()[0]) {
            $payment = new OrderPayment($order->getOrderPayments()[0]->id);
            if ($payment->payment_method !== IPGCheckout::PAYMENT_MATHOD_NAME) {
                return;
            }
        }

        try {
            $checkout_id = $order->getOrderPayments()[0]->transaction_id;
            $transaction_id = CheckoutRequestHandler::getInstance()->getTransactionId($checkout_id);
            $refundedResponse = json_decode(RefundRequestHandler::getInstance()->request($order, $transaction_id, $amount), true);
            $refundedAmount = $refundedResponse['transactionAmount']['total'];
            $refundedCurrency = $refundedResponse['transactionAmount']['currency'];
            RefundRequestHandler::writeMessage($order, "Refunded " . $refundedAmount . ' ' . $refundedCurrency . ' for Credit Slip Number' . $orderSlip->id);
        } catch (ClientException $e) {
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip total amount (calculated): ' . $amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip amount (provided by PrestaShop): ' . $orderSlip->amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip shipping cost amount (provided by PrestaShop): ' . $orderSlip->shipping_cost_amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog(var_export($e->getMessage(), true), 3, 0, IPGCheckout::NAME);
            RefundRequestHandler::writeMessage($order, "Refunding failed: " . $amount);
            $orderSlip->delete();
            $_SESSION['hook_order_slip_error'] = 'Refund failed';
        } catch (Exception $e) {
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip total amount (calculated): ' . $amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip amount (provided by PrestaShop): ' . $orderSlip->amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog('Order (' . $order->id . ') slip shipping cost amount (provided by PrestaShop): ' . $orderSlip->shipping_cost_amount, 1, 0, IPGCheckout::NAME, $order->id);
            PrestaShopLogger::addLog(var_export($e, true), 3, 0, IPGCheckout::NAME);
            RefundRequestHandler::writeMessage($order, "Refunding failed: " . $amount);
            $orderSlip->delete();
            $_SESSION['hook_order_slip_error'] = 'Refund failed';
        }
    }

    public function hookDisplayAdminOrderTop($params)
    {
        if (isset($_SESSION['hook_order_slip_error'])) {
            unset($_SESSION['hook_order_slip_error']);
            return $this->displayError($this->trans("Refund failed! Created Credit Slip was removed. Manual treatment mighty be necessary!", [], 'Modules.Ipgcheckout.Admin'));
        }
    }

    public function hookDisplayAdminOrderSideBottom($params)
    {
        $order = new Order($params['id_order']);
        if (count($order->getOrderPayments()) <= 0) {
            return;
        }
        if ($order->getOrderPayments()[0]) {
            $payment = new OrderPayment($order->getOrderPayments()[0]->id);
            if ($payment->payment_method !== IPGCheckout::PAYMENT_MATHOD_NAME) {
                return;
            }
        }
        $checkout_id = $order->getOrderPayments()[0]->transaction_id;
        $response = CheckoutRequestHandler::getInstance()->checkoutStatus($checkout_id);
        $status = json_decode($response, true);
        $this->context->smarty->assign([
            'checkout_id' => $checkout_id,
            'trace_id' => $status['ipgTransactionDetails']['apiTraceId'],
            'paymentMethodType' => $status['paymentMethodUsed']['paymentMethodType'],
            'status' => $status,
            'response' => $response
        ]);
        return $this->display(__FILE__, 'views/templates/admin/orderSide.tpl');
    }

    public function install()
    {
        return (
            parent::install()
            && $this->createTable()
            && $this->registerHook('paymentOptions')
            && $this->registerHook('actionOrderSlipAdd')
            && $this->registerHook('displayAdminOrderSideBottom')
            && $this->registerHook('displayAdminOrderTop')
            && Configuration::updateValue(IPGCheckout::NAME_KEY, IPGCheckout::NAME)
            && Configuration::updateValue(IPGCheckout::STORE_ID_KEY, IPGCheckout::STORE_ID)
            && Configuration::updateValue(IPGCheckout::API_KEY_KEY, IPGCheckout::API_KEY)
            && Configuration::updateValue(IPGCheckout::SECRET_KEY, IPGCheckout::SECRET)
            && Configuration::updateValue(IPGCheckout::SANDBOX_KEY, IPGCheckout::SANDBOX)
            && Configuration::updateValue(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY, IPGCheckout::TRUE)
            && Configuration::updateValue(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY, IPGCheckout::TRUE)
            && Configuration::updateValue(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY, IPGCheckout::TRUE)
            && Configuration::updateValue(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY, IPGCheckout::TRUE)
            && Configuration::updateValue(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY, IPGCheckout::FALSE)
        );
    }

    public function uninstall()
    {
        return (
            parent::uninstall()
            && $this->dropTable()
            && $this->unregisterHook('paymentOptions')
            && $this->unregisterHook('actionOrderSlipAdd')
            && $this->unregisterHook('displayAdminOrderSideBottom')
            && $this->unregisterHook('displayAdminOrderTop')
            && Configuration::deleteByName(IPGCheckout::NAME_KEY)
            && Configuration::deleteByName(IPGCheckout::STORE_ID_KEY)
            && Configuration::deleteByName(IPGCheckout::API_KEY_KEY)
            && Configuration::deleteByName(IPGCheckout::SECRET_KEY)
            && Configuration::deleteByName(IPGCheckout::SANDBOX_KEY)
            && Configuration::deleteByName(IPGCheckout::GENERIC_CHECKOUT_ACTIVE_KEY)
            && Configuration::deleteByName(IPGCheckout::DEBIT_CHECKOUT_ACTIVE_KEY)
            && Configuration::deleteByName(IPGCheckout::APPLE_CHECKOUT_ACTIVE_KEY)
            && Configuration::deleteByName(IPGCheckout::GOOGLE_CHECKOUT_ACTIVE_KEY)
            && Configuration::deleteByName(IPGCheckout::BIZUM_CHECKOUT_ACTIVE_KEY)
        );
    }
}