<?php

class CheckoutRequestHandler
{
    public function __construct(private string $storeId, private string $apiKey, private string $secret)
    {
    }

    private function getCheckoutUri()
    {
        if (Configuration::get(DemoPay::DEMO_PAY_SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/exp/v1/checkouts';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/exp/v1/checkouts';
    }

    private function sign(string $requestId, int $time, string $body)
    {
        $token = $this->apiKey . $requestId . $time . $body;
        return base64_encode(hash_hmac('sha256', $token, strval($this->secret), true));
    }

    private static function generateUuid(): string
    {
        $out = bin2hex(random_bytes(18));

        $out[8] = "-";
        $out[13] = "-";
        $out[18] = "-";
        $out[23] = "-";
        $out[14] = "4";
        $out[19] = ["8", "9", "a", "b"][random_int(0, 3)];

        return $out;
    }

    public function createCheckout(Cart $cart, string $successUrl, string $failureUrl)
    {
        $time = intval(microtime(true) * 1000);

        $total = $cart->getTotalShippingCost();

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = json_encode([
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
                    "successUrl" => $successUrl."?id=".$cart->id."&uuid=".$clientRequestId,
                    "failureUrl" => $failureUrl."?id=".$cart->id."&uuid=".$clientRequestId
                ]
            ]
        ]);

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
    public function refund($checkoutId, $total)
    {

    }
}