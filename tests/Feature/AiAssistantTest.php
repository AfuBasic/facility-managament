<?php

use App\Livewire\AiAssistant;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the ai assistant component', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AiAssistant::class)
        ->assertOk()
        ->assertSet('isOpen', false)
        ->assertSet('message', '')
        ->assertCount('conversation', 1); // Welcome message
});

it('can toggle chat window', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AiAssistant::class)
        ->assertSet('isOpen', false)
        ->call('toggleChat')
        ->assertSet('isOpen', true)
        ->call('toggleChat')
        ->assertSet('isOpen', false);
});

it('can clear conversation', function () {
    $user = User::factory()->create();

    actingAs($user);

    $component = Livewire::test(AiAssistant::class)
        ->call('clearConversation')
        ->assertCount('conversation', 1); // Should have one message (cleared message)

    // Verify the message content
    expect($component->get('conversation')[0]['content'])->toContain('Conversation cleared');
});

it('prevents sending empty messages', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(AiAssistant::class)
        ->set('message', '')
        ->call('sendMessage')
        ->assertCount('conversation', 1); // Should still only have welcome message
});
