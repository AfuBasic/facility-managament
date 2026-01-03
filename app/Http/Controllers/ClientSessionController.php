<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientSessionController extends Controller
{
    /**
     * Switch the current client session logic.
     */
    public function switch(Request $request, $client_id)
    {
        $user = Auth::user();

        // Verify user belongs to this client and membership is accepted
        $membership = $user->clientMemberships()
                           ->where('client_account_id', $client_id)
                           ->where('status', 'accepted')
                           ->first();
        
        if ($membership) {
            $request->session()->put('client_account_id', $client_id);
            return redirect()->route('app.dashboard');
        }
        
        return redirect()->route('user.home')->with('error', 'Invalid client selection.');
    }
}
