<?php

namespace App\Livewire\Client;

use App\Actions\Client\Roles\CreateRole;
use App\Actions\Client\Roles\DeleteRole;
use App\Actions\Client\Roles\SeedPermissions;
use App\Actions\Client\Roles\UpdateRole;
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
        $this->authorize('view roles');

        //Check if permissions have been seeded, if not, seed them
        $permissions = Permission::all();
        if($permissions->isEmpty()) {
            (new SeedPermissions())->execute();
        }
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

    public function save(
        CreateRole $createRole,
        UpdateRole $updateRole
    ) {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('edit roles');
            $role = Role::where('client_account_id', $this->clientAccountId)->findOrFail($this->editingRoleId);
            $updateRole->execute($role, $this->name, $this->selectedPermissions);
        } else {
            $this->authorize('create roles');
            $createRole->execute($this->name, $this->selectedPermissions, $this->clientAccountId);
        }

        $this->showModal = false;
        $this->loadRoles();
        
        $this->dispatch('toast', message: 'Role saved successfully!', type: 'success');
    }

    public function delete($id, DeleteRole $deleteRole)
    {
        $this->authorize('delete roles');
        $role = Role::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $deleteRole->execute($role);
        
        $this->loadRoles();
        $this->dispatch('toast', message: 'Role deleted successfully.', type: 'success');
    }

    public function toggleSelectAll()
    {
        // If all are selected, deselect all. Otherwise, select all.
        $allPermissions = Permission::all()->pluck('name')->toArray();
        
        if (count($this->selectedPermissions) === count($allPermissions)) {
            $this->selectedPermissions = [];
        } else {
            $this->selectedPermissions = $allPermissions;
        }
    }

    public function toggleGroup($groupName)
    {
        // Get all permissions for this group
        // We need to replicate the grouping logic or filter existing permissions
        // Replicating logic: simple string matching based on the convention "action module"
        
        $groupPermissions = Permission::all()->filter(function($perm) use ($groupName) {
            $parts = explode(' ', $perm->name);
            $module = count($parts) > 1 ? ucfirst($parts[1]) : 'Other';
            return $module === $groupName;
        })->pluck('name')->toArray();
        
        $intersect = array_intersect($this->selectedPermissions, $groupPermissions);
        
        if (count($intersect) === count($groupPermissions)) {
            // All selected, so deselect this group
            $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissions);
        } else {
            // Not all selected, so select all in group (merge)
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissions));
        }
        
        // Re-index array to be safe
        $this->selectedPermissions = array_values($this->selectedPermissions);
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
