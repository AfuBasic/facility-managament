<?php

namespace App\Livewire\Client\StoreDetail;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetHistory;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ViewAssetModal extends Component
{
    public ?Asset $asset = null;
    public $showModal = false;
    public $activeTab = 'details'; // details, history

    #[Url(as: 'view_asset')]
    public $urlAssetId = '';

    // Action State
    public $actionType = null; // restock, checkout, checkin
    public $quantity = 1;
    public $cost = null;
    public $targetUserId = null;
    public $notes = '';
    public $condition = 'Good'; // For check-in

    // Searchable Selects
    public $usersSearch = '';
    
    public function mount()
    {
        if ($this->urlAssetId) {
            $this->openModal($this->urlAssetId);
        }
    }

    #[On('open-view-asset-modal')]
    public function openModal($assetId)
    {
        Log::info('ViewAssetModal: Opening for Asset', ['id' => $assetId]);
        $this->asset = Asset::with(['facility', 'store', 'user', 'assignedUser', 'supplierContact', 'images'])->find($assetId);
        
        if (!$this->asset) {
            $this->dispatch('notify', variant: 'error', message: 'Asset not found.');
            $this->urlAssetId = ''; // Reset if invalid
            return;
        }

        $this->urlAssetId = $assetId;
        $this->showModal = true;
        $this->activeTab = 'details';
        $this->resetActionForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetActionForm();
        $this->asset = null;
        $this->urlAssetId = '';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
    
    public function setAction($type)
    {
        $this->actionType = $type;
        $this->resetActionForm();
        
        // Pre-fill logic
        if ($type === 'restock') {
            $this->quantity = 1;
        }
    }

    public function resetActionForm()
    {
        $this->quantity = 1;
        $this->cost = null;
        $this->targetUserId = null;
        $this->notes = '';
        $this->condition = 'Good';
        $this->usersSearch = '';
    }

    public function getHistoryProperty()
    {
        if (!$this->asset) return [];
        
        return AssetHistory::where('asset_id', $this->asset->id)
            ->with(['performedBy', 'targetUser'])
            ->latest()
            ->get();
    }

    public function getUserOptionsProperty()
    {
        return User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })->latest()
            ->pluck('name', 'id');
    }

    public function submitAction()
    {
        if (!$this->asset) return;

        $this->validate([
            'notes' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1',
        ]);

        $previousState = $this->asset->toArray();

        // Common history data
        $spaceIdToLog = null;

        if ($this->actionType === 'restock') {
            $this->validate([
                'cost' => 'nullable|numeric|min:0',
            ]);

            $this->asset->increment('units', $this->quantity);
            $this->logHistory('restock', $this->quantity, $this->cost);
            
            $this->dispatch('notify', variant: 'success', message: 'Asset restocked successfully.');

        } elseif ($this->actionType === 'checkout') {
            $this->validate([
                'targetUserId' => 'required|exists:users,id',
            ]);
            
            // Refined Logic per Asset Type
            if ($this->asset->type === 'fixed') {
                // Fixed: Needs User AND Space
                $this->validate(['targetSpaceId' => 'required|exists:spaces,id']);
                $spaceIdToLog = $this->targetSpaceId;
                
                $this->asset->update([
                    'assigned_to_user_id' => $this->targetUserId,
                    'space_id' => $this->targetSpaceId,
                    'checked_out_at' => now(),
                ]);

            } elseif ($this->asset->type === 'tools') {
               // Tools: Needs User only (Location implicit or handled by user ownership)
               // Validation already checked user above.
               // We DO NOT set space_id on the asset if it's not required to be tracked there, 
               // OR we keep previous space. For now, assume we just assign user.
               
               $this->asset->update([
                    'assigned_to_user_id' => $this->targetUserId,
                    'checked_out_at' => now(),
                    // 'space_id' => null? or keep where it was picked from? usually keep.
                ]);

            } elseif ($this->asset->type === 'consumable') {
                // Consumable: Needs User AND Location
                $this->validate(['targetSpaceId' => 'required|exists:spaces,id']);
                if ($this->asset->units < 1) {
                     $this->addError('quantity', 'Asset is not available.');
                     return;
                }
                
                // Check if enough stock is available (Total - Assigned)
                $assignedCount = $this->asset->assignments()->sum('quantity');
                $available = $this->asset->units - $assignedCount;
                
                if ($available < $this->quantity) {
                    $this->addError('quantity', "Only {$available} units available.");
                    return;
                }

                // Create Assignment
                AssetAssignment::create([
                    'asset_id' => $this->asset->id,
                    'user_id' => $this->targetUserId,
                    'space_id' => $this->targetSpaceId,
                    'quantity' => $this->quantity,
                    'checked_out_at' => now(),
                    'notes' => $this->notes,
                ]);
                
                // Update Asset State (Optional, but good for quick status view)
                $this->asset->update([
                   'checked_out_at' => now(), // Just marks last activity
                ]);
            }
            
            $this->logHistory('checkout', $this->quantity, null, $this->notes, $spaceIdToLog);
            $this->dispatch('notify', variant: 'success', message: 'Asset checked out successfully.');

        } elseif ($this->actionType === 'checkin') {
            
            if ($this->asset->type !== 'consumable') {
                // Find assignment for this user (Wait, we need to know WHICH assignment to return if multiple)
                // For now, let's assume we return ALL from this user or handle logic.
                // Simplified: Find an assignment for this asset/user and decrement.
                
                // TODO: UI should ideally let you select which specific assignment to check in if a user has multiple separate checkouts.
                // For now, we auto-resolve:
                
                $assignment = AssetAssignment::where('asset_id', $this->asset->id)
                    // ->where('user_id', $targetUserId) // We need to select the user returning it!
                    // If actionType is checkin, we usually select the Borrower.
                    // But our UI currently selects "Assigned To" implicitly for single-assigns.
                    // Implementation Detail: We need a "Select Assignment to Return" UI?
                    // OR: "Select User returning item"? 
                    
                    // Let's stick to the simplest flow: User selects "Check In", 
                    // IF asset has assignments, we show a list of WHO has it.
                    
                    ->where('user_id', $this->targetUserId) // We need to bind this input on Checkin too!
                    ->first();
                    
                if ($assignment) {
                    if ($assignment->quantity <= $this->quantity) {
                        $assignment->delete();
                    } else {
                        $assignment->decrement('quantity', $this->quantity);
                    }
                }
                
                $this->asset->update([
                    'last_checked_in_at' => now(),
                ]);
            }
             
            $this->logHistory('checkin', $this->quantity, null, 'Condition: ' . $this->condition . '. ' . $this->notes);
            $this->dispatch('notify', variant: 'success', message: 'Asset checked in successfully.');
        }

        $this->closeModal();
        $this->dispatch('refresh-asset-list'); 
    }

    private function logHistory($action, $qty, $cost = null, $notes = null, $spaceId = null)
    {
        if (!$notes) $notes = $this->notes;
        if (!$cost) $cost = $this->cost;

        AssetHistory::create([
            'asset_id' => $this->asset->id,
            'action_type' => $action,
            'quantity_change' => $action === 'checkin' || $action === 'restock' ? $qty : -$qty,
            'performed_by_user_id' => Auth::id(),
            'target_user_id' => $this->targetUserId,
            'space_id' => $spaceId, // Log where it went
            'cost_per_unit' => $cost,
            'notes' => $notes,
            'previous_state' => null, // Could implement full snapshot here
        ]);
    }

    public function render()
    {
        return view('livewire.client.store-detail.view-asset-modal');
    }
}
