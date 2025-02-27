<?php

namespace App\Http\Livewire;

use App\Models\CryptoPair;
use Livewire\Component;

class CryptoPrice extends Component
{
    public $cryptoPairs = [];

    protected $listeners = [
        'echo:crypto-prices,CryptoPriceUpdated' => 'refreshCryptoPairs',
        'CryptoPriceUpdated' => 'refreshCryptoPairs'
    ];

    public function mount()
    {
        $this->fetchCryptoPairs();
    }

    public function refreshCryptoPairs($event = null)
    {
        logger('Crypto price update received', ['event' => $event]);
        $this->fetchCryptoPairs();
    }

    private function fetchCryptoPairs()
    {
        $this->cryptoPairs = CryptoPair::orderBy('last_updated', 'desc')->get();

        // Log fetched data for debugging
        logger('Fetched crypto pairs', ['count' => count($this->cryptoPairs)]);
    }

    public function render()
    {
        return view('livewire.crypto-price');
    }
}
