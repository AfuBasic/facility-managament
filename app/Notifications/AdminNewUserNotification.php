<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public string $registrationMethod = 'email') {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $methodLabel = $this->registrationMethod === 'email' ? 'Email Registration' : ucfirst($this->registrationMethod).' Login';

        return (new MailMessage)
            ->subject('New User Registration - '.config('app.name'))
            ->greeting('Hello Admin,')
            ->line('A new user has registered on '.config('app.name').'.')
            ->line('**User Details:**')
            ->line('- **Name:** '.$this->user->name)
            ->line('- **Email:** '.$this->user->email)
            ->line('- **Registration Method:** '.$methodLabel)
            ->line('- **Registered At:** '.$this->user->created_at->format('M j, Y g:i A'))
            ->action('View Users', route('admin.users'))
            ->line('You can manage this user from the admin dashboard.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $methodLabel = $this->registrationMethod === 'email' ? 'email registration' : ucfirst($this->registrationMethod).' login';

        return [
            'title' => 'New User Registration',
            'message' => $this->user->name.' ('.$this->user->email.') registered via '.$methodLabel,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'registration_method' => $this->registrationMethod,
            'url' => route('admin.users'),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
