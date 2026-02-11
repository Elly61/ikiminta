<?php
/**
 * Payment Gateway Integration - MOMO
 */

class MOMOPaymentGateway {
    private $apiKey;
    private $apiSecret;
    private $merchantId;
    private $sandbox;
    private $currency;
    private $apiUrl;

    public function __construct() {
        $this->apiKey = MOMO_API_KEY;
        $this->apiSecret = MOMO_API_SECRET;
        $this->merchantId = MOMO_MERCHANT_ID;
        $this->sandbox = MOMO_SANDBOX;
        $this->currency = MOMO_CURRENCY;
        $this->apiUrl = MOMO_SANDBOX ? 'https://sandbox.momoapi.mtn.com' : 'https://api.momoapi.mtn.com';
    }

    /**
     * Initialize a payment request
     */
    public function initiatePayment($phoneNumber, $amount, $externalId) {
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json',
            'X-Reference-Id: ' . $this->generateReferenceId(),
            'X-Target-Environment: ' . ($this->sandbox ? 'sandbox' : 'production')
        ];

        $payload = [
            'amount' => $amount,
            'currency' => $this->currency,
            'externalId' => $externalId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phoneNumber
            ],
            'payerMessage' => 'IKIMINTA Deposit',
            'payeeNote' => 'Payment for IKIMINTA Services'
        ];

        $url = $this->apiUrl . '/collection/v1_0/requesttopay';

        $response = $this->sendRequest($url, 'POST', json_encode($payload), $headers);

        if ($response['status_code'] === 202) {
            return [
                'success' => true,
                'reference_id' => $response['headers']['X-Reference-Id'] ?? null,
                'message' => 'Payment request initiated'
            ];
        }

        return [
            'success' => false,
            'message' => $response['body']['message'] ?? 'Payment initiation failed'
        ];
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($referenceId) {
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'X-Target-Environment: ' . ($this->sandbox ? 'sandbox' : 'production')
        ];

        $url = $this->apiUrl . '/collection/v1_0/requesttopay/' . $referenceId;

        $response = $this->sendRequest($url, 'GET', null, $headers);

        if ($response['status_code'] === 200) {
            $body = json_decode($response['body'], true);
            return [
                'success' => true,
                'status' => $body['status'],
                'data' => $body
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to check payment status'
        ];
    }

    /**
     * Get access token
     */
    private function getAccessToken() {
        $headers = [
            'Authorization: Basic ' . base64_encode($this->merchantId . ':' . $this->apiSecret),
            'Content-Type: application/x-www-form-urlencoded'
        ];

        $url = $this->apiUrl . '/oauth/v1_0/token';
        $payload = 'grant_type=client_credentials';

        $response = $this->sendRequest($url, 'POST', $payload, $headers);

        if ($response['status_code'] === 200) {
            $body = json_decode($response['body'], true);
            return $body['access_token'];
        }

        return null;
    }

    /**
     * Generate unique reference ID
     */
    private function generateReferenceId() {
        return uniqid('MOMO-', true);
    }

    /**
     * Send HTTP request
     */
    private function sendRequest($url, $method, $data, $headers) {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'status_code' => $statusCode,
            'body' => $response,
            'headers' => $this->parseHeaders($responseHeaders),
            'error' => $error
        ];
    }

    /**
     * Parse response headers
     */
    private function parseHeaders($headerString) {
        $headers = [];
        if ($headerString) {
            foreach (explode("\r\n", $headerString) as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $headers[trim($key)] = trim($value);
                }
            }
        }
        return $headers;
    }

    /**
     * Validate phone number format
     */
    public function validatePhoneNumber($phoneNumber) {
        // Format: +256700000000 or 256700000000
        $pattern = '/^\+?256[0-9]{9}$/';
        return preg_match($pattern, $phoneNumber);
    }
}
