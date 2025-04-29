<?php

namespace App\Commission;

interface CommissionStrategyInterface
{
    public function calculate(array $operation, array $rates): float;
}