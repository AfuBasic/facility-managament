<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleRepository
{
    /**
     * Get paginated roles for a client with optional search
     */
    public function getPaginatedForClient(
        int $clientAccountId,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Role::where('client_account_id', $clientAccountId)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->where('name', '<>', 'admin')
            ->with('permissions')
            ->paginate($perPage);
    }

    /**
     * Get all roles for a client (excluding admin)
     */
    public function getAllForClient(int $clientAccountId): Collection
    {
        return Role::where('client_account_id', $clientAccountId)
            ->where('name', '!=', 'admin')
            ->get();
    }

    /**
     * Get grouped permissions by module
     */
    public function getGroupedPermissions(): Collection
    {
        return Permission::all()
            ->groupBy(function($perm) {
                $parts = explode(' ', $perm->name);
                return count($parts) > 1 ? ucfirst($parts[1]) : 'Other';
            });
    }

    /**
     * Find role by ID for a specific client
     */
    public function findForClient(int $roleId, int $clientAccountId): ?Role
    {
        return Role::where('client_account_id', $clientAccountId)
            ->find($roleId);
    }
}
