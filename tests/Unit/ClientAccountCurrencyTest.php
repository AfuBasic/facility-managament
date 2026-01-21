<?php

use App\Models\ClientAccount;

it('returns the currency symbol', function () {
    $clientAccount = new ClientAccount(['currency' => '₦']);

    expect($clientAccount->getCurrencySymbol())->toBe('₦');
});

it('returns default currency symbol when not set', function () {
    $clientAccount = new ClientAccount();

    expect($clientAccount->getCurrencySymbol())->toBe('$');
});

it('formats currency with the client symbol', function () {
    $clientAccount = new ClientAccount(['currency' => '€']);

    expect($clientAccount->formatCurrency(1234.56))->toBe('€1,234.56');
});

it('formats currency with default decimals', function () {
    $clientAccount = new ClientAccount(['currency' => '$']);

    expect($clientAccount->formatCurrency(1000))->toBe('$1,000.00');
});

it('formats currency with custom decimals', function () {
    $clientAccount = new ClientAccount(['currency' => '£']);

    expect($clientAccount->formatCurrency(1234.567, 0))->toBe('£1,235');
});

it('handles null amounts', function () {
    $clientAccount = new ClientAccount(['currency' => '$']);

    expect($clientAccount->formatCurrency(null))->toBe('$0.00');
});
