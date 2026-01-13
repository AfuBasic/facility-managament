<?php

namespace App\Livewire\Client;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\ClientAccount;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
class AssetDetail extends Component
{
    public Asset $asset;
    public ClientAccount $clientAccount;
    
    public $activeTab = 'details'; // details, history, assignments
    
    // Action State
    public $actionType = null; // restock, checkout, checkin
    public $quantity = 1;
    public $cost = null;
    public $targetUserId = null;
    public $targetSpaceId = null;
    public $selectedAssignmentId = null;
    public $notes = '';
    public $condition = 'Good';
    
    public function mount(Asset $asset)
    {
        $this->asset = $asset->load(['facility', 'store', 'user', 'assignedUser', 'supplierContact', 'space', 'images', 'assignments.user', 'assignments.space']);
        $this->clientAccount = app(ClientAccount::class);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->actionType = null; // Reset action when switching tabs
    }
    
    public function setAction($type)
    {
        $this->actionType = $type;
        $this->resetActionForm();
    }

    public function resetActionForm()
    {
        $this->quantity = 1;
        $this->cost = null;
        $this->targetUserId = null;
        $this->targetSpaceId = null;
        $this->selectedAssignmentId = null;
        $this->notes = '';
        $this->condition = 'Good';
    }

    public function getHistoryProperty()
    {
        return AssetHistory::where('asset_id', $this->asset->id)
            ->with(['performedBy', 'targetUser', 'space'])
            ->latest()
            ->get();
    }

    public function getUserOptionsProperty()
    {
        return User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->latest()
            ->pluck('name', 'id');
    }

    public function getSpaceOptionsProperty()
    {
        return Space::where('facility_id', $this->asset->facility_id)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function getAvailableUnitsProperty()
    {
        $assigned = $this->asset->assignments()->sum('quantity');
        return $this->asset->units - $assigned;
    }

    public function updatedSelectedAssignmentId($value)
    {
        if ($value) {
            $assignment = AssetAssignment::find($value);
            if ($assignment) {
                $this->quantity = $assignment->quantity;
            }
        }
    }

    public function submitAction()
    {
        try {
            DB::transaction(function () {
                if ($this->actionType === 'restock') {
                    $this->validate([
                        'quantity' => 'required|integer|min:1',
                        'cost' => 'nullable|numeric|min:0',
                    ]);

                    $this->asset->increment('units', $this->quantity);
                    $this->logHistory('restock', $this->quantity, $this->cost);
                    
                    session()->flash('success', 'Asset restocked successfully.');

                } elseif ($this->actionType === 'checkout') {
                    $this->validate([
                        'quantity' => 'required|integer|min:1|max:' . $this->availableUnits,
                        'targetUserId' => 'required|exists:users,id',
                    ]);
                    
                    // Validate space based on asset type
                    if (in_array($this->asset->type, ['fixed', 'consumable'])) {
                        $this->validate(['targetSpaceId' => 'required|exists:spaces,id']);
                    }

                    if ($this->asset->type === 'consumable') {
                        // Consumables: Decrement units directly
                        $this->asset->decrement('units', $this->quantity);
                        $this->logHistory('checkout', $this->quantity, null, $this->notes, $this->targetSpaceId);
                    } else {
                        // Fixed/Tools: Create assignment
                        AssetAssignment::create([
                            'asset_id' => $this->asset->id,
                            'user_id' => $this->targetUserId,
                            'space_id' => $this->targetSpaceId,
                            'quantity' => $this->quantity,
                            'checked_out_at' => now(),
                            'notes' => $this->notes,
                        ]);
                        $this->logHistory('checkout', $this->quantity, null, $this->notes, $this->targetSpaceId);
                    }
                    
                    session()->flash('success', 'Asset checked out successfully.');

                } elseif ($this->actionType === 'checkin') {
                    $this->validate([
                        'selectedAssignmentId' => 'required|exists:asset_assignments,id',
                        'quantity' => 'required|integer|min:1',
                        'condition' => 'required|in:Good,Fair,Damaged,Needs Repair',
                    ]);

                    $assignment = AssetAssignment::find($this->selectedAssignmentId);
                    
                    // Validate that return quantity doesn't exceed checked out quantity
                    if ($this->quantity > $assignment->quantity) {
                        throw new \Exception("Cannot return {$this->quantity} units. Only {$assignment->quantity} units were checked out.");
                    }
                    
                    if ($assignment->quantity <= $this->quantity) {
                        $assignment->delete();
                    } else {
                        $assignment->decrement('quantity', $this->quantity);
                    }
                    
                    $this->logHistory('checkin', $this->quantity, null, "Condition: {$this->condition}. {$this->notes}");
                    session()->flash('success', 'Asset checked in successfully.');
                }
            });

            $this->asset->refresh();
            $this->actionType = null;
            $this->setTab('history');
            
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
            Log::error('Asset action failed', [
                'action' => $this->actionType,
                'asset_id' => $this->asset->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function logHistory($action, $qty, $cost = null, $notes = null, $spaceId = null)
    {
        AssetHistory::create([
            'asset_id' => $this->asset->id,
            'action_type' => $action,
            'quantity' => $qty,
            'performed_by_user_id' => Auth::id(),
            'target_user_id' => $this->targetUserId,
            'space_id' => $spaceId,
            'cost_per_unit' => $cost,
            'note' => $notes,
        ]);
    }

    public function render()
    {
        return view('livewire.client.asset-detail');
    }
}
