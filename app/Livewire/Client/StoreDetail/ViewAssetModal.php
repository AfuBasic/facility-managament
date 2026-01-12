<?php

namespace App\Livewire\Client\StoreDetail;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class ViewAssetModal extends Component
{
    public ?Asset $asset = null;
    public $showModal = false;
    public $activeTab = 'details'; // details, history

    // Action State
    public $actionType = null; // restock, checkout, checkin
    public $quantity = 1;
    public $cost = null;
    public $targetUserId = null;
    public $notes = '';
    public $condition = 'Good'; // For check-in

    // Searchable Selects
    public $usersSearch = '';
    
    #[On('open-view-asset-modal')]
    public function openModal($assetId)
    {
        Log::info('ViewAssetModal: Opening for Asset', ['id' => $assetId]);
        $this->asset = Asset::with(['facility', 'store', 'user', 'supplierContact', 'images'])->find($assetId);
        
        if (!$this->asset) {
            $this->dispatch('notify', variant: 'error', message: 'Asset not found.');
            return;
        }

        $this->showModal = true;
        $this->activeTab = 'details';
        $this->resetActionForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetActionForm();
        $this->asset = null;
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

    public function getUsersProperty()
    {
        // Simple user search for checkout
        // In a real app, this might need optimizing for large user bases
        return User::where('name', 'like', '%' . $this->usersSearch . '%')
            ->take(10)
            ->get();
    }

    public function submitAction()
    {
        if (!$this->asset) return;

        $this->validate([
            'notes' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1',
        ]);

        $previousState = $this->asset->toArray();

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

            if ($this->asset->type === 'consumable') {
                if ($this->asset->units < $this->quantity) {
                    $this->addError('quantity', 'Not enough units available.');
                    return;
                }
                $this->asset->decrement('units', $this->quantity);
            } else {
                // Fixed/Tools - Assign the asset
                if ($this->asset->units < 1) {
                     $this->addError('quantity', 'Asset is not available.');
                     return;
                }
                
                // For serialized items, we usually just assign the whole item.
                // Assuming 1 unit for serialized checkout for now.
                $this->asset->update([
                    'user_id' => $this->targetUserId,
                    // If we had a specific status column on asset, we'd update it here.
                    // For now, user_id present means it's assigned.
                ]);
            }
            
            $this->logHistory('checkout', $this->quantity);
            $this->dispatch('notify', variant: 'success', message: 'Asset checked out successfully.');

        } elseif ($this->actionType === 'checkin') {
            
            if ($this->asset->type !== 'consumable') {
                $this->asset->update(['user_id' => Auth::id()]); // Assign back to store manager/auth user? or null?
                // Usually null or the store manager. Let's set to null (unassigned) for now
                // OR better, set to the current user (Owner) as per previous logic.
                $this->asset->update(['user_id' => Auth::id()]);
            }
             // For consumables, check-in isn't really a thing unless returning unused items.
             
            $this->logHistory('checkin', $this->quantity, null, 'Condition: ' . $this->condition);
            $this->dispatch('notify', variant: 'success', message: 'Asset checked in successfully.');
        }

        $this->asset->refresh();
        $this->actionType = null;
        $this->setTab('history'); // Switch to history to see the log
        
        // Refresh parent
        $this->dispatch('refresh-asset-list'); 
    }

    private function logHistory($type, $units, $cost = null, $extraNotes = '')
    {
        AssetHistory::create([
            'asset_id' => $this->asset->id,
            'action_type' => $type,
            'performed_by_user_id' => Auth::id(),
            'target_user_id' => $this->targetUserId,
            'units' => $units,
            'cost_per_unit' => $cost,
            'note' => trim($this->notes . ' ' . $extraNotes),
            'previous_state' => null, // Simplified for now
        ]);
    }

    public function render()
    {
        return view('livewire.client.store-detail.view-asset-modal');
    }
}
