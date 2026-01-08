<?php

namespace App\Http\Controllers;


use App\Models\ClientMembership;
use App\Services\InvitationTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, ClientMembership $membership)
    {
        if (! $request->hasValidSignature()) {
            // If invalid or expired, mark as expired if relevant
            if (in_array($membership->status, [ClientMembership::STATUS_PENDING, ClientMembership::STATUS_RESET])) {
                $membership->update(['status' => ClientMembership::STATUS_EXPIRED]);
            }
            
            return view('auth.invitation-expired');
        }

        // If explicitly expired in DB (e.g. manually set), also show expired
        if ($membership->status === ClientMembership::STATUS_EXPIRED) {
             return view('auth.invitation-expired');
        }
        
        if(Auth::user()) {
            return redirect()->route('user.invitations');
        }
        if($membership->user->email_verified_at) {
            return redirect()->route('login')->with('url.intended', route('user.invitations'));
        }


        return view('auth.accept-invitation', ['membership' => $membership]);
    }

    public function store(Request $request, ClientMembership $membership)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired signature.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $membership->user()->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(), 
        ]);

        $membership->update(['status' => ClientMembership::STATUS_ACCEPTED]);
        app(InvitationTracker::class)->recordAcceptance(
            $membership->user->email,
            $membership->client_account_id,
            $membership->user->id
        );
        // Login
        Auth::login($membership->user);

        return redirect()->route('user.home');
    }
}
