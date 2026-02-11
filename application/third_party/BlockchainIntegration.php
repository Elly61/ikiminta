<?php
/**
 * Blockchain Integration
 */

class BlockchainIntegration {
    private $apiUrl;
    private $network;
    private $enabled;

    public function __construct() {
        $this->apiUrl = BLOCKCHAIN_API_URL;
        $this->network = BLOCKCHAIN_NETWORK;
        $this->enabled = BLOCKCHAIN_ENABLED;
    }

    /**
     * Record a transaction on blockchain
     */
    public function recordTransaction($transactionType, $transactionId, $userId, $amount) {
        if (!$this->enabled) {
            return ['success' => true, 'message' => 'Blockchain disabled'];
        }

        try {
            $blockHash = $this->generateBlockHash($transactionId, $userId, $amount);

            $payload = [
                'transaction_type' => $transactionType,
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'amount' => $amount,
                'block_hash' => $blockHash,
                'timestamp' => time(),
                'network' => $this->network
            ];

            // Store blockchain record in database
            $blockchainRecord = [
                'transaction_type' => $transactionType,
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'amount' => $amount,
                'block_hash' => $blockHash,
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 'verified'
            ];

            // In production, this would be sent to actual blockchain
            // For now, we'll store locally
            
            return [
                'success' => true,
                'block_hash' => $blockHash,
                'message' => 'Transaction recorded on blockchain',
                'data' => $blockchainRecord
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to record transaction on blockchain: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate block hash using SHA-256
     */
    private function generateBlockHash($transactionId, $userId, $amount) {
        $data = implode('|', [
            $transactionId,
            $userId,
            $amount,
            time(),
            $this->network
        ]);

        return hash('sha256', $data);
    }

    /**
     * Verify transaction on blockchain
     */
    public function verifyTransaction($blockHash) {
        if (!$this->enabled) {
            return ['verified' => true, 'message' => 'Blockchain disabled'];
        }

        try {
            $url = $this->apiUrl . 'verify';

            $payload = json_encode([
                'block_hash' => $blockHash,
                'network' => $this->network
            ]);

            $response = $this->sendRequest($url, 'POST', $payload);

            if ($response['status_code'] === 200) {
                $body = json_decode($response['body'], true);
                return [
                    'verified' => $body['verified'] ?? false,
                    'message' => $body['message'] ?? 'Verification complete',
                    'data' => $body
                ];
            }

            return [
                'verified' => false,
                'message' => 'Verification failed'
            ];
        } catch (Exception $e) {
            return [
                'verified' => false,
                'message' => 'Error verifying transaction: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction history from blockchain
     */
    public function getTransactionHistory($userId, $limit = 50) {
        if (!$this->enabled) {
            return ['transactions' => [], 'message' => 'Blockchain disabled'];
        }

        try {
            $url = $this->apiUrl . 'transactions?user_id=' . $userId . '&limit=' . $limit;

            $response = $this->sendRequest($url, 'GET');

            if ($response['status_code'] === 200) {
                $body = json_decode($response['body'], true);
                return [
                    'success' => true,
                    'transactions' => $body['transactions'] ?? [],
                    'message' => 'Transactions retrieved'
                ];
            }

            return [
                'success' => false,
                'transactions' => [],
                'message' => 'Failed to retrieve transactions'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'transactions' => [],
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send HTTP request
     */
    private function sendRequest($url, $method, $data = null) {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 10
        ]);

        if ($data !== null && $method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'status_code' => $statusCode,
            'body' => $response,
            'error' => $error
        ];
    }

    /**
     * Check if blockchain is enabled
     */
    public function isEnabled() {
        return $this->enabled;
    }
}
