<?php

namespace Tests\Feature;

use App\Services\AiAssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

it('can connect to claude api and get a response', function () {
    $user = \App\Models\User::factory()->create();

    actingAs($user);

    $aiService = app(AiAssistantService::class);

    // Ask a simple question
    $response = $aiService->ask('How many assets do we have?');

    // Should get a successful response
    expect($response)->toHaveKey('success');
    expect($response)->toHaveKey('answer');

    // Log the response for debugging
    dump([
        'success' => $response['success'],
        'answer' => $response['answer'] ?? null,
        'error' => $response['error'] ?? null,
    ]);
})->skip('Manual test - requires valid API key');
