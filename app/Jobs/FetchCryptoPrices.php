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

            $existingCryptoPair = CryptoPair::where('pair', $pair)->first();

            return tap(CryptoPair::updateOrCreate(
                ['pair' => $pair],
                ['average_price' => $averagePrice, 'last_updated' => now()]
            ), function ($cryptoPair) use ($averagePrice, $existingCryptoPair) {
                if ($existingCryptoPair) {
                    $cryptoPair->update([
                        'price_change' => $averagePrice - $existingCryptoPair->average_price
                    ]);
                }
            });
        });

        // Broadcast all updates in one go
        $updates->each(fn($cryptoPair) => broadcast(new CryptoPriceUpdated($cryptoPair))->toOthers());
    }

    private function fetchPricesForPair(string $pair, array $exchanges, CryptoClient $cryptoClient): ?float
    {
        $prices = [];

        foreach ($exchanges as $exchange) {
            $response = $cryptoClient->getPrice($exchange, $pair);

            if (!empty($response) && isset($response[0]['last'])) {
                $prices[] = (float) $response[0]['last'];
            }
        }

        if (empty($prices)) {
            Log::warning("No valid prices found for pair: $pair across " . implode(', ', $exchanges));
            return null;
        }

        return round(array_sum($prices) / count($prices), 2);
    }
}
