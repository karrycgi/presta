<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class CheckoutRequestHandler extends RequestHandler
{
    const USER_AGENT_HEADER_FIELD = 'x-shopplugin';
    const USER_AGENT = 'IPGCheckout/1.0 Prestashop/'._PS_VERSION_.' PHP/'.PHP_VERSION;
    public static function getInstance(): CheckoutRequestHandler
    {
        return new CheckoutRequestHandler(
            Configuration::get(IPGCheckout::STORE_ID_KEY),
            Configuration::get(IPGCheckout::API_KEY_KEY),
            Configuration::get(IPGCheckout::SECRET_KEY)
        );
    }

    protected function getCheckoutUri()
    {
        if (Configuration::get(IPGCheckout::SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/exp/v1/checkouts';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/exp/v1/checkouts';
    }

    private function toPaymentMethode(string $name): string
    {
        switch (trim($name)) {
            case "applepay":
                return "applepay";
            case "googlepay":
                return "googlepay";
            case "cards":
                return "cards";
            case "bizum":
                return "bizum";
            default:
                return "generic";
        }
    }
    private function prepareCreateCheckoutRequestBody(Cart $cart, string $webHooksUrl, string $successUrl, string $failureUrl, string $paymentMethode): string
    {
        ini_set('serialize_precision', -1); // if not there is a float error at some numbers

        $total = $cart->getCartTotalPrice();
        $total_items = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) - $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $vat_amount = $cart->getOrderTotal(true) - $cart->getOrderTotal(false);
        $shipping_costs = $cart->getTotalShippingCost();
        if($vat_amount > 0){$total_items -= $vat_amount;} // if vat is included in product price we have to subtract it from total items to avoid double counting

        $currency = new Currency((int) $cart->id_currency)->iso_code;

        $customer = new Customer((int) $cart->id_customer);
        $address = new Address((int) $cart->id_address_invoice);

        $obj = [
            "storeId" => $this->storeId,
            "transactionOrigin" => "ECOM",
            "transactionType" => "SALE",
            "order" => [
                "basket" => [
                    "lineItems" => array_map(function ($product) {
                        return [
                            "itemIdentifier" => (string) $product["id_product"],
                            "name" => $product["name"],
                            "price" => $product["price"],
                            "quantity" => $product["quantity"],
                            "total" => $product["total"]
                        ];
                    }, $cart->getProducts())
                ],
                "billing" => [
                    "person" => [
                        "firstName" => $customer->firstname,
                        "lastName" => $customer->lastname
                    ],
                    "contact" => [
                        "email" => $customer->email
                    ],
                    "address" => [
                        "address1" => $address->address1,
                        "address2" => $address->address2,
                        "city" => $address->city,
                        "country" => Country::getIsoById((int) $address->id_country),
                        "postalCode" => $address->postcode
                    ]
                ]
            ],
            "transactionAmount" => [
                "total" => $total,
                "currency" => $currency,
                "components" => [
                    "subtotal" => round($total_items, 2),
                    "shipping" => round($shipping_costs, 2),
                    "vatAmount" => round($vat_amount, 2)
                ]
            ],
            "checkoutSettings" => [
                "preSelectedPaymentMethod" => $paymentMethode == "generic" ? null : $paymentMethode,
                "webHooksUrl" => $webHooksUrl . "?id=" . $cart->id,
                "redirectBackUrls" => [
                    "successUrl" => $successUrl . "?id=" . $cart->id,
                    "failureUrl" => $failureUrl . "?id=" . $cart->id
                ]
            ]
        ];

        $json = json_encode($obj);

        return $json;
    }

    public function createCheckout(Cart $cart, string $webHooksUrl, string $successUrl, string $failureUrl, string $paymentMethode)
    {
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = $this->prepareCreateCheckoutRequestBody($cart, $webHooksUrl, $successUrl, $failureUrl, $this->toPaymentMethode($paymentMethode));

        $messageSignature = $this->sign($clientRequestId, $time, $requestBody);

        $client = new \GuzzleHttp\Client();

        $requestOptions = [
            'body' => $requestBody,
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Api-Key' => $this->apiKey,
                'Client-Request-Id' => $clientRequestId,
                'Message-Signature' => $messageSignature,
                'Timestamp' => $time,
                CheckoutRequestHandler::USER_AGENT_HEADER_FIELD => CheckoutRequestHandler::USER_AGENT
            ],
        ];

        $response = $client->request('POST', $this->getCheckoutUri(), $requestOptions);

        PrestaShopLogger::addLog(CheckoutRequestHandler::USER_AGENT_HEADER_FIELD.' - - '.CheckoutRequestHandler::USER_AGENT);

        return $response->getBody()->getContents();
    }

    public function checkoutStatus(string $checkoutId)
    {
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $messageSignature = $this->sign($clientRequestId, $time);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->getCheckoutUri() . '/' . $checkoutId, [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Api-Key' => $this->apiKey,
                'Client-Request-Id' => $clientRequestId,
                'Message-Signature' => $messageSignature,
                'Timestamp' => $time,
                CheckoutRequestHandler::USER_AGENT_HEADER_FIELD => CheckoutRequestHandler::USER_AGENT
            ],
        ]);

        return $response->getBody()->getContents();
    }

    public function getTransactionId($checkoutId)
    {
        $status = json_decode(CheckoutRequestHandler::getInstance()->checkoutStatus($checkoutId), true);
        return $status['ipgTransactionDetails']['ipgTransactionId'];
    }
}