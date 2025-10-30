<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class CheckoutRequestHandler extends RequestHandler
{
    public static function getInstance(): CheckoutRequestHandler
    {
        return new CheckoutRequestHandler(
            Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY),
            Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY),
            Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY)
        );
    }
    private function prepareCreateCheckoutRequestBody(Cart $cart, string $webHooksUrl, string $successUrl, string $failureUrl, string $clientRequestId): string
    {
        ini_set('serialize_precision', -1); // if not there is a float error at some numbers

        $total = $cart->getCartTotalPrice();

        $json = json_encode([
            "storeId" => $this->storeId,
            "merchantTransactionId" => (string) $cart->id,
            "transactionOrigin" => "ECOM",
            "transactionType" => "SALE",
            "transactionAmount" => [
                "total" => $total,
                "currency" => "EUR",
                "components" => [
                    "subtotal" => $total
                ]
            ],
            "checkoutSettings" => [
                "webHooksUrl" => $webHooksUrl . "?id=" . $cart->id,
                "redirectBackUrls" => [
                    "successUrl" => $successUrl . "?id=" . $cart->id,
                    "failureUrl" => $failureUrl . "?id=" . $cart->id
                ]
            ]
        ]);

        return $json;
    }

    public function createCheckout(Cart $cart, string $webHooksUrl, string $successUrl, string $failureUrl)
    {
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = $this->prepareCreateCheckoutRequestBody($cart, $webHooksUrl, $successUrl, $failureUrl, $clientRequestId);

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
                'Timestamp' => $time
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
                'Timestamp' => $time
            ],
        ]);

        return $response->getBody()->getContents();
    }
}