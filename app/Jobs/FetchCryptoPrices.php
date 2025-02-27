<?php

namespace App\Jobs;

use App\Clients\CryptoClient;
use App\Events\CryptoPriceUpdated;
use App\Models\CryptoPair;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchCryptoPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pairs = explode(',', config('services.crypto.pairs'));
        $exchanges = explode(',', config('services.crypto.exchanges'));
        $cryptoClient = app(CryptoClient::class);

        $updates = collect($pairs)->map(function ($pair) use ($exchanges, $cryptoClient) {
            // Fetch prices in parallel for the pair
            $averagePrice = $this->fetchPricesForPair($pair, $exchanges, $cryptoClient);
            $averagePrice = $averagePrice ?: 0;

            // Fetch last stored price for comparison
            $lastCryptoEntry = CryptoPair::where('pair', $pair)->latest('created_at')->first();
            $previousPrice = $lastCryptoEntry?->average_price ?? 0;

            // Store a new entry instead of updating an existing one
            return CryptoPair::create([
                'pair' => $pair,
                'average_price' => $averagePrice,
                'price_change' => $averagePrice - $previousPrice,
                'last_updated' => now(),
            ]);
        });

        // Broadcast all updates in one go
        $updates->each(fn($cryptoPair) => broadcast(new CryptoPriceUpdated($cryptoPair))->toOthers());

    }

    private function fetchPricesForPair(string $pair, array $exchanges, CryptoClient $cryptoClient): ?float
    {
        // Fetch all prices concurrently
        $pricesByExchange = $cryptoClient->getPrices($exchanges, $pair);

        // Extract valid prices
        $prices = collect($pricesByExchange)
            ->map(fn($data) => $data[0]['last'] ?? null) // Extract "last" price
            ->filter() // Remove null values
            ->map(fn($price) => (float)$price)
            ->values();

        if ($prices->isEmpty()) {
            Log::warning("No valid prices found for pair: $pair across " . implode(', ', $exchanges));
            return null;
        }

        return round($prices->average(), 2);
    }
}
