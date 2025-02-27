<?php

namespace App\Clients;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CryptoClient
{
    /**
     * Get prices for a given pair from multiple exchanges
     *
     * @param array $exchanges
     * @param string $pair
     * @return array
     */
    public function getPrices(array $exchanges, string $pair): array
    {
        // Use Laravel's Pool to send parallel requests with inherited authentication
        $responses = Http::pool(fn(Pool $pool) => collect($exchanges)->map(fn($exchange) => $pool->withToken(config('services.crypto.api_key')) // Ensure token is included
        ->baseUrl(config('services.crypto.base_url')) // Ensure base URL is included
        ->get("/getData?symbol={$pair}@{$exchange}")
        )->toArray()
        );

        // Process responses
        return collect($exchanges)->mapWithKeys(function ($exchange, $index) use ($responses) {
            $response = $responses[$index] ?? null; // Pool responses are indexed numerically
            return [$exchange => ($response && $response->successful()) ? data_get($response->json(), 'symbols', []) : []];
        })->toArray();
    }
}
