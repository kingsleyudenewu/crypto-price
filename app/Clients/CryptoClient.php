<?php

namespace App\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CryptoClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withToken(config('services.crypto.api_key'))
            ->baseUrl(config('services.crypto.base_url'));
    }


    public function getPrice(string $exchange, string $pair)
    {
        $response = $this->client->get("/getData?symbol={$pair}@{$exchange}");

        return $response->successful() ? data_get($response, 'symbols') : [];
    }
}
