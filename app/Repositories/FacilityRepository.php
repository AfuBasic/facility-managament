<?php

namespace App\Repositories;

use App\Models\Facility;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FacilityRepository
{
    /**
     * Get paginated facilities for a client with optional search
     */
    public function getPaginatedForClient(
        int $clientAccountId,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Facility::with('users')
            ->forClient($clientAccountId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->withCount('users')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get paginated facilities assigned to a specific user
     */
    public function getPaginatedForUser(
        int $userId,
        int $clientAccountId,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Facility::with('users')
            ->forClient($clientAccountId)
            ->assignedToUser($userId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->withCount('users')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find facility by ID for a specific client
     */
    public function findForClient(int $facilityId, int $clientAccountId): ?Facility
    {
        return Facility::forClient($clientAccountId)
            ->with('users')
            ->find($facilityId);
    }

    /**
     * Get all facilities for a client
     */
    public function getAllForClient(int $clientAccountId): Collection
    {
        return Facility::with('users')
            ->forClient($clientAccountId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get users assigned to a facility
     */
    public function getUsersForFacility(int $facilityId): Collection
    {
        $facility = Facility::with('users')->find($facilityId);
        return $facility ? $facility->users : collect();
    }
}
