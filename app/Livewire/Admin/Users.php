<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Users | Admin')]
class Users extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function suspend(User $user): void
    {
        $user->suspended_at = now();
        $user->save();
        $this->dispatch('toast', message: 'User suspended successfully.', type: 'success');
    }

    public function activate(User $user): void
    {
        $user->suspended_at = null;
        $user->save();
        $this->dispatch('toast', message: 'User activated successfully.', type: 'success');
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->withCount('clientMemberships')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
        ]);
    }
}
