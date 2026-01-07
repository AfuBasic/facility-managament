<?php

namespace App\Repositories;

use App\Models\ClientMembership;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MembershipRepository
{
    /**
     * Get paginated memberships for a client with optional search
     */
    public function getPaginatedForClient(
        int $clientAccountId,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return ClientMembership::where('client_account_id', $clientAccountId)
            ->with(['user', 'user.roles'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->whereHas('user.roles', function($q) {
                $q->where('name', '!=', 'admin');
            })
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find membership by ID
     */
    public function findById(int $id): ?ClientMembership
    {
        return ClientMembership::find($id);
    }

    /**
     * Check if user is already a member
     */
    public function isUserMember(int $userId, int $clientAccountId): bool
    {
        return ClientMembership::where('user_id', $userId)
            ->where('client_account_id', $clientAccountId)
            ->exists();
    }
}
