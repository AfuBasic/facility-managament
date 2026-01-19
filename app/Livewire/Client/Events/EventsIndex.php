<?php

namespace App\Livewire\Client\Events;

use App\Livewire\Concerns\WithNotifications;
use App\Mail\EventInvitationMail;
use App\Models\ClientAccount;
use App\Models\Event;
use App\Models\User;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
#[Title('Events | Optima FM')]
class EventsIndex extends Component
{
    use WithNotifications, WithPagination;

    public string $search = '';

    public string $filter = 'upcoming'; // upcoming, past, all

    public bool $showModal = false;

    public bool $isEditing = false;

    public ?int $editingEventId = null;

    // Form fields
    public string $title = '';

    public string $description = '';

    public string $type = 'virtual';

    public string $location = '';

    public string $eventDate = '';

    public string $eventTime = '';

    public string $endTime = '';

    public array $attendeeIds = [];

    public ClientAccount $clientAccount;

    // Calendar View Properties
    public string $view = 'list'; // 'list' or 'calendar'
    public int $currentMonth;
    public int $currentYear;
    public bool $showViewModal = false;
    public ?Event $viewingEvent = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'type' => 'required|in:virtual,physical',
            'location' => 'required_if:type,physical|nullable|string|max:500',
            'eventDate' => 'required|date|after_or_equal:today',
            'eventTime' => 'required|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i|after:eventTime',
            'attendeeIds' => 'required|array|min:1',
            'attendeeIds.*' => 'exists:contacts,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'location.required_if' => 'Location is required for physical events.',
            'attendeeIds.required' => 'Please select at least one attendee.',
            'attendeeIds.min' => 'Please select at least one attendee.',
        ];
    }

    public function mount(): void
    {
        $this->clientAccount = app(ClientAccount::class);
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function switchView(string $view): void
    {
        $this->view = $view;
    }

    public function nextMonth(): void
    {
        $date = \Carbon\Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function prevMonth(): void
    {
        $date = \Carbon\Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function viewEvent(int $id): void
    {
        $this->viewingEvent = Event::with('attendees')->findOrFail($id);
        $this->showViewModal = true;
    }

    #[Computed]
    public function calendarEvents()
    {
        $start = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth()->startOfWeek();
        $end = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->endOfMonth()->endOfWeek();

        return Event::where('client_account_id', $this->clientAccount->id)
            ->whereBetween('starts_at', [$start, $end])
            ->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function events()
    {
        $query = Event::where('client_account_id', $this->clientAccount->id)
            ->with(['attendees', 'creator']);

        // Apply filter
        if ($this->filter === 'upcoming') {
            $query->upcoming();
        } elseif ($this->filter === 'past') {
            $query->past();
        } else {
            $query->orderBy('starts_at', 'desc');
        }

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhereHas('attendees', function ($q) {
                        $q->where('firstname', 'like', "%{$this->search}%")
                          ->orWhere('lastname', 'like', "%{$this->search}%");
                    });
            });
        }

        return $query->paginate(10);
    }

    #[Computed]
    public function availableContacts()
    {
        return Contact::where('client_account_id', $this->clientAccount->id)
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $event = Event::where('client_account_id', $this->clientAccount->id)->findOrFail($id);
        $this->editingEventId = $event->id;
        $this->title = $event->title;
        $this->description = $event->description ?? '';
        $this->type = $event->type;
        $this->location = $event->location ?? '';
        $this->eventDate = $event->starts_at->format('Y-m-d');
        $this->eventTime = $event->starts_at->format('H:i');
        $this->endTime = $event->ends_at?->format('H:i') ?? '';
        $this->attendeeIds = $event->attendees->pluck('id')->toArray();
        $this->isEditing = true;
        // Close view modal if open
        $this->showViewModal = false;
        $this->viewingEvent = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $startsAt = \Carbon\Carbon::parse("{$this->eventDate} {$this->eventTime}");
        $endsAt = $this->endTime
            ? \Carbon\Carbon::parse("{$this->eventDate} {$this->endTime}")
            : $startsAt->copy()->addHour(); // Default to 1 hour if no end time

        // Check for overlaps
        $query = Event::where('client_account_id', $this->clientAccount->id)
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->where('starts_at', '<', $endsAt)
                  ->where('ends_at', '>', $startsAt);
            });

        if ($this->isEditing) {
            $query->where('id', '!=', $this->editingEventId);
        }

        if ($query->exists()) {
            $this->addError('eventTime', 'This time slot overlaps with another event.');
            return;
        }

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'type' => $this->type,
            'location' => $this->type === 'physical' ? $this->location : null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];

        if ($this->isEditing) {
            $event = Event::findOrFail($this->editingEventId);
            $event->update($data);

            // Sync attendees
            $event->attendees()->sync($this->attendeeIds);

            $this->success('Event updated successfully!');
        } else {
            $data['created_by'] = Auth::id();
            $event = Event::create($data);

            // Attach attendees with pending status
            $event->attendees()->attach($this->attendeeIds);

            // queue invitation emails to all attendees
            foreach ($event->attendees as $attendee) {
                Mail::to($attendee->email)->queue(new EventInvitationMail($event, $attendee));
            }

            $this->success('Event created and invitations sent!');
        }

        $this->showModal = false;
        $this->resetForm();
        unset($this->events);
    }

    public function resendInvitation(int $id): void
    {
        $event = Event::where('client_account_id', $this->clientAccount->id)->findOrFail($id);
        foreach ($event->attendees as $attendee) {
            Mail::to($attendee->email)->queue(new EventInvitationMail($event, $attendee));
        }
        $this->success('Invitations resent to all attendees!');
    }

    #[On('confirm-action')]
    public function handleConfirmAction(string $action, mixed $id): void
    {
        if ($action === 'delete-event') {
            $this->deleteEvent($id);
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('confirm-action', action: 'delete-event', id: $id);
    }

    public function deleteEvent(int $id): void
    {
        $event = Event::where('client_account_id', $this->clientAccount->id)
            ->findOrFail($id);

        $event->delete();
        $this->success('Event deleted successfully.');
        unset($this->events);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showViewModal = false;
        $this->viewingEvent = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'title', 'description', 'type', 'location',
            'eventDate', 'eventTime', 'endTime', 'attendeeIds',
            'isEditing', 'editingEventId',
        ]);
        $this->type = 'virtual';
        $this->attendeeIds = [];
    }

    public function render()
    {
        return view('livewire.client.events.events-index');
    }
}
