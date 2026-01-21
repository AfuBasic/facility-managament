<?php

use App\Models\ClientMembership;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Client-scoped user channel - only if user is a member of that client account
Broadcast::channel('client.{clientAccountId}.user.{userId}', function ($user, $clientAccountId, $userId) {
    // User must match and be a member of this client account
    if ((int) $user->id !== (int) $userId) {
        return false;
    }

    return ClientMembership::where('client_account_id', $clientAccountId)
        ->where('user_id', $userId)
        ->where('status', ClientMembership::STATUS_ACCEPTED)
        ->exists();
});

// Conversation channel - only participants can subscribe
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (! $conversation) {
        return false;
    }

    return $conversation->user_one_id === $user->id || $conversation->user_two_id === $user->id;
});
