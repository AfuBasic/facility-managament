<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientAccount extends Model
{
    protected $fillable = ['name'];

    public function memberships(): HasMany
    {
        return $this->hasMany(ClientMembership::class);
    }
}
