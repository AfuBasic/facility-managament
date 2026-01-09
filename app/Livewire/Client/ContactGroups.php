<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\ContactGroup;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('Contact Groups | Optima FM')]
class ContactGroups extends Component
{
    use WithPagination, WithNotifications;

    public $showModal = false;
    public $isEditing = false;
    public $editingGroupId;
    public $name = '';
    public $status = 'active';
    public $clientAccountId;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'status' => 'required|in:active,inactive',
    ];

    public function hydrate()
    {
        if ($this->clientAccountId) {
            setPermissionsTeamId($this->clientAccountId);
        }
    }

    public function mount()
    {
        $this->authorize('view contacts');
        $this->clientAccountId = app(ClientAccount::class)->id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create contacts');
        $this->reset(['name', 'status', 'isEditing', 'editingGroupId']);
        $this->status = 'active';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit contacts');
        
        $group = ContactGroup::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        
        $this->editingGroupId = $group->id;
        $this->name = $group->name;
        $this->status = $group->status;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('edit contacts');
            $group = ContactGroup::where('client_account_id', $this->clientAccountId)->findOrFail($this->editingGroupId);
            $group->update([
                'name' => $this->name,
                'status' => $this->status,
            ]);
            $this->success('Contact group updated successfully!');
        } else {
            $this->authorize('create contacts');
            ContactGroup::create([
                'name' => $this->name,
                'status' => $this->status,
            ]);
            $this->success('Contact group created successfully!');
        }

        $this->showModal = false;
        $this->reset(['name', 'status', 'isEditing', 'editingGroupId']);
    }

    public function delete($id)
    {
        $this->authorize('delete contacts');
        
        $group = ContactGroup::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $group->delete();
        
        $this->success('Contact group deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit contacts');
        
        $group = ContactGroup::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $group->update([
            'status' => $group->status === 'active' ? 'inactive' : 'active'
        ]);
        
        $this->success('Status updated successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'status', 'isEditing', 'editingGroupId']);
    }

    public function render()
    {
        $groups = ContactGroup::where('client_account_id', $this->clientAccountId)
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.client.contact-groups', [
            'groups' => $groups
        ]);
    }
}
