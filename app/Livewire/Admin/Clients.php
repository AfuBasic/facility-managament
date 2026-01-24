<?php

namespace App\Livewire\Admin;

use App\Models\ClientAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Organizations | Admin')]
class Clients extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $clients = ClientAccount::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->withCount('memberships')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.clients', [
            'clients' => $clients,
        ]);
    }
}
