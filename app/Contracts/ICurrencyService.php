<?php
namespace App\Contracts;

interface ICurrencyService
{
    public function getCurrencyBase(): string;
    public function getCurrencyRateToBase(string $currency): float;
    public function getCurrencyRate(string $from, string $to): float;
    public function getCurrencyRateFromBase(string $currency): float;
    public function convertToCurrency(float $amount, string $from, string $to): float;
    public function convertToBaseCurrency(float $amount, string $currency): float;

}