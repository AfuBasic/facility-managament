<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    public $email = '';

    public $password = '';

    public $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            if (Auth::user()->suspended_at) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                $this->addError('email', 'Your account has been suspended. Please contact support.');
                $this->dispatch('toast', message: 'Your account has been suspended.', type: 'error');

                return;
            }

            session()->regenerate();

            return redirect()->intended(route('user.home'));
        }

        $this->addError('email', 'Invalid Login Credentials.');
        $this->dispatch('toast', message: 'Invalid Login Credentials.', type: 'error');
    }

    public function render()
    {
        return view('livewire.login');
    }
}
