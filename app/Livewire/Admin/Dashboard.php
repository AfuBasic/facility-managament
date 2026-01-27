<?php

namespace App\Livewire\Admin;

use App\Models\ClientAccount;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard | Admin')]
class Dashboard extends Component
{
    public int $totalUsers = 0;

    public int $newUsersToday = 0;

    public int $totalClients = 0;

    public int $totalWorkOrders = 0;

    /** @var array<int, int> */
    public array $usersPerDay = [];

    public function mount(): void
    {
        $this->totalUsers = User::count();
        $this->newUsersToday = User::whereDate('created_at', today())->count();
        $this->totalClients = ClientAccount::count();
        $this->totalWorkOrders = WorkOrder::count();

        // Users registered per day for the last 7 days
        $this->usersPerDay = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
