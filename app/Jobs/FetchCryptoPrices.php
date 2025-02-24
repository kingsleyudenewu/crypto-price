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
        $pairs = explode(',',config('services.crypto.pairs'));
        $exchanges = explode(',',config('services.crypto.exchanges'));
        $cryptoClient = app(CryptoClient::class);

        $updates = collect($pairs)->map(function ($pair) use ($exchanges, $cryptoClient) {
            $averagePrice = $this->fetchPricesForPair($pair, $exchanges, $cryptoClient);
            $averagePrice = $averagePrice ?: 0;

            return tap(CryptoPair::updateOrCreate(
                ['pair' => $pair],
                ['average_price' => $averagePrice, 'last_updated' => now()]
            ), function ($cryptoPair) use ($averagePrice) {
                if (!$cryptoPair->wasRecentlyCreated) {
                    $cryptoPair->update([
                        'price_change' => $averagePrice - $cryptoPair->getOriginal('average_price')
                    ]);
                }
            });
        });

        // Broadcast all updates in one go
        $updates->each(fn($cryptoPair) => broadcast(new CryptoPriceUpdated($cryptoPair))->toOthers());
    }

    private function fetchPricesForPair(string $pair, array $exchanges, CryptoClient $cryptoClient): ?float
    {
        // Fetch API responses and extract 'last' price correctly
        $prices = collect($exchanges)->map(function ($exchange) use ($pair, $cryptoClient) {
            $response = $cryptoClient->getPrice($exchange, $pair);
            // Ensure response contains expected data format
            return isset($response[0]['last']) ? (float) $response[0]['last'] : null;
        });

        // Remove null values (failed responses)
        $validPrices = $prices->filter();

        if ($validPrices->isEmpty()) {
            Log::warning("No valid prices found for pair: $pair");
            return null;
        }

        // Calculate average price
        return round($validPrices->avg(), 2);
    }
}
