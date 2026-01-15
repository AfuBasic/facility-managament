<?php

namespace App\Livewire;

use App\Actions\PublicUserSignup;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Signup extends Component
{
    public $step = 1;

    public $organization_name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public function nextStep()
    {
        $this->validate([
            'organization_name' => 'required|min:3',
        ]);

        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function submit()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = app(PublicUserSignup::class)->execute(
            $this->organization_name,
            $this->email,
            $this->password,
        );

        Auth::login($user);

        redirect()->route('signed-up');
    }

    public function render()
    {
        return view('livewire.signup');
    }
}
