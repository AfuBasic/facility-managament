<?php

use App\Livewire\Client\Events\EventsIndex;
use App\Mail\EventInvitationMail;
use App\Models\ClientAccount;
use App\Models\Event;
use App\Models\User;
use App\Services\IcsCalendarService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->clientAccount = ClientAccount::factory()->create();
    $this->user = User::factory()->create();

    // Create membership for user
    $this->user->clientMemberships()->create([
        'client_account_id' => $this->clientAccount->id,
        'status' => 'accepted',
    ]);

    // Bind the client account to the container
    app()->instance(ClientAccount::class, $this->clientAccount);
});

test('events index page is displayed for authenticated users', function () {
    $this->actingAs($this->user)
        ->get('/app/events')
        ->assertOk()
        ->assertSeeLivewire(EventsIndex::class);
});

test('events are displayed in the list', function () {
    $event = Event::factory()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Team Meeting',
    ]);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->assertSee('Team Meeting');
});

test('virtual event auto-generates jitsi meeting link', function () {
    $event = Event::factory()->virtual()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
    ]);

    expect($event->meeting_link)->toStartWith('https://meet.jit.si/optima-');
    expect($event->isVirtual())->toBeTrue();
});

test('physical event stores location', function () {
    $event = Event::factory()->physical()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'location' => '123 Main Street',
    ]);

    expect($event->location)->toBe('123 Main Street');
    expect($event->isPhysical())->toBeTrue();
    expect($event->meeting_link)->toBeNull();
});

test('event can have multiple attendees', function () {
    $event = Event::factory()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
    ]);

    $attendees = User::factory(3)->create();
    $event->attendees()->attach($attendees->pluck('id'));

    expect($event->attendees)->toHaveCount(3);
});

test('event attendee status can be tracked', function () {
    $event = Event::factory()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
    ]);

    $attendee = User::factory()->create();
    $event->attendees()->attach($attendee->id, [
        'status' => 'accepted',
        'responded_at' => now(),
    ]);

    expect($event->acceptedAttendees)->toHaveCount(1);
    expect($event->pendingAttendees)->toHaveCount(0);
});

test('creating event sends invitation emails to all attendees', function () {
    Mail::fake();

    $attendees = User::factory(2)->create();
    foreach ($attendees as $attendee) {
        $attendee->clientMemberships()->create([
            'client_account_id' => $this->clientAccount->id,
            'status' => 'accepted',
        ]);
    }

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->set('title', 'Project Review')
        ->set('description', 'Review quarterly goals')
        ->set('type', 'virtual')
        ->set('eventDate', now()->addDay()->format('Y-m-d'))
        ->set('eventTime', '10:00')
        ->set('endTime', '11:00')
        ->set('attendeeIds', $attendees->pluck('id')->toArray())
        ->call('save')
        ->assertHasNoErrors();

    Mail::assertQueued(EventInvitationMail::class, 2);
});

test('ics calendar service generates valid ics content', function () {
    $event = Event::factory()->virtual()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Test Event',
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addHour(),
    ]);

    $icsService = new IcsCalendarService;
    $content = $icsService->generate($event);

    expect($content)->toContain('BEGIN:VCALENDAR');
    expect($content)->toContain('BEGIN:VEVENT');
    expect($content)->toContain('SUMMARY:Test Event');
    expect($content)->toContain('END:VEVENT');
    expect($content)->toContain('END:VCALENDAR');
});

test('ics calendar service includes attendee when provided', function () {
    $event = Event::factory()->virtual()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
    ]);

    $attendee = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);

    $icsService = new IcsCalendarService;
    $content = $icsService->generate($event, $attendee);

    expect($content)->toContain('ATTENDEE');
    expect($content)->toContain('john@example.com');
});

test('event validation requires attendees', function () {
    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->set('title', 'Test Event')
        ->set('type', 'virtual')
        ->set('eventDate', now()->addDay()->format('Y-m-d'))
        ->set('eventTime', '10:00')
        ->set('attendeeIds', [])
        ->call('save')
        ->assertHasErrors(['attendeeIds']);
});

test('physical event validation requires location', function () {
    $attendee = User::factory()->create();
    $attendee->clientMemberships()->create([
        'client_account_id' => $this->clientAccount->id,
        'status' => 'accepted',
    ]);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->set('title', 'Test Event')
        ->set('type', 'physical')
        ->set('location', '')
        ->set('eventDate', now()->addDay()->format('Y-m-d'))
        ->set('eventTime', '10:00')
        ->set('attendeeIds', [$attendee->id])
        ->call('save')
        ->assertHasErrors(['location']);
});

test('event can be edited', function () {
    $event = Event::factory()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Original Title',
    ]);

    $attendee = User::factory()->create();
    $attendee->clientMemberships()->create([
        'client_account_id' => $this->clientAccount->id,
        'status' => 'accepted',
    ]);
    $event->attendees()->attach($attendee->id);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->call('edit', $event)
        ->set('title', 'Updated Title')
        ->call('save')
        ->assertHasNoErrors();

    expect($event->fresh()->title)->toBe('Updated Title');
});

test('event can be deleted', function () {
    $event = Event::factory()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
    ]);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->call('deleteEvent', $event->id);

    expect(Event::find($event->id))->toBeNull();
});

test('upcoming events are filtered correctly', function () {
    Event::factory()->upcoming()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Future Event',
    ]);

    Event::factory()->past()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Past Event',
    ]);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->set('filter', 'upcoming')
        ->assertSee('Future Event')
        ->assertDontSee('Past Event');
});

test('past events are filtered correctly', function () {
    Event::factory()->upcoming()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Future Event',
    ]);

    Event::factory()->past()->create([
        'client_account_id' => $this->clientAccount->id,
        'created_by' => $this->user->id,
        'title' => 'Past Event',
    ]);

    $this->actingAs($this->user);

    Livewire::test(EventsIndex::class)
        ->set('filter', 'past')
        ->assertSee('Past Event')
        ->assertDontSee('Future Event');
});
