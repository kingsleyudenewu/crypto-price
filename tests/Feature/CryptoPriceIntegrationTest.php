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

uses(RefreshDatabase::class);

test('fetch crypto prices job broadcasts event', function () {
    Event::fake();

    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrice')
            ->andReturn([
                'status' => 'success',
                'symbols' => [
                    'symbol' => 'BTCUSDT',
                    "last" => "96539.92",
                    "lowest" => "96415.19",
                    "highest" => "96709.99",
                    "date" => "2025-02-23 06:33:07",
                    "daily_change_percentage" => "0.010266237411560915",
                    "last_btc" => "0"
                ]
            ]);
    }));

    CryptoPair::factory()->create(['pair' => 'BTCUSD', 'average_price' => 96539.92]);

    FetchCryptoPrices::dispatchSync();

    Event::assertDispatched(\App\Events\CryptoPriceUpdated::class);
});


test('fetch crypto prices job updates database', function () {
    // Create initial crypto pairs
    CryptoPair::factory()->create(['pair' => 'BTCUSDT', 'average_price' => 49000]);

    // Mock the CryptoApiService
    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrice')
            ->andReturn([
                ['symbol' => 'BTCUSDT', 'last' => "96539.92", 'lowest' => "96415.19", 'highest' => "96709.99"]
            ]);
    }));

    // Run the job
    FetchCryptoPrices::dispatchSync();

    // Assert the database was updated
    $this->assertDatabaseHas('crypto_pairs', [
        'pair' => 'BTCUSDT',
        'average_price' => 96539.92,
    ]);
});

test('fetch crypto prices job updates database with new price', function () {
    // Mock the CryptoApiService
    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrice')
            ->andReturn([
                ['symbol' => 'BTCUSDT', 'last' => "96539.92", 'lowest' => "96415.19", 'highest' => "96709.99"]
            ]);
    }));

    // Create initial crypto pairs
    CryptoPair::factory()->create(['pair' => 'BTCUSDT', 'average_price' => 49000]);

    // Run the job
    FetchCryptoPrices::dispatchSync();

    // Assert the database was updated
    $this->assertDatabaseHas('crypto_pairs', [
        'pair' => 'BTCUSDT',
        'average_price' => 96539.92,
    ]);
});

test('fetch crypto prices job updates database with price change', function () {
    // Create initial crypto pairs
    CryptoPair::factory()->create(['pair' => 'BTCUSDT', 'average_price' => 49000]);

    // Mock the CryptoApiService
    $this->instance(CryptoClient::class, $this->mock(CryptoClient::class, function ($mock) {
        $mock->shouldReceive('getPrice')
            ->andReturn([
                ['symbol' => 'BTCUSDT', 'last' => "96539.92", 'lowest' => "96415.19", 'highest' => "96709.99"]
            ]);
    }));

    // Run the job
    FetchCryptoPrices::dispatchSync();

    // Assert the database was updated
    $this->assertDatabaseHas('crypto_pairs', [
        'pair' => 'BTCUSDT',
        'average_price' => 96539.92,
        'price_change' => 47539.92,
    ]);
});




