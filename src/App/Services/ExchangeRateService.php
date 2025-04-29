<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class ExchangeRateService
{
    private const URL = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    public function __construct(
        private readonly ErrorHandler $errorHandler,
        private readonly bool $apiAvailable
    ) {}

    /**
     * @throws GuzzleException
     */
    public function getRates(): array
    {
        if ($this->apiAvailable) {
            return $this->fetchRatesFromApi();
        }
        return $this->getDefaultRates();
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    private function fetchRatesFromApi(): array
    {
        $client = new Client();
        try {
            $response = $client->get(self::URL);
            $responseBody = (string) $response->getBody();
            $data = json_decode($responseBody, true);

            if (isset($data['rates'])) {
                return $data['rates'];
            }

            throw new Exception("Invalid API response structure");
        } catch (RequestException $e) {
            throw $this->errorHandler->handleApiError($e->getMessage());
        } catch (Exception $e) {
            throw $this->errorHandler->handleGeneralError($e->getMessage());
        }
    }

    private function getDefaultRates(): array
    {
        return [
            'EUR' => 1,
            'USD' => 1.1497,
            'JPY' => 129.53
        ];
    }
}