<?php

class RequestHandler {
    public function __construct(protected string $storeId, protected string $apiKey, protected string $secret)
    {
    }
    protected function getCheckoutUri()
    {
        if (Configuration::get(DemoPay::DEMO_PAY_SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/exp/v1/checkouts';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/exp/v1/checkouts';
    }

    protected function sign(string $requestId, int $time, string $body="")
    {
        $token = $this->apiKey . $requestId . $time . $body;
        return base64_encode(hash_hmac('sha256', $token, strval($this->secret), true));
    }

    protected static function generateUuid(): string
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
}