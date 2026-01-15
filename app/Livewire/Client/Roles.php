<?php

namespace App\Livewire\Client;

use App\Actions\Client\Roles\CreateRole;
use App\Actions\Client\Roles\DeleteRole;
use App\Actions\Client\Roles\UpdateRole;
use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('components.layouts.client-app')]
#[Title('Roles & Permissions | Optima FM')]
class Roles extends Component
{
    use WithNotifications, WithPagination;

    public $showModal = false;

    public $isEditing = false;

    public $editingRoleId;

    public $name;

    public $selectedPermissions = [];

    public $clientAccountId;

    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'selectedPermissions' => 'required|array|min:1',
    ];

    public function hydrate()
    {
        if ($this->clientAccountId) {
            setPermissionsTeamId($this->clientAccountId);
        }
    }

    public function mount()
    {
        $this->authorize('view roles');
        $this->seedPermissionsIfNeeded();
        $this->clientAccountId = app(ClientAccount::class)->id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['name', 'selectedPermissions', 'isEditing', 'editingRoleId']);
        $this->showModal = true;
    }

    public function edit($id)
    {
        $role = $this->roleRepo()->findForClient($id, $this->clientAccountId);

        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(CreateRole $createRole, UpdateRole $updateRole)
    {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('edit roles');
            $role = $this->roleRepo()->findForClient($this->editingRoleId, $this->clientAccountId);
            $updateRole->execute($role, $this->name, $this->selectedPermissions);
        } else {
            $this->authorize('create roles');
            $createRole->execute($this->name, $this->selectedPermissions, $this->clientAccountId);
        }

        $this->showModal = false;
        $this->success('Role saved successfully!');
    }

    public function delete($id, DeleteRole $deleteRole)
    {
        $this->authorize('delete roles');

        $role = $this->roleRepo()->findForClient($id, $this->clientAccountId);
        $deleteRole->execute($role);

        $this->success('Role deleted successfully.');
    }

    public function toggleSelectAll()
    {
        $allPermissions = Permission::all()->pluck('name')->toArray();

        $this->selectedPermissions = count($this->selectedPermissions) === count($allPermissions)
            ? []
            : $allPermissions;
    }

    public function toggleGroup($groupName)
    {
        $groupPermissions = $this->getGroupPermissions($groupName);
        $intersect = array_intersect($this->selectedPermissions, $groupPermissions);

        if (count($intersect) === count($groupPermissions)) {
            // Deselect group
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, $groupPermissions)
            );
        } else {
            // Select group
            $this->selectedPermissions = array_values(
                array_unique(array_merge($this->selectedPermissions, $groupPermissions))
            );
        }
    }

    public function render()
    {
        return view('livewire.client.roles.index', [
            'roles' => $this->roleRepo()->getPaginatedForClient($this->clientAccountId, $this->search),
            'groupedPermissions' => $this->roleRepo()->getGroupedPermissions(),
        ]);
    }

    /**
     * Get RoleRepository instance
     */
    private function roleRepo(): RoleRepository
    {
        return app(RoleRepository::class);
    }

    /**
     * Get permissions for a specific group
     */
    private function getGroupPermissions(string $groupName): array
    {
        return Permission::all()
            ->filter(function ($perm) use ($groupName) {
                $parts = explode(' ', $perm->name);
                $module = count($parts) > 1 ? ucfirst($parts[1]) : 'Other';

                return $module === $groupName;
            })
            ->pluck('name')
            ->toArray();
    }

    /**
     * Seed permissions if they don't exist
     */
    private function seedPermissionsIfNeeded(): void
    {
        if (Permission::count() === 0) {
            Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);
        }
    }
}
