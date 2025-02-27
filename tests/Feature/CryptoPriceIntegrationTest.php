<?php

namespace Tests\Feature;

use App\Clients\CryptoClient;
use App\Events\CryptoPriceUpdated;
use App\Jobs\FetchCryptoPrices;
use App\Models\CryptoPair;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * In the test below, we are testing the  FetchCryptoPrices  job.
 * We are testing that the job broadcasts the  CryptoPriceUpdated  event, stores the new prices correctly, and correctly calculates the price change.
 * We are using the  Event::fake()  method to fake the event dispatcher.
 * We are also using the  Http::fake()  method to fake the HTTP client.
 * We are using the  mock()  method to mock the  CryptoClient  class.
 * We are mocking the getPrices method to return a predefined array of prices.
 * We are using the  dispatchSync() method to dispatch the job synchronously. This will execute the job immediately.
 */

uses(RefreshDatabase::class);

test('fetch crypto prices job broadcasts event', function () {
    Event::fake();

    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrices')
            ->andReturn([
                'binance' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45000.50"],
                    ['symbol' => 'BTCUSDT', 'last' => "96539.92"],
                    ['symbol' => 'BTCETH', 'last' => "24.89"]
                ],
                'mexc' => [
                    ['symbol' => 'BTCUSDC', 'last' => "44990.10"],
                    ['symbol' => 'BTCUSDT', 'last' => "96510.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.95"]
                ],
                'huobi' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45020.00"],
                    ['symbol' => 'BTCUSDT', 'last' => "96525.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.90"]
                ],
            ]);
    }));

    FetchCryptoPrices::dispatchSync();

    Event::assertDispatched(CryptoPriceUpdated::class);
});

test('fetch crypto prices job stores new prices correctly', function () {
    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrices')
            ->andReturn([
                'binance' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45000.50"],
                    ['symbol' => 'BTCUSDT', 'last' => "96539.92"],
                    ['symbol' => 'BTCETH', 'last' => "24.89"]
                ],
                'mexc' => [
                    ['symbol' => 'BTCUSDC', 'last' => "44990.10"],
                    ['symbol' => 'BTCUSDT', 'last' => "96510.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.95"]
                ],
                'huobi' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45020.00"],
                    ['symbol' => 'BTCUSDT', 'last' => "96525.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.90"]
                ],
            ]);
    }));

    FetchCryptoPrices::dispatchSync();

    // Expected average prices per pair
    $expectedPrices = [
        'BTCUSDC' => (45000.50 + 44990.10 + 45020.00) / 3,
        'BTCUSDT' => (96539.92 + 96510.00 + 96525.00) / 3,
        'BTCETH' => (24.89 + 24.95 + 24.90) / 3,
    ];

    $this->assertDatabaseHas('crypto_pairs', [
        'pair' => 'BTCUSDC',
        'average_price' => round($expectedPrices['BTCUSDC'],2),
    ]);
});

test('fetch crypto prices job correctly calculates price change', function () {
    // Create initial crypto pair prices
    $previousPrices = [
        'BTCUSDC' => 44000.00,
        'BTCUSDT' => 96000.00,
        'BTCETH' => 25.00,
    ];

    foreach ($previousPrices as $pair => $price) {
        CryptoPair::factory()->create(['pair' => $pair, 'average_price' => $price]);
    }

    // Mock CryptoClient
    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrices')
            ->andReturn([
                'binance' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45000.50"],
                    ['symbol' => 'BTCUSDT', 'last' => "96539.92"],
                    ['symbol' => 'BTCETH', 'last' => "24.89"]
                ],
                'mexc' => [
                    ['symbol' => 'BTCUSDC', 'last' => "44990.10"],
                    ['symbol' => 'BTCUSDT', 'last' => "96510.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.95"]
                ],
                'huobi' => [
                    ['symbol' => 'BTCUSDC', 'last' => "45020.00"],
                    ['symbol' => 'BTCUSDT', 'last' => "96525.00"],
                    ['symbol' => 'BTCETH', 'last' => "24.90"]
                ],
            ]);
    }));

    FetchCryptoPrices::dispatchSync();

    // Compute expected price change
    $expectedPriceChanges = [
        'BTCUSDC' => ((45000.50 + 44990.10 + 45020.00) / 3) - 44000.00,
        'BTCUSDT' => ((96539.92 + 96510.00 + 96525.00) / 3) - 96000.00,
        'BTCETH' => ((24.89 + 24.95 + 24.90) / 3) - 25.00,
    ];

    $this->assertDatabaseHas('crypto_pairs', [
        'pair' => 'BTCUSDC',
        'price_change' => round($expectedPriceChanges['BTCUSDC'],2),
    ]);
});


