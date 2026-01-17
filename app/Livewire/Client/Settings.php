<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use Livewire\Component;

class Settings extends Component
{
    use WithNotifications;

    public int $clientAccountId;

    public string $name = '';

    public string $notificationEmail = '';

    public string $companyPhone = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'notificationEmail' => 'nullable|email|max:255',
            'companyPhone' => 'nullable|string|max:50',
        ];
    }

    public function mount(int $clientAccountId)
    {
        $this->clientAccountId = $clientAccountId;

        $clientAccount = ClientAccount::findOrFail($clientAccountId);
        $this->name = $clientAccount->name ?? '';
        $this->notificationEmail = $clientAccount->notification_email ?? '';
        $this->companyPhone = $clientAccount->company_phone ?? '';
    }

    public function save()
    {
        $this->validate();

        $clientAccount = ClientAccount::findOrFail($this->clientAccountId);
        $clientAccount->update([
            'name' => $this->name,
            'notification_email' => $this->notificationEmail ?: null,
            'phone' => $this->companyPhone ?: null,
        ]);

        $this->success('Settings saved successfully!');
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.client.settings');
    }
}
