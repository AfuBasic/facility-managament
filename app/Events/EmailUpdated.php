<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $oldEmail;

    public $newEmail;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $oldEmail, string $newEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }
}
