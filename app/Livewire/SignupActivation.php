<?php

namespace App\Livewire;

use App\Actions\VerifyActivation;
use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class SignupActivation extends Component
{
    public string $token;

    public bool $valid = false;

    public function mount(Request $request, User $user)
    {
        $this->valid = $request->hasValidSignature();

        if ($this->valid) {
            app(VerifyActivation::class)->execute($user);
        }
    }

    public function render()
    {
        return view($this->valid ? 'livewire.signup-activation' : 'livewire.signup-activation-invalid');
    }
}
