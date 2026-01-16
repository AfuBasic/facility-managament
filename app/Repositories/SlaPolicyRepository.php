<?php

namespace App\Repositories;

use App\Models\SlaPolicy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SlaPolicyRepository
{
    /**
     * Get paginated SLA policies for a client with optional search.
     */
    public function getPaginatedForClient(
        int $clientAccountId,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return SlaPolicy::where('client_account_id', $clientAccountId)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->with('rules')
            ->withCount('workOrders')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get all active SLA policies for a client.
     */
    public function getAllActiveForClient(int $clientAccountId): Collection
    {
        return SlaPolicy::where('client_account_id', $clientAccountId)
            ->active()
            ->with('rules')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find SLA policy by ID for a specific client.
     */
    public function findForClient(int $policyId, int $clientAccountId): ?SlaPolicy
    {
        return SlaPolicy::where('client_account_id', $clientAccountId)
            ->with('rules')
            ->find($policyId);
    }

    /**
     * Find SLA policy by ID for a specific client (throws exception if not found).
     */
    public function findOrFailForClient(int $policyId, int $clientAccountId): SlaPolicy
    {
        return SlaPolicy::where('client_account_id', $clientAccountId)
            ->with('rules')
            ->findOrFail($policyId);
    }

    /**
     * Get the default SLA policy for a client.
     */
    public function getDefaultForClient(int $clientAccountId): ?SlaPolicy
    {
        return SlaPolicy::where('client_account_id', $clientAccountId)
            ->active()
            ->default()
            ->with('rules')
            ->first();
    }

    /**
     * Count SLA policies for a client.
     */
    public function countForClient(int $clientAccountId): int
    {
        return SlaPolicy::where('client_account_id', $clientAccountId)->count();
    }

    /**
     * Unset all default policies for a client.
     */
    public function unsetDefaultsForClient(int $clientAccountId): void
    {
        SlaPolicy::where('client_account_id', $clientAccountId)
            ->update(['is_default' => false]);
    }

    /**
     * Set a policy as default for a client.
     */
    public function setAsDefault(int $policyId, int $clientAccountId): void
    {
        $this->unsetDefaultsForClient($clientAccountId);

        SlaPolicy::where('client_account_id', $clientAccountId)
            ->where('id', $policyId)
            ->update(['is_default' => true]);
    }
}
