<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\ContactType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('Contact Types | Optima FM')]
class ContactTypes extends Component
{
    use WithPagination, WithNotifications;

    public $showModal = false;
    public $isEditing = false;
    public $editingTypeId;
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
        $this->reset(['name', 'status', 'isEditing', 'editingTypeId']);
        $this->status = 'active';
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit contacts');
        
        $type = ContactType::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        
        $this->editingTypeId = $type->id;
        $this->name = $type->name;
        $this->status = $type->status;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->authorize('edit contacts');
            $type = ContactType::where('client_account_id', $this->clientAccountId)->findOrFail($this->editingTypeId);
            $type->update([
                'name' => $this->name,
                'status' => $this->status,
            ]);
            $this->success('Contact type updated successfully!');
        } else {
            $this->authorize('create contacts');
            ContactType::create([
                'name' => $this->name,
                'status' => $this->status,
            ]);
            $this->success('Contact type created successfully!');
        }

        $this->showModal = false;
        $this->reset(['name', 'status', 'isEditing', 'editingTypeId']);
    }

    public function delete($id)
    {
        $this->authorize('delete contacts');
        
        $type = ContactType::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $type->delete();
        
        $this->success('Contact type deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->authorize('edit contacts');
        
        $type = ContactType::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $type->update([
            'status' => $type->status === 'active' ? 'inactive' : 'active'
        ]);
        
        $this->success('Status updated successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'status', 'isEditing', 'editingTypeId']);
    }

    public function render()
    {
        $types = ContactType::where('client_account_id', $this->clientAccountId)
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.client.contact-types', [
            'types' => $types
        ]);
    }
}
