<?php
class CheckoutRequestHandler {
    public function __construct(private string $token, private string $secret, private string $storeId) {

    }
    private function auth() {

    }

    public function createCheckout(float $total, string $currency) {

    }
    public function checkoutStatus(string $checkoutId) {

    }
    public function refund($checkoutId, $total ) {

    }
}