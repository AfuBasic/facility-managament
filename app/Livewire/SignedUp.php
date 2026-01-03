<?php

namespace App\Livewire;

use App\Actions\SendVerificationEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Signup Successful | Optima FM')]
class SignedUp extends Component
{

    public function sendVerificationEmail()
    {
        $user = Auth::user();
        if(!$user) {
            return redirect('/login');
        }
        $sendVerification = (new SendVerificationEmail())->execute($user);

        if($sendVerification->isNotEmpty()) {
            $this->dispatch('toast', message: $sendVerification->first()['message'], type: $sendVerification->first()['type']);
            return;
        } 
        
        $this->dispatch('toast', message: 'Verification email sent', type: 'success');
    }
    
    public function render()
    {
        return view('livewire.signed-up');
    }
}
