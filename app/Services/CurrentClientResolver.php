<?php

namespace App\Services;

use App\Models\ClientAccount;
use Illuminate\Http\Request;

class CurrentClientResolver
{
 
    public function resolve(Request $request): ?ClientAccount
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }
        
        $memberships = $user->clientMemberships()->where('status','accepted')->with('clientAccount');
        $clientId = $request->session()->get('client_account_id');
        if($clientId) {
            $client = $memberships->where('client_account_id', $clientId)->first();
            return $client?->clientAccount;
        }
        
        $clients = $memberships->get()->pluck('clientAccount');
        
        if($clients->count() === 1) {
            $request->session()->put('client_account_id', $clients->first()->id);
            return $clients->first();
        }
        
        return null;
    }
}
