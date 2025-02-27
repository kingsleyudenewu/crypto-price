<?php

namespace App\Clients;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $responses = Http::pool(fn(Pool $pool) => collect($exchanges)->map(fn($exchange) =>
        $pool->withToken(config('services.crypto.api_key'))
            ->baseUrl(config('services.crypto.base_url'))
            ->get("/getData?symbol={$pair}@{$exchange}")
        )->toArray());

        return collect($exchanges)->mapWithKeys(function ($exchange, $index) use ($responses) {
            $response = $responses[$index] ?? null;

            if (!$response instanceof \Illuminate\Http\Client\Response) {
                Log::error("Crypto API call failed for {$exchange}");
                return [$exchange => []]; // Return empty array for failed requests
            }

            return [$exchange => ($response->successful()) ? data_get($response->json(), 'symbols', []) : []];
        })->toArray();
    }
}
