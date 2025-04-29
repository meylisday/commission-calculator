<?php

namespace Tests;

use App\Calculator;
use App\Services\ExchangeRateService;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testDepositCalculation()
    {
        $operations = [
            ['2014-12-31', 4, 'private', 'withdraw', 1200.0, 'EUR'],
            ['2015-01-01', 4, 'private', 'withdraw', 1000.0, 'EUR'],
            ['2016-01-05', 4, 'private', 'withdraw', 1000.0, 'EUR'],
            ['2016-01-05', 1, 'private', 'deposit', 200.0, 'EUR'],
            ['2016-01-06', 2, 'business', 'withdraw', 300.0, 'EUR'],
            ['2016-01-06', 1, 'private', 'withdraw', 30000.0, 'JPY'],
            ['2016-01-07', 1, 'private', 'withdraw', 1000.0, 'EUR'],
            ['2016-01-07', 1, 'private', 'withdraw', 100.0, 'USD'],
            ['2016-01-10', 1, 'private', 'withdraw', 100.0, 'EUR'],
            ['2016-01-10', 2, 'business', 'deposit', 10000.0, 'EUR'],
            ['2016-01-10', 3, 'private', 'withdraw', 1000.0, 'EUR'],
            ['2016-02-15', 1, 'private', 'withdraw', 300.0, 'EUR'],
            ['2016-02-19', 5, 'private', 'withdraw', 3000000.0, 'JPY'],
        ];

        $exchangeRateServiceMock = $this->createMock(ExchangeRateService::class);
        $exchangeRateServiceMock->method('getRates')->willReturn([
            'EUR' => 1,
            'USD' => 1.1497,
            'JPY' => 129.53
        ]);

        $calculator = new Calculator($exchangeRateServiceMock);

        foreach ($operations as $operation) {
            $results[] = $calculator->calculate($operation);
        }

        $expectedResults = [
            0.60, 3.00, 0.00, 0.06, 1.50, 0, 0.70, 0.30, 0.30, 3.00, 0.00, 0.00, 8612
        ];

        foreach ($expectedResults as $index => $expected) {
            $actual = $results[$index];
            if ($expected == $actual) {
                echo "\033[32mSuccess at index $index: Expected $expected, got $actual\033[0m\n";
            } else {
                echo "\033[31mError at index $index: Expected $expected, got $actual\033[0m\n";
            }
            $this->assertEquals($expected, $actual);
        }
    }
}