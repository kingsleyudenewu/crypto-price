@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Crypto Price Tracker</h1>
    <div class="mb-4">
        @livewire('live-clock')
    </div>
    @livewire('crypto-price-table')
@endsection
