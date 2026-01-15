<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactGroup extends Model
{
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'client_account_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the client account that owns the contact group
     */
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }

    /**
     * Get the contacts for this group
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
