<?php
namespace App\Api;

class PayPal {
    private static $instance;

    private $clientId;

    private $clientSecret;

    private $environment = 'sandbox';

    private function __construct() {
        $this->clientId = $_ENV['SANDBOX_ID_PAYPAL'];
        $this->clientSecret = $_ENV['SANDBOX_SECRET_PAYPAL'];
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function createOrder($amount, $currency, $returnUrl, $cancelUrl) {

        $accessToken = $this->getAccessToken();
        $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders";
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ];

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
            ],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);

    }

    private function getAccessToken() {
        $url = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
        $headers = [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
        ];
        $postData = 'grant_type=client_credentials';
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        return $data['access_token'];
    }
}
