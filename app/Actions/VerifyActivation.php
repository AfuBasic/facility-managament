<?php

namespace App\Actions;

use App\Models\User;

class VerifyActivation
{
    public function execute(User $user) {
        $user->markEmailAsVerified();
    }
}