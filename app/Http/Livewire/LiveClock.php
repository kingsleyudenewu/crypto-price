<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LiveClock extends Component
{
    public $time;

    public function mount()
    {
        $this->time = now()->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.live-clock');
    }
}
