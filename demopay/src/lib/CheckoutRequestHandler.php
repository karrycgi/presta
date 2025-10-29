<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class CheckoutRequestHandler extends RequestHandler
{
    private function prepareCreateCheckoutRequestBody(Cart $cart, string $successUrl, string $failureUrl, string $clientRequestId): string
    {
        ini_set( 'serialize_precision', -1 ); // if not there is a float error at some numbers

        $total = $cart->getCartTotalPrice();

        $json = json_encode([
            "storeId" => $this->storeId,
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
                "redirectBackUrls" => [
                    "successUrl" => $successUrl . "?id=" . $cart->id . "&uuid=" . $clientRequestId,
                    "failureUrl" => $failureUrl . "?id=" . $cart->id . "&uuid=" . $clientRequestId
                ]
            ]
        ]);

        return $json;
    }

    public function createCheckout(Cart $cart, string $successUrl, string $failureUrl)
    {
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = $this->prepareCreateCheckoutRequestBody($cart, $successUrl, $failureUrl, $clientRequestId);

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

    }
}