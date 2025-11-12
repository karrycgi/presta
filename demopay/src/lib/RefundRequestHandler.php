<?php

require_once dirname(__FILE__) . '/../../vendor/autoload.php';
class RefundRequestHandler extends RequestHandler
{
    public static function getInstance(): RefundRequestHandler
    {
        return new RefundRequestHandler(
            Configuration::get(DemoPay::DEMO_PAY_STORE_ID_KEY),
            Configuration::get(DemoPay::DEMO_PAY_API_KEY_KEY),
            Configuration::get(DemoPay::DEMO_PAY_SECRET_KEY)
        );
    }
    protected function getCheckoutUri()
    {
        if (Configuration::get(DemoPay::DEMO_PAY_SANDBOX_KEY) === 'FALSE') {
            return 'https://prod.emea.api.fiservapps.com/ipp/payments-gateway/v2/payments';
        }

        return 'https://prod.emea.api.fiservapps.com/sandbox/ipp/payments-gateway/v2/payments';
    }

    protected function getRefundUriWithTransactionId($id) {
        return $this->getCheckoutUri().'/'.$id;
    }
    public static function &getCustomerThread(int $customer_id, int $order_id): CustomerThread
    {
        $customer = new Customer((int) $customer_id);
        // 2. Check if a customer thread already exists for this order and email
        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order_id);

        if (!$id_customer_thread) {
            // If no thread exists, create a new one
            $customer_thread = new CustomerThread();
            $customer_thread->id_contact = 0; // General contact (or specific contact ID if needed)
            $customer_thread->id_customer = (int) $customer->id;
            $customer_thread->id_order = (int) $order_id;
            $customer_thread->id_lang = (int) $customer->id_lang;
            $customer_thread->email = $customer->email;
            // $customer_thread->status = 'open'; // Set the thread status
            $customer_thread->token = Tools::passwdGen(12); // Generate a unique token
            $customer_thread->add();
        } else {
            // Otherwise, load the existing thread
            $customer_thread = new CustomerThread((int) $id_customer_thread);
        }

        return $customer_thread;
    }

    public function request(Order &$order, int $transaction_id, float $amount) {
        ini_set('serialize_precision', -1); // if not there is a float error at some numbers
        
        $time = intval(microtime(true) * 1000);

        $clientRequestId = CheckoutRequestHandler::generateUuid();

        $requestBody = json_encode([
            'storeId' => $this->storeId,
            'requestType' => 'ReturnTransaction',
            'transactionAmount' => [
                'total' => $amount,
                'currency' => new Currency((int) $order->id_currency)->iso_code
            ]
        ]);
        PrestaShopLogger::addLog(var_export('Request Amount: '.$amount, true), 1);
        PrestaShopLogger::addLog(var_export('Request body: '.$requestBody, true), 1);

        $messageSignature = $this->sign($clientRequestId, $time, $requestBody);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $this->getRefundUriWithTransactionId($transaction_id), [
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

    public static function writeMessage(Order $order, string $message)
    {
        $customer_thread = &RefundRequestHandler::getCustomerThread($order->id_customer, $order->id);

        $customer_message = new CustomerMessage();
        $customer_message->id_customer_thread = (int) $customer_thread->id;
        $customer_message->id_employee = 0;
        $customer_message->message = $message;
        $customer_message->private = 1;

        return $customer_message->add();
    }
}