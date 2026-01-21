<?php

namespace App\Livewire\Client;

use App\Livewire\Concerns\WithNotifications;
use App\Models\ClientAccount;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Settings extends Component
{
    use WithNotifications;

    public int $clientAccountId;

    public string $name = '';

    public string $notificationEmail = '';

    public string $companyPhone = '';

    public string $currency = '$';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'notificationEmail' => 'nullable|email|max:255',
            'companyPhone' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
        ];
    }

    public function mount(int $clientAccountId)
    {
        $this->clientAccountId = $clientAccountId;

        $clientAccount = ClientAccount::findOrFail($clientAccountId);
        $this->name = $clientAccount->name ?? '';
        $this->notificationEmail = $clientAccount->notification_email ?? '';
        $this->companyPhone = $clientAccount->phone ?? '';
        $this->currency = $clientAccount->currency ?? '$';
    }

    public function save()
    {
        $this->validate();

        $clientAccount = ClientAccount::findOrFail($this->clientAccountId);
        $clientAccount->update([
            'name' => $this->name,
            'notification_email' => $this->notificationEmail ?: null,
            'phone' => $this->companyPhone ?: null,
            'currency' => $this->currency,
        ]);

        $this->success('Settings saved successfully!');
        $this->dispatch('saved');
    }

    #[Computed]
    public function currencyOptions(): array
    {
        return [
            '$' => '$ - US Dollar (USD)',
            '€' => '€ - Euro (EUR)',
            '£' => '£ - British Pound (GBP)',
            '₦' => '₦ - Nigerian Naira (NGN)',
            '¥' => '¥ - Japanese Yen (JPY)',
            '₹' => '₹ - Indian Rupee (INR)',
            'C$' => 'C$ - Canadian Dollar (CAD)',
            'A$' => 'A$ - Australian Dollar (AUD)',
            'CHF' => 'CHF - Swiss Franc (CHF)',
            'R' => 'R - South African Rand (ZAR)',
            'AED' => 'AED - UAE Dirham (AED)',
            'SAR' => 'SAR - Saudi Riyal (SAR)',
        ];
    }

    public function render()
    {
        return view('livewire.client.settings');
    }
}
