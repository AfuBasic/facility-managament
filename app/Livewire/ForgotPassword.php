<?php

namespace App\Livewire;

use App\Events\ForgotPasswordRequested;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public $email = '';

    public $status = null;

    public function submit()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user) {
            ForgotPasswordRequested::dispatch($user);
        }

        // We show the same message whether user exists or not for security,
        // but here we know it exists due to validation.
        $this->status = 'We have emailed your password reset link.';
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.forgot-password');
    }
}
