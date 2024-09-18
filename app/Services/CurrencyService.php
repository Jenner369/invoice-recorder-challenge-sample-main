<?php
namespace App\Services;
use Cache;
use Http;

class CurrencyService
{
    protected $cacheExpiration;
    protected $currencyBase;
    protected $currencyApi;
    public function __construct()
    {
        $this->cacheExpiration = config('currency.cache_expiration');
        $this->currencyBase = config('currency.currency_base');
        $this->currencyApi = config('currency.currency_api');
    }

    private function generateCacheKey(string $from, string $to): string
    {
        return "currency_rate_{$from}_to_{$to}";
    }

    public function getCurrencyBase(): string
    {
        return $this->currencyBase;
    }

    public function getCurrencyRateToBase(string $currency): float
    {
        $cacheKey = $this->generateCacheKey($currency, $this->currencyBase);
        return Cache::remember($cacheKey, $this->cacheExpiration, function () use ($currency) {
            $url = "{$this->currencyApi}{$currency}";
            $response = Http::get($url);
            if ($response->successful()) {
                $rates = $response->json('rates');
                if (isset($rates[$this->currencyBase])) {
                    return $rates[$this->currencyBase];
                }
                throw new \Exception("No se encontró la tasa para la moneda solicitada: {$currency}");
            }
            throw new \Exception("Error al obtener las tasas de conversión de la API.");
        });
    }

    public function getCurrencyRate(string $from, string $to): float
    {
        $cacheKey = $this->generateCacheKey($from, $to);
        return Cache::remember($cacheKey, $this->cacheExpiration, function () use ($from, $to) {
            $url = "{$this->currencyApi}{$from}";
            $response = Http::get($url);
            if ($response->successful()) {
                $rates = $response->json('rates');
                if (isset($rates[$to])) {
                    return $rates[$to];
                }
                throw new \Exception("No se encontró la tasa para la moneda solicitada: {$to}");
            }
            throw new \Exception("Error al obtener las tasas de conversión de la API.");
        });
    }

    public function getCurrencyRateFromBase(string $currency): float
    {
        $cacheKey = $this->generateCacheKey($this->currencyBase, $currency);
        
        return Cache::remember($cacheKey, $this->cacheExpiration, function () use ($currency) {

            $url = "{$this->currencyApi}{$this->currencyBase}";
            $response = Http::get($url);
            
            if ($response->successful()) {
                
                $rates = $response->json('rates');
                
                if (isset($rates[$currency])) {
                    return $rates[$currency];
                }

                throw new \Exception("No se encontró la tasa para la moneda solicitada: {$currency}");
            }

            throw new \Exception("Error al obtener las tasas de conversión de la API.");
        });
    }

    public function convertToCurrency(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;
        
        if ($from === $this->currencyBase) return $amount * $this->getCurrencyRateToBase($to);

        if ($to === $this->currencyBase) return $amount * $this->getCurrencyRateFromBase($from);

        return $amount * $this->getCurrencyRate($from, $to);
    }

    public function convertToBaseCurrency(float $amount, string $currency): float
    {
        if ($currency === $this->currencyBase) return $amount;
        
        return $amount * $this->getCurrencyRateToBase($currency);
    }

}