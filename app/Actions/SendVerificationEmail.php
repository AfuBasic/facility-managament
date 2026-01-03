<?php

namespace App\Actions;

use App\Events\ResendVerification;
use App\Models\User;

class SendVerificationEmail
{
    
    
    public function execute(User $user)
    {
        $error = collect([]);
         if(!$user) {
            $error->push(['message' => 'User not found', 'type' => 'error']);
            return $error;
        }

        /**
         * Check if user is verified
         */
        if($user->hasVerifiedEmail()) {
            $error->push(['message' => 'Your email is already verified', 'type' => 'warning']);

            return $error;
        }
        
        /**
         * Check if user has pending verifications that are under 5 mins before
         * sending a new one
         */
        $last_token = $user->verification_sent_at;
        if($last_token && $last_token->diffInMinutes(now()) < 5) {
            $left = floor(5 - $last_token->diffInMinutes(now()));
            $time = $left <= 1 ? " a few seconds" : $left . " minutes";
            $message = "Please try again after " . $time;
            return $error->push(['message' => $message, 'type' => 'warning']);
        }
        
        $user->verification_sent_at = now();
        $user->save();
        
        event(new ResendVerification($user));
        return $error;
    }
}