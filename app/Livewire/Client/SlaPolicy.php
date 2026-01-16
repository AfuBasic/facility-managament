<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\SlaPolicy as SlaPolicyModel;
use App\Models\SlaPolicyRule;
use App\Repositories\SlaPolicyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('SLA Policies | Optima FM')]
class SlaPolicy extends Component
{
    use WithNotifications, WithPagination;

    public $showModal = false;

    public $isEditing = false;

    public $editingPolicyId;

    public $name = '';

    public $description = '';

    public $isDefault = false;

    public $businessHoursOnly = true;

    public $isActive = true;

    public $search = '';

    public ClientAccount $clientAccount;

    // SLA Rules (by priority)
    public $rules = [
        'critical' => ['response' => 60, 'resolution' => 240],
        'high' => ['response' => 240, 'resolution' => 1440],
        'medium' => ['response' => 480, 'resolution' => 4320],
        'low' => ['response' => 1440, 'resolution' => 10080],
    ];

    protected $validationRules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'rules.critical.response' => 'required|integer|min:1',
        'rules.critical.resolution' => 'required|integer|min:1',
        'rules.high.response' => 'required|integer|min:1',
        'rules.high.resolution' => 'required|integer|min:1',
        'rules.medium.response' => 'required|integer|min:1',
        'rules.medium.resolution' => 'required|integer|min:1',
        'rules.low.response' => 'required|integer|min:1',
        'rules.low.resolution' => 'required|integer|min:1',
    ];

    protected $validationAttributes = [
        'rules.critical.response' => 'critical response time',
        'rules.critical.resolution' => 'critical resolution time',
        'rules.high.response' => 'high response time',
        'rules.high.resolution' => 'high resolution time',
        'rules.medium.response' => 'medium response time',
        'rules.medium.resolution' => 'medium resolution time',
        'rules.low.response' => 'low response time',
        'rules.low.resolution' => 'low resolution time',
    ];

    public function hydrate()
    {
        if ($this->clientAccount->id) {
            setPermissionsTeamId($this->clientAccount->id);
        }
    }

    public function mount()
    {
        Gate::authorize('view sla policy');
        $this->clientAccount = app(ClientAccount::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset([
            'name', 'description', 'isDefault', 'businessHoursOnly',
            'isActive', 'isEditing', 'editingPolicyId',
        ]);
        $this->rules = [
            'critical' => ['response' => 60, 'resolution' => 240],
            'high' => ['response' => 240, 'resolution' => 1440],
            'medium' => ['response' => 480, 'resolution' => 4320],
            'low' => ['response' => 1440, 'resolution' => 10080],
        ];
        $this->showModal = true;
    }

    public function edit($id)
    {
        $policy = $this->slaPolicyRepo()->findOrFailForClient($id, $this->clientAccount->id);

        $this->editingPolicyId = $policy->id;
        $this->name = $policy->name;
        $this->description = $policy->description;
        $this->isDefault = $policy->is_default;
        $this->businessHoursOnly = $policy->business_hours_only;
        $this->isActive = $policy->is_active;

        // Load rules
        $this->rules = [
            'critical' => ['response' => 60, 'resolution' => 240],
            'high' => ['response' => 240, 'resolution' => 1440],
            'medium' => ['response' => 480, 'resolution' => 4320],
            'low' => ['response' => 1440, 'resolution' => 10080],
        ];

        foreach ($policy->rules as $rule) {
            $this->rules[$rule->priority] = [
                'response' => $rule->response_time_minutes,
                'resolution' => $rule->resolution_time_minutes,
            ];
        }

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate($this->validationRules, [], $this->validationAttributes);

        DB::transaction(function () {
            if ($this->isEditing) {
                $this->authorize('edit sla policy');
                $policy = SlaPolicyModel::where('client_account_id', $this->clientAccount->id)
                    ->findOrFail($this->editingPolicyId);

                // If setting as default, unset other defaults
                if ($this->isDefault && ! $policy->is_default) {
                    SlaPolicyModel::where('client_account_id', $this->clientAccount->id)
                        ->where('id', '!=', $policy->id)
                        ->update(['is_default' => false]);
                }

                $policy->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_default' => $this->isDefault,
                    'business_hours_only' => $this->businessHoursOnly,
                    'is_active' => $this->isActive,
                ]);

                // Update rules
                foreach ($this->rules as $priority => $times) {
                    SlaPolicyRule::updateOrCreate(
                        ['sla_policy_id' => $policy->id, 'priority' => $priority],
                        [
                            'response_time_minutes' => $times['response'],
                            'resolution_time_minutes' => $times['resolution'],
                        ]
                    );
                }
            } else {
                $this->authorize('create sla policy');

                // Check if this is the first policy for the client - make it default automatically
                $existingPoliciesCount = $this->slaPolicyRepo()->countForClient($this->clientAccount->id);
                $shouldBeDefault = $existingPoliciesCount === 0 || $this->isDefault;

                // If setting as default, unset other defaults
                if ($shouldBeDefault && $existingPoliciesCount > 0) {
                    $this->slaPolicyRepo()->unsetDefaultsForClient($this->clientAccount->id);
                }

                $policy = SlaPolicyModel::create([
                    'client_account_id' => $this->clientAccount->id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'is_default' => $shouldBeDefault,
                    'business_hours_only' => $this->businessHoursOnly,
                    'is_active' => $this->isActive,
                ]);

                // Create rules
                foreach ($this->rules as $priority => $times) {
                    SlaPolicyRule::create([
                        'sla_policy_id' => $policy->id,
                        'priority' => $priority,
                        'response_time_minutes' => $times['response'],
                        'resolution_time_minutes' => $times['resolution'],
                    ]);
                }
            }
        });

        $this->showModal = false;
        $this->success('SLA Policy saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('delete sla policy');

        $policy = SlaPolicyModel::where('client_account_id', $this->clientAccount->id)
            ->findOrFail($id);

        // Check if policy is in use
        $workOrderCount = $policy->workOrders()->count();
        if ($workOrderCount > 0) {
            $this->error("Cannot delete policy. It is used by {$workOrderCount} work orders.");

            return;
        }

        $policy->delete();
        $this->success('SLA Policy deleted successfully.');
    }

    public function toggleDefault($id)
    {
        $this->authorize('edit sla policy');

        DB::transaction(function () use ($id) {
            $this->slaPolicyRepo()->setAsDefault($id, $this->clientAccount->id);
        });

        $this->success('Default SLA Policy updated.');
    }

    public function render()
    {
        return view('livewire.client.sla-policy.index', [
            'policies' => $this->slaPolicyRepo()->getPaginatedForClient(
                $this->clientAccount->id,
                $this->search
            ),
        ]);
    }

    /**
     * Get SlaPolicyRepository instance.
     */
    private function slaPolicyRepo(): SlaPolicyRepository
    {
        return app(SlaPolicyRepository::class);
    }

    /**
     * Format minutes to human readable string.
     */
    public function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            return "{$hours}h";
        }

        $days = floor($hours / 24);

        return "{$days}d";
    }
}
