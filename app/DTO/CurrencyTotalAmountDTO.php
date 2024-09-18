<?php
namespace App\DTO;
use JsonSerializable;

class CurrencyTotalAmountDTO implements JsonSerializable
{
    public float $totalInBase;
    public string $currencyBase;
    public float $totalInCurrency;
    public string $currencyRequested;

    public function __construct(float $totalInBase, string $currencyBase, float $totalInCurrency, string $currencyRequested)
    {
        $this->totalInBase = round($totalInBase, 2);
        $this->currencyBase = $currencyBase;
        $this->totalInCurrency = round($totalInCurrency, 2);
        $this->currencyRequested = $currencyRequested;
    }


    public static function make(float $totalInBase, string $currencyBase, float $totalInCurrency, string $currencyRequested): CurrencyTotalAmountDTO
    {
        return new self($totalInBase, $currencyBase, $totalInCurrency, $currencyRequested);
    }

    /**
     * Convert the DTO to a JSON serializable array.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'currency_base' => [
                'total' => $this->totalInBase,
                'currency' => $this->currencyBase,
            ],
            'currency_requested' => [
                'total' => $this->totalInCurrency,
                'currency' => $this->currencyRequested,
            ],
        ];
    }
}