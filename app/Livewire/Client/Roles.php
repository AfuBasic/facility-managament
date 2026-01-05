<?php

namespace App\Livewire\Client;

use App\Models\ClientAccount;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.client-app')]
#[Title('Roles | Optima FM')]
class Roles extends Component
{

    public $roles;
    
    #[Rule('required|min:3')]
    public $name = '';
    
    public string | null $clientAccountId = null;

    public $selectedPermissions = [];
    public $isEditing = false;
    public $showModal = false;
    public $editingRoleId;

    public function mount()
    {
        Gate::authorize('view roles');
        $this->clientAccountId = app(ClientAccount::class)->id;
        $this->loadRoles();
    }

    public function loadRoles()
    {
        // Fetch roles scoped to the current client
        $this->roles = Role::where('client_account_id', $this->clientAccountId)
            ->where('name', '!=', 'admin')
            ->with('permissions')
            ->get();
    }

    public function create()
    {
        $this->reset(['name', 'selectedPermissions', 'isEditing', 'editingRoleId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $role = Role::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        
        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $role = Role::where('client_account_id', $this->clientAccountId)->findOrFail($this->editingRoleId);
            $role->update(['name' => $this->name]);
        } else {
            // Create role scoped to this client/team
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
                'client_account_id' => app(ClientAccount::class)->id
            ]);
        }

        // Sync permissions
        $role->syncPermissions($this->selectedPermissions);

        $this->showModal = false;
        $this->loadRoles();
        
        $this->dispatch('toast', message: 'Role saved successfully!', type: 'success');
    }

    public function delete($id)
    {
        $role = Role::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $role->delete();
        
        $this->loadRoles();
        $this->dispatch('toast', message: 'Role deleted successfully.', type: 'success');
    }

    public function render()
    {
        // Group permissions by module for the UI
        $permissions = Permission::all()
            ->groupBy(function($perm) {
                $parts = explode(' ', $perm->name);
                return count($parts) > 1 ? ucfirst($parts[1]) : 'Other';
            });

        return view('livewire.client.roles.index', [
            'groupedPermissions' => $permissions
        ]);
    }
}
