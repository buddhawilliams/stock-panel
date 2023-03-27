<?php

namespace App\Provider;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\Exception\ApiException;

readonly class SearchResultProvider
{
    public function __construct(
        private ApiClient $api
    ) {
    }

    /**
     * @param string $searchTerm
     * @return array
     * @throws ApiException
     */
    public function search(string $searchTerm): array
    {
        try {
            $data = $this->api->search($searchTerm);
        } catch (ApiException $e) {
            throw new ApiException('Error in search: ' . $e->getMessage(), 1, $e);
        }
        return $this->createResult($data);
    }

    private function createResult(array $data): array
    {
        $resultSet = [];
        foreach ($data as $result) {
            $resultSet[] = [
                'symbol' => $result->getSymbol(),
                'name' => $result->getName(),
                'exchange' => $result->getExchDisp() ?? null,
            ];
        }
        return $resultSet;
    }
}
