<?php
/**
 * Currency Converter Library
 */

require_once dirname(__FILE__) . '/../config/Database.php';

class CurrencyConverter {
    private $db;
    private $defaultCurrency;
    private $rates = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->defaultCurrency = DEFAULT_CURRENCY;
        $this->loadRates();
    }

    /**
     * Load exchange rates from database
     */
    private function loadRates() {
        $rates = $this->db->select('SELECT * FROM currency_rates');
        foreach ($rates as $rate) {
            $key = $rate['from_currency'] . '_' . $rate['to_currency'];
            $this->rates[$key] = $rate['rate'];
        }
    }

    /**
     * Convert currency
     */
    public function convert($amount, $fromCurrency, $toCurrency) {
        // If same currency, return same amount
        if ($fromCurrency === $toCurrency) {
            return [
                'amount' => $amount,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'rate' => 1,
                'success' => true
            ];
        }

        // Get exchange rate
        $key = $fromCurrency . '_' . $toCurrency;
        
        if (!isset($this->rates[$key])) {
            return [
                'success' => false,
                'message' => 'Exchange rate not available'
            ];
        }

        $rate = $this->rates[$key];
        $convertedAmount = $amount * $rate;

        return [
            'amount' => round($convertedAmount, 2),
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'rate' => $rate,
            'success' => true
        ];
    }

    /**
     * Get exchange rate
     */
    public function getRate($fromCurrency, $toCurrency) {
        $key = $fromCurrency . '_' . $toCurrency;
        return $this->rates[$key] ?? null;
    }

    /**
     * Update exchange rate
     */
    public function updateRate($fromCurrency, $toCurrency, $rate) {
        try {
            $this->db->update(
                'currency_rates',
                ['rate' => $rate],
                'from_currency = ? AND to_currency = ?',
                [$fromCurrency, $toCurrency]
            );
            
            // Reload rates
            $this->loadRates();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get all supported currencies
     */
    public function getSupportedCurrencies() {
        return SUPPORTED_CURRENCIES;
    }

    /**
     * Get default currency
     */
    public function getDefaultCurrency() {
        return $this->defaultCurrency;
    }

    /**
     * Batch convert currencies
     */
    public function batchConvert($amounts, $fromCurrency, $toCurrency) {
        $results = [];
        
        foreach ($amounts as $amount) {
            $results[] = $this->convert($amount, $fromCurrency, $toCurrency);
        }
        
        return $results;
    }
}
