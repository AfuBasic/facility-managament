<?php

use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\User;
use App\Models\WorkOrder;

test('work order serial is auto-generated on creation', function () {
    $clientAccount = ClientAccount::factory()->create(['name' => 'Lara Corp']);
    $facility = Facility::factory()->create(['client_account_id' => $clientAccount->id]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'client_account_id' => $clientAccount->id,
        'facility_id' => $facility->id,
        'title' => 'Test Work Order',
        'description' => 'Test description',
        'priority' => 'medium',
        'status' => 'reported',
        'reported_by' => $user->id,
        'reported_at' => now(),
    ]);

    expect($workOrder->workorder_serial)->toStartWith('#LC');
});

test('work order serial extracts initials from organization name', function () {
    $clientAccount = ClientAccount::factory()->create(['name' => 'Facility Management Inc']);
    $facility = Facility::factory()->create(['client_account_id' => $clientAccount->id]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'client_account_id' => $clientAccount->id,
        'facility_id' => $facility->id,
        'title' => 'Test Work Order',
        'description' => 'Test description',
        'priority' => 'medium',
        'status' => 'reported',
        'reported_by' => $user->id,
        'reported_at' => now(),
    ]);

    expect($workOrder->workorder_serial)->toStartWith('#FMI');
});

test('work order serial includes timestamp and random digits for uniqueness', function () {
    $clientAccount = ClientAccount::factory()->create(['name' => 'Acme']);
    $facility = Facility::factory()->create(['client_account_id' => $clientAccount->id]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'client_account_id' => $clientAccount->id,
        'facility_id' => $facility->id,
        'title' => 'Test Work Order',
        'description' => 'Test description',
        'priority' => 'medium',
        'status' => 'reported',
        'reported_by' => $user->id,
        'reported_at' => now(),
    ]);

    // Format: #{INITIALS}{yymmddHHiiss}{3-digit-random}
    expect($workOrder->workorder_serial)->toMatch('/^#A\d{15}$/');
});

test('work order serial defaults to WO for empty organization name', function () {
    $clientAccount = ClientAccount::factory()->create(['name' => '']);
    $facility = Facility::factory()->create(['client_account_id' => $clientAccount->id]);
    $user = User::factory()->create();

    $workOrder = WorkOrder::create([
        'client_account_id' => $clientAccount->id,
        'facility_id' => $facility->id,
        'title' => 'Test Work Order',
        'description' => 'Test description',
        'priority' => 'medium',
        'status' => 'reported',
        'reported_by' => $user->id,
        'reported_at' => now(),
    ]);

    expect($workOrder->workorder_serial)->toStartWith('#WO');
});

test('multiple work orders created simultaneously have unique serials', function () {
    $clientAccount = ClientAccount::factory()->create(['name' => 'Test Corp']);
    $facility = Facility::factory()->create(['client_account_id' => $clientAccount->id]);
    $user = User::factory()->create();

    $serials = collect();

    // Create multiple work orders rapidly
    for ($i = 0; $i < 5; $i++) {
        $workOrder = WorkOrder::create([
            'client_account_id' => $clientAccount->id,
            'facility_id' => $facility->id,
            'title' => "Test Work Order {$i}",
            'description' => 'Test description',
            'priority' => 'medium',
            'status' => 'reported',
            'reported_by' => $user->id,
            'reported_at' => now(),
        ]);

        $serials->push($workOrder->workorder_serial);
    }

    // All serials should be unique
    expect($serials->unique()->count())->toBe(5);
});
