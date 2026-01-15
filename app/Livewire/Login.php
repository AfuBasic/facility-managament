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
