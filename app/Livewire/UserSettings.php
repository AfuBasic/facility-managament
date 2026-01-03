<?php

namespace App\Livewire;

use App\Events\EmailUpdateOtpRequested;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailUpdateOtp;

#[Layout('components.layouts.dashboard')]
#[Title('Account Settings | Optima FM')]
class UserSettings extends Component
{
    // Profile
    public $name = '';
    public $email = '';

    // OTP State
    public $new_email = '';
    public $otp_code = '';
    public $generated_otp = null;
    public $otp_sent_at = null;
    public $pending_email = null;
    public $showOtpModal = false;

    // Password
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    public function mount()
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->new_email = $this->email; // Default to current email
    }

    public function updateInformation()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->save();

        $this->dispatch('toast', message: 'Profile information updated successfully.', type: 'success');
    }

    public function initiateEmailUpdate()
    {
        $this->validate([
            'new_email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore(Auth::id())],
        ]);

        if ($this->new_email === Auth::user()->email) {
            $this->dispatch('toast', message: 'Email is already set to this address.', type: 'info');
            return;
        }

        // Rate Limiting Check
        if ($this->generated_otp && 
            $this->pending_email === $this->new_email && 
            $this->otp_sent_at && 
            \Carbon\Carbon::parse($this->otp_sent_at)->diffInMinutes(now()) < 2) {
            
            // Reuse existing OTP
            $this->showOtpModal = true;
            //$this->dispatch('toast', message: 'Please enter the verification code sent previously.', type: 'info');
            return;
        }

        // Generate 6-digit OTP
        $this->generated_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_sent_at = now();
        $this->pending_email = $this->new_email;
        
        // Show Modal
        $this->showOtpModal = true;

        // Dispatch Event to send OTP to CURRENT email
        EmailUpdateOtpRequested::dispatch(Auth::user(), $this->generated_otp);

        $this->dispatch('toast', message: 'Verification code sent to your CURRENT email address.', type: 'info');
    }

    public function verifyOtpAndSave()
    {
        $this->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        if ($this->otp_code !== $this->generated_otp) {
            $this->addError('otp_code', 'Invalid verification code.');
            return;
        }

        $user = Auth::user();
        $oldEmail = $user->email; // Capture old email
        $user->email = $this->new_email;
        $user->email_verified_at = null; // Unverify the new email
        $user->save();

        // Send Verification Link to NEW email
        $user->sendEmailVerificationNotification();
        
        // Notify OLD email about the change
        \App\Events\EmailUpdated::dispatch($user, $oldEmail, $this->new_email);

        // Update local state
        $this->email = $this->new_email;
        
        // Reset Logic
        $this->reset(['generated_otp', 'otp_code', 'showOtpModal', 'otp_sent_at', 'pending_email']);

        $this->dispatch('toast', message: 'Email updated! Please check your NEW email to verify it.', type: 'success');
    }

    public function cancelEmailUpdate()
    {
        // Don't clear generated_otp so we can respect rate limiting if they try again immediately
        $this->reset(['otp_code', 'showOtpModal']);
        $this->new_email = $this->email; // Revert to original
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('toast', message: 'Password changed successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.user-settings');
    }
}
