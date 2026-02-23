<?php

namespace App\Livewire;

use Livewire\Component;

class AddCharity extends Component
{
    public $show = false;
    
    public function render()
    {
        return view('livewire.add-charity');
    }

    public function search()
    {}
    
}
