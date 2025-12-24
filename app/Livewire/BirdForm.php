<?php

namespace App\Livewire;

use App\Models\Bird;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BirdForm extends Component
{
    public Collection $birds;
    #[Validate('required')]
    public $name;

    #[Validate('required|numeric')]
    public $count;
    public function store()
    {
        $this->validate();
        Bird::create([
            'name' => $this->name,
            'count' => $this->count,
        ]);

        $this->reset();
    }

    public function render()
    {
        $this->birds = Bird::all();
        return view('livewire.bird-form');
    }
}
