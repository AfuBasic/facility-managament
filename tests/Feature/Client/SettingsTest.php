<?php

use App\Livewire\Client\Settings;
use App\Models\ClientAccount;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->clientAccount = ClientAccount::factory()->create([
        'currency' => '$',
    ]);

    $this->user = User::factory()->create();
});

it('loads the settings component with client account data', function () {
    Livewire::test(Settings::class, ['clientAccountId' => $this->clientAccount->id])
        ->assertSet('name', $this->clientAccount->name)
        ->assertSet('currency', '$');
});

it('can update the currency setting', function () {
    Livewire::test(Settings::class, ['clientAccountId' => $this->clientAccount->id])
        ->set('currency', '₦')
        ->call('save')
        ->assertDispatched('saved');

    $this->clientAccount->refresh();
    expect($this->clientAccount->currency)->toBe('₦');
});

it('validates currency is required', function () {
    Livewire::test(Settings::class, ['clientAccountId' => $this->clientAccount->id])
        ->set('currency', '')
        ->call('save')
        ->assertHasErrors(['currency' => 'required']);
});

it('provides currency options', function () {
    $component = Livewire::test(Settings::class, ['clientAccountId' => $this->clientAccount->id]);

    $currencyOptions = $component->instance()->currencyOptions;

    expect($currencyOptions)->toBeArray()
        ->and($currencyOptions)->toHaveKey('$')
        ->and($currencyOptions)->toHaveKey('₦')
        ->and($currencyOptions)->toHaveKey('€');
});
