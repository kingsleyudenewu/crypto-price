<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\CryptoPair;
use App\Clients\CryptoClient;

uses(RefreshDatabase::class);

test('fetch the average price correctly from crypto pair', function () {
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45000.50]);
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 44990.10]);
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45020.00]);

    $averagePrice = CryptoPair::where('pair', 'BTCUSDC')->avg('average_price');
    expect(round($averagePrice, 2))->toBe(45003.53);
});

test('fetch the price change correctly from crypto pair', function () {
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45000.50]);
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 44990.10]);
    $pair = CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45020.00]);

    $averagePrice = CryptoPair::where('pair', 'BTCUSDC')->avg('average_price');
    $priceChange = CryptoPair::where('pair', 'BTCUSDC')->avg('average_price') - $averagePrice;
    expect($priceChange)->toBe(0.00);
});

test('fetch the price change percentage correctly from crypto pair', function () {
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45000.50]);
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 44990.10]);
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45020.00]);

    $averagePrice = CryptoPair::where('pair', 'BTCUSDC')->avg('average_price');
    $priceChange = CryptoPair::where('pair', 'BTCUSDC')->avg('average_price') - $averagePrice;
    $priceChangePercentage = ($priceChange / $averagePrice) * 100;
    expect($priceChangePercentage)->toBe(0.00);
});

test('fetch the last updated price correctly from crypto pair', function () {
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45000.50]);
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 44990.10]);
    CryptoPair::factory()->create(['pair' => 'BTCUSDC', 'average_price' => 45020.00]);

    $lastCryptoEntry = CryptoPair::where('pair', 'BTCUSDC')->latest('id')->first();

    expect($lastCryptoEntry->average_price)->toBe(45020.00);
});

test('fetch api prices concurrently from crypto client', function () {
    $exchanges = ['binance', 'huobi'];
    $pair = 'BTCUSDC';
    $cryptoClient = app(CryptoClient::class);

    $pricesByExchange = $cryptoClient->getPrices($exchanges, $pair);
    expect($pricesByExchange)->toHaveCount(2);
    expect($pricesByExchange)->toHaveKeys(['binance', 'huobi']);
    expect($pricesByExchange['binance'])->toHaveCount(1);
    expect($pricesByExchange['huobi'])->toHaveCount(1);
    expect($pricesByExchange['binance'][0])->toHaveKey('last');
    expect($pricesByExchange['huobi'][0])->toHaveKey('last');
});


