<?php

use GuzzleHttp\Exception\ClientException;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class ValidateRequest extends RequestHandler
{
    public function __construct(protected string $storeId, protected string $apiKey, protected string $secret)
    {
    }
    public static function getInstance(): ValidateRequest
    {
        return new ValidateRequest(
            Configuration::get(IPGCheckout:: STORE_ID_KEY),
            Configuration::get(IPGCheckout:: API_KEY_KEY),
            Configuration::get(IPGCheckout:: SECRET_KEY)
        );
    }
    protected function getCheckoutUri()
    {
        if (Configuration::get(IPGCheckout:: SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/ipp/payments-gateway/v2/card-information';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/ipp/payments-gateway/v2/card-information';
    }

    public function request()
    {
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = json_encode([
            'storeId' => $this->storeId,
            'paymentCard' => [
                'number' => 'NL91ABNA0417164300'
            ],
            'paymentCardEncrypted' => [
                'encryptionType' => 'JWE',
                'encryptedData' => 'asdf',
                'keyId' => 'asdf'
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
                'Timestamp' => $time,
                'User-Agent' => CheckoutRequestHandler::USER_AGENT
            ],
        ]);

        return $response->getBody()->getContents();
    }

    public function validateCredentials(): bool
    {
        try {
            $this->request();
        } catch (ClientException $e) {
            return !in_array($e->getCode(), [401, 403]);
        }
        return true;
    }
}