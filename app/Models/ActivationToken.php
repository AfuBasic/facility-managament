<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationToken extends Model
{
    protected $fillable = [
        'token',
        'expires_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
