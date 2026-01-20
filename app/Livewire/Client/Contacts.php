<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\ContactType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('Contacts | Optima FM')]
class Contacts extends Component
{
    use WithNotifications, WithPagination;

    #[Url(as: 'create', history: true)]
    public $showModal = false;

    public $isEditing = false;

    public $editingContactId;

    // Form fields
    public $firstname = '';

    public $lastname = '';

    public $email = '';

    public $phone = '';

    public $birthday = '';

    public $gender = '';

    public $address = '';

    public $notes = '';

    public $contact_type_id = '';

    public $contact_group_id = '';

    public $contact_person_id = '';

    // Filters
    #[Url]
    public $search = '';

    #[Url]
    public $filterType = '';

    #[Url]
    public $filterGroup = '';

    public $clientAccountId;

    // Quick Create States
    public $isCreatingType = false;

    public $newTypeName = '';

    public $isCreatingGroup = false;

    public $newGroupName = '';

    protected $rules = [
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:255|unique:contacts',
        'birthday' => 'nullable|date',
        'gender' => 'required|in:male,female,other',
        'address' => 'nullable|string',
        'notes' => 'nullable|string',
        'contact_type_id' => 'required|exists:contact_types,id',
        'contact_group_id' => 'required|exists:contact_groups,id',
        'contact_person_id' => 'nullable|exists:contacts,id',
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

        // If modal is opened via URL, initialize form for creating
        if ($this->showModal && ! $this->isEditing) {
            $this->resetForm();
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function updatingFilterGroup()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->authorize('create contacts');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('edit contacts');

        $contact = Contact::where('client_account_id', $this->clientAccountId)->findOrFail($id);

        $this->editingContactId = $contact->id;
        $this->firstname = $contact->firstname;
        $this->lastname = $contact->lastname;
        $this->email = $contact->email;
        $this->phone = $contact->phone;
        $this->birthday = $contact->birthday?->format('Y-m-d');
        $this->gender = $contact->gender;
        $this->address = $contact->address;
        $this->notes = $contact->notes;
        $this->contact_type_id = $contact->contact_type_id;
        $this->contact_group_id = $contact->contact_group_id;
        $this->contact_person_id = $contact->contact_person_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email ?: null,
            'phone' => $this->phone,
            'birthday' => $this->birthday ?: null,
            'gender' => $this->gender ?: null,
            'address' => $this->address ?: null,
            'notes' => $this->notes ?: null,
            'contact_type_id' => $this->contact_type_id ?: null,
            'contact_group_id' => $this->contact_group_id ?: null,
            'contact_person_id' => $this->contact_person_id ?: null,
        ];

        if ($this->isEditing) {
            $this->authorize('edit contacts');
            $contact = Contact::where('client_account_id', $this->clientAccountId)->findOrFail($this->editingContactId);
            $contact->update($data);
            $this->success('Contact updated successfully!');
        } else {
            $this->authorize('create contacts');
            Contact::create($data);
            $this->success('Contact created successfully!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $this->authorize('delete contacts');

        $contact = Contact::where('client_account_id', $this->clientAccountId)->findOrFail($id);
        $contact->delete();

        $this->success('Contact deleted successfully.');
    }

    // Quick Create Methods for Types
    public function toggleCreateType()
    {
        $this->isCreatingType = ! $this->isCreatingType;
        $this->newTypeName = '';
        if ($this->isCreatingType) {
            $this->contact_type_id = '';
        }
    }

    public function saveType()
    {
        $this->validate([
            'newTypeName' => 'required|string|max:255|unique:contact_types,name,NULL,id,client_account_id,'.$this->clientAccountId,
        ]);

        $type = ContactType::create([
            'client_account_id' => $this->clientAccountId,
            'name' => $this->newTypeName,
            'status' => 'active',
        ]);

        $this->contact_type_id = $type->id;
        $this->isCreatingType = false;
        $this->newTypeName = '';
        $this->success('Contact type created successfully.');
    }

    // Quick Create Methods for Groups
    public function toggleCreateGroup()
    {
        $this->isCreatingGroup = ! $this->isCreatingGroup;
        $this->newGroupName = '';
        if ($this->isCreatingGroup) {
            $this->contact_group_id = '';
        }
    }

    public function saveGroup()
    {
        $this->validate([
            'newGroupName' => 'required|string|max:255|unique:contact_groups,name,NULL,id,client_account_id,'.$this->clientAccountId,
        ]);

        $group = ContactGroup::create([
            'client_account_id' => $this->clientAccountId,
            'name' => $this->newGroupName,
            'status' => 'active',
        ]);

        $this->contact_group_id = $group->id;
        $this->isCreatingGroup = false;
        $this->newGroupName = '';
        $this->success('Contact group created successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'firstname', 'lastname', 'email', 'phone', 'birthday',
            'gender', 'address', 'notes', 'contact_type_id',
            'contact_group_id', 'contact_person_id', 'isEditing', 'editingContactId',
            'isCreatingType', 'newTypeName', 'isCreatingGroup', 'newGroupName',
        ]);
    }

    public function render()
    {
        $contacts = Contact::where('client_account_id', $this->clientAccountId)
            ->with(['contactType', 'contactGroup', 'contactPerson'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('firstname', 'like', '%'.$this->search.'%')
                        ->orWhere('lastname', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('contact_type_id', $this->filterType);
            })
            ->when($this->filterGroup, function ($query) {
                $query->where('contact_group_id', $this->filterGroup);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $types = ContactType::where('client_account_id', $this->clientAccountId)
            ->where('status', 'active')
            ->get();

        $groups = ContactGroup::where('client_account_id', $this->clientAccountId)
            ->where('status', 'active')
            ->get();

        $availableContacts = Contact::where('client_account_id', $this->clientAccountId)
            ->when($this->editingContactId, function ($query) {
                $query->where('id', '!=', $this->editingContactId);
            })
            ->orderBy('firstname')
            ->get();

        return view('livewire.client.contacts', [
            'contacts' => $contacts,
            'types' => $types,
            'groups' => $groups,
            'availableContacts' => $availableContacts,
        ]);
    }
}
