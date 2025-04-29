<?php

declare(strict_types=1);

namespace App;

use App\Services\ExchangeRateService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use DateTimeImmutable;

class Calculator
{
    private const DEPOSIT_RATE = 0.0003;
    private const BUSINESS_WITHDRAW_RATE = 0.005;
    private const PRIVATE_WITHDRAW_RATE = 0.003;

    private const WEEKLY_FREE_LIMIT = 1000.00;
    private const WEEKLY_FREE_OPERATIONS = 3;

    private const CURRENCY_DECIMALS = [
        'USD' => 2,
        'EUR' => 2,
        'JPY' => 0,
    ];

    private array $weeklyWithdrawals = [];

    public function __construct(
        private readonly ExchangeRateService $exchangeRateService
    ) {}

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function calculate(array $operation): float
    {
        [$date, $userId, $userType, $operationType, $amount, $currency] = $operation;
        $rates = $this->exchangeRateService->getRates();

        if ($operationType === 'deposit') {
            return $this->calculateCommission((float) $amount, $currency, self::DEPOSIT_RATE, $rates);
        }

        if ($userType === 'business' && $operationType === 'withdraw') {
            return $this->calculateCommission((float) $amount, $currency, self::BUSINESS_WITHDRAW_RATE, $rates);
        }

        if ($userType === 'private' && $operationType === 'withdraw') {
            return $this->calculatePrivateWithdraw(
                new DateTimeImmutable($date),
                (int) $userId,
                (float) $amount,
                $currency,
                $rates
            );
        }

        return 0.00;
    }

    private function calculateCommission(
        float $amount,
        string $currency,
        float $rate,
        array $rates
    ): float {
        $commission = $amount * $rate;
        $convertToEUR = $this->convertToEUR($commission, $currency, $rates);
        $commissionInCurrency = $this->convertFromEUR($convertToEUR, $currency, $rates);
        return $this->roundToCurrencyDecimal($commissionInCurrency, $currency);
    }

    private function calculatePrivateWithdraw(
        DateTimeImmutable $date,
        int $userId,
        float $amount,
        string $currency,
        array $rates
    ): float {
        $weekKey = $this->getWeekKey($date, $userId);
        $this->initializeWeekDataIfNeeded($weekKey);

        $amountInEUR = $this->convertToEUR($amount, $currency, $rates);

        $weekData = &$this->weeklyWithdrawals[$weekKey];
        $freeAmountLeft = max(0.0, self::WEEKLY_FREE_LIMIT - $weekData['amount']);

        $commissionableAmount = 0.0;
        if ($weekData['count'] < self::WEEKLY_FREE_OPERATIONS) {
            $commissionableAmount = max(0.0, $amountInEUR - $freeAmountLeft);
        } else {
            $commissionableAmount = $amountInEUR;
        }

        $weekData['amount'] += $amountInEUR;
        $weekData['count']++;

        $commissionInEUR = $commissionableAmount * self::PRIVATE_WITHDRAW_RATE;
        $commission = $this->convertFromEUR($commissionInEUR, $currency, $rates);

        return $this->roundToCurrencyDecimal($commission, $currency);
    }

    private function getWeekKey(DateTimeImmutable $date, int $userId): string
    {
        return $userId . '-' . $date->format('o-W');
    }

    private function initializeWeekDataIfNeeded(string $key): void
    {
        if (!isset($this->weeklyWithdrawals[$key])) {
            $this->weeklyWithdrawals[$key] = [
                'count' => 0,
                'amount' => 0.0,
            ];
        }
    }

    private function convertToEUR(float $amount, string $currency, array $rates): float
    {
        return $currency === 'EUR' ? $amount : $amount / $rates[$currency];
    }

    private function convertFromEUR(float $amount, string $currency, array $rates): float
    {
        return $currency === 'EUR' ? $amount : $amount * $rates[$currency];
    }

    private function roundToCurrencyDecimal(float $amount, string $currency): float
    {
        return match ($currency) {
            'JPY' => ceil($amount),
            'USD' => floor($amount * 10) / 10,
            default => $this->roundUp($amount, self::CURRENCY_DECIMALS[$currency] ?? 2),
        };
    }

    private function roundUp(float $amount, int $decimals): float
    {
        $multiplier = pow(10, $decimals);
        return ceil($amount * $multiplier) / $multiplier;
    }
}
