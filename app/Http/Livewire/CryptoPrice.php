<?php

namespace App\Http\Livewire;

use App\Models\CryptoPair;
use Livewire\Component;

class CryptoPrice extends Component
{
    public $cryptoPairs;

    protected $listeners = ['echo:crypto-prices, CryptoPriceUpdated' => 'updatePrices'];

    public function mount()
    {
        $this->cryptoPairs = CryptoPair::all();
    }

    public function updatePrices($event)
    {
        $updatedPair = $event['cryptoPair'];

        foreach ($this->cryptoPairs as $index => $price) {
            if ($price->pair === $updatedPair['pair']) {
                $this->prices[$index]['average_price'] = $updatedPair['average_price'];
                $this->prices[$index]['last_updated'] = $updatedPair['last_updated'];
                break;
            }
        }
    }

    public function render()
    {
        return view('livewire.crypto-price');
    }
}
