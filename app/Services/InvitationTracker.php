<?php

namespace App\Services;

use App\Models\InvitationLog;
use App\Models\User;

class InvitationTracker
{
    /**
     * Record a new invitation
     */
    public function recordInvitation(
        string $email,
        string $roleName,
        int $clientAccountId,
        int $invitedByUserId,
        bool $isNewUser
    ): InvitationLog {
        return InvitationLog::create([
            'user_id' => null, // Will be set when accepted
            'email' => $email,
            'role_name' => $roleName,
            'status' => 'pending',
            'invited_at' => now(),
            'expires_at' => now()->addHour(),
            'invited_by_user_id' => $invitedByUserId,
            'is_new_user' => $isNewUser,
        ]);
    }

    /**
     * Record invitation acceptance
     */
    public function recordAcceptance(string $email, int $clientAccountId, int $userId): void
    {
        InvitationLog::where('email', $email)
            ->where('client_account_id', $clientAccountId)
            ->where('status', 'pending')
            ->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'user_id' => $userId,
            ]);
    }

    /**
     * Mark invitation as expired
     */
    public function markExpired(string $email, int $clientAccountId): void
    {
        InvitationLog::where('email', $email)
            ->where('client_account_id', $clientAccountId)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update([
                'status' => 'expired',
            ]);
    }

    /**
     * Update invitation on resend
     */
    public function recordResend(string $email, int $clientAccountId): void
    {
        InvitationLog::where('email', $email)
            ->where('client_account_id', $clientAccountId)
            ->whereIn('status', ['pending', 'expired'])
            ->update([
                'status' => 'pending',
                'invited_at' => now(),
                'expires_at' => now()->addHour(),
            ]);
    }
}
