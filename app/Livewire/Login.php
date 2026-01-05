<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

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
