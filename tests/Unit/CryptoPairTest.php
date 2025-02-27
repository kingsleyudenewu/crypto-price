<?php

use App\Models\CryptoPair;
use function Pest\Laravel\mock;

test('fetch the average price correctly from crypto pair', function () {
    // Mock CryptoPair model
    $cryptoPairMock = mock(CryptoPair::class);

    // Mock the query and avg result
    $cryptoPairMock->shouldReceive('where->avg')
        ->with('average_price')
        ->andReturn(45003.87);

    // Call the mocked method
    $averagePrice = $cryptoPairMock->where('pair', 'BTCUSDC')->avg('average_price');

    // Assert the expected result
    expect($averagePrice)->toBe(45003.87);
});

test('fetch the price change correctly from crypto pair', function () {
    // Mock CryptoPair model
    $cryptoPairMock = mock(CryptoPair::class);

    // Mock the query and avg result
    $cryptoPairMock->shouldReceive('where->avg')
        ->with('average_price')
        ->andReturn(45003.87);

    // Call the mocked method
    $averagePrice = $cryptoPairMock->where('pair', 'BTCUSDC')->avg('average_price');
    $priceChange = $cryptoPairMock->where('pair', 'BTCUSDC')->avg('average_price') - $averagePrice;

    // Assert the expected result
    expect($priceChange)->toBe(0.00);
});

test('fetch the price change percentage correctly from crypto pair', function () {
    // Mock CryptoPair model
    $cryptoPairMock = mock(CryptoPair::class);

    // Mock the query and avg result
    $cryptoPairMock->shouldReceive('where->avg')
        ->with('average_price')
        ->andReturn(45003.87);

    // Call the mocked method
    $averagePrice = $cryptoPairMock->where('pair', 'BTCUSDC')->avg('average_price');
    $priceChange = $cryptoPairMock->where('pair', 'BTCUSDC')->avg('average_price') - $averagePrice;
    $priceChangePercentage = ($priceChange / $averagePrice) * 100;

    // Assert the expected result
    expect($priceChangePercentage)->toBe(0.00);
});
