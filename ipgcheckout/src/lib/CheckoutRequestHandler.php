<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class CheckoutRequestHandler extends RequestHandler
{
    const USER_AGENT = 'IPGCheckout/1.0 Prestashop/9.0.0';
    public static function getInstance(): CheckoutRequestHandler
    {
        return new CheckoutRequestHandler(
            Configuration::get(IPGCheckout:: STORE_ID_KEY),
            Configuration::get(IPGCheckout:: API_KEY_KEY),
            Configuration::get(IPGCheckout:: SECRET_KEY)
        );
    }

    protected function getCheckoutUri()
    {
        if (Configuration::get(IPGCheckout:: SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/exp/v1/checkouts';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/exp/v1/checkouts';
    }

    private function toPaymentMethode(string $name): string {
        switch(trim($name)) {
            case "applepay": return "applepay";
            case "googlepay": return "googlepay";
            case "cards": return "cards";
            case "bizum": return "bizum";
            default: return "generic";
        }
    }
    private function prepareCreateCheckoutRequestBody(Cart $cart, string $webHooksUrl, string $successUrl, string $failureUrl, string $paymentMethode): string
    {
        ini_set('serialize_precision', -1); // if not there is a float error at some numbers

        $total = $cart->getCartTotalPrice();
        $total_items = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $shipping_costs = $cart->getTotalShippingCost();

        $currency = new Currency((int) $cart->id_currency)->iso_code;

        $obj = [
            "storeId" => $this->storeId,
            "merchantTransactionId" => (string) $cart->id,
            "transactionOrigin" => "ECOM",
            "transactionType" => "SALE",
            "transactionAmount" => [
                "total" => $total,
                "currency" => $currency,
                "components" => [
                    "subtotal" => $total_items,
                    "shipping" => $shipping_costs,
                ]
            ],
            "checkoutSettings" => [
                "preSelectedPaymentMethod" => $paymentMethode == "generic"?null:$paymentMethode,
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
        $response = $client->request('POST', $this->getCheckoutUri(), [
            'body' => $requestBody,
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Api-Key' => $this->apiKey,
                'Client-Request-Id' => $clientRequestId,
                'Message-Signature' => $messageSignature,
                'Timestamp' => $time,
                'User-Agent' => CheckoutRequestHandler::USER_AGENT
            ],
        ]);

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
                'User-Agent' => CheckoutRequestHandler::USER_AGENT
            ],
        ]);

        return $response->getBody()->getContents();
    }

    public function getTransactionId($checkoutId) {
        $status = json_decode(CheckoutRequestHandler::getInstance()->checkoutStatus($checkoutId), true);
        return $status['ipgTransactionDetails']['ipgTransactionId'];
    }
}