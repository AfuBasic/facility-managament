<?php

namespace App\Models\Traits;

use App\Models\ClientMembership;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait MembershipTrait
{
    public function clientMemberships(): HasMany{
        return $this->hasMany(ClientMembership::class);
    }
}