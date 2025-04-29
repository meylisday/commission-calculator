<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exceptions\ApiException;
use Exception;

class ErrorHandler
{
    public function handleApiError(string $errorMessage): ApiException
    {
        return new ApiException("API Error: " . $errorMessage);
    }

    /**
     * @throws Exception
     */
    public function handleGeneralError(string $errorMessage): Exception
    {
        return new Exception("General Error: " . $errorMessage);
    }
}