#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Commission\BusinessWithdrawCommission;
use App\Commission\DepositCommission;
use App\Commission\PrivateWithdrawCommission;
use App\Services\ErrorHandler;
use App\Calculator;
use App\Reader;
use App\Services\ExchangeRateService;

$filePath = $argv[1] ?? null;

if (!$filePath) {
    echo "Usage: php bin/console.php path/to/file.csv\n";
    exit(1);
}

$reader = new Reader($filePath);
$errorHandler = new ErrorHandler();
$exchangeRateService = new ExchangeRateService($errorHandler, true);
$calculator = new Calculator(
    $exchangeRateService,
    [
        'deposit_private'  => new DepositCommission(),
        'withdraw_business'=> new BusinessWithdrawCommission(),
        'withdraw_private' => new PrivateWithdrawCommission(),
    ]
);

foreach ($reader->getOperations() as $operation) {
    $commission = $calculator->calculate($operation);
    echo $commission . "\n";
}
