<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\ClientAccount;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Facility;
use App\Models\Space;
use App\Models\Store;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Log;

class QueryInterpreter
{
    /**
     * Allowed models for querying
     */
    protected array $allowedModels = [
        'Asset' => Asset::class,
        'WorkOrder' => WorkOrder::class,
        'Facility' => Facility::class,
        'Store' => Store::class,
        'Event' => Event::class,
        'Contact' => Contact::class,
        'Space' => Space::class,
        'User' => User::class,
    ];

    /**
     * Execute a query based on AI instructions
     */
    public function execute(array $queryInstructions): array
    {
        try {
            $modelName = $queryInstructions['model'] ?? null;

            if (! $modelName || ! isset($this->allowedModels[$modelName])) {
                throw new \Exception("Invalid or disallowed model: {$modelName}");
            }

            $modelClass = $this->allowedModels[$modelName];
            $query = $modelClass::query();

            // IMPORTANT: Always filter by current client for data isolation
            $currentClient = app(ClientAccount::class);

            // If the bound instance has no ID, fallback to session
            if ($currentClient && ! $currentClient->id) {
                $clientId = session('current_client_account_id');
                if ($clientId) {
                    $currentClient = ClientAccount::find($clientId);
                }
            }

            if ($currentClient && $currentClient->id) {
                $query->where('client_account_id', $currentClient->id);
            }

            // Apply where clauses
            if (isset($queryInstructions['query_instructions']['where'])) {
                foreach ($queryInstructions['query_instructions']['where'] as $where) {
                    $query->where(
                        $where['field'],
                        $where['operator'] ?? '=',
                        $where['value']
                    );
                }
            }

            // Apply whereHas for relationship filtering (e.g., "stores in Facility A")
            if (isset($queryInstructions['query_instructions']['whereHas'])) {
                foreach ($queryInstructions['query_instructions']['whereHas'] as $whereHas) {
                    $relation = $whereHas['relation'] ?? null;
                    if ($relation) {
                        $query->whereHas($relation, function ($q) use ($whereHas) {
                            $q->where(
                                $whereHas['field'],
                                $whereHas['operator'] ?? '=',
                                $whereHas['value']
                            );
                        });
                    }
                }
            }

            // Apply eager loading
            if (isset($queryInstructions['query_instructions']['with'])) {
                $query->with($queryInstructions['query_instructions']['with']);
            }

            // Apply ordering
            if (isset($queryInstructions['query_instructions']['orderBy'])) {
                $orderBy = $queryInstructions['query_instructions']['orderBy'];
                $query->orderBy(
                    $orderBy['field'],
                    $orderBy['direction'] ?? 'asc'
                );
            }

            // Apply limit
            $limit = $queryInstructions['query_instructions']['limit'] ?? 10;
            $query->limit(min($limit, 100)); // Max 100 results

            // Execute query
            $results = $query->get();

            return [
                'success' => true,
                'data' => $results,
                'count' => $results->count(),
                'model' => $modelName,
            ];
        } catch (\Exception $e) {
            Log::error('Query Interpreter Error', [
                'instructions' => $queryInstructions,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
