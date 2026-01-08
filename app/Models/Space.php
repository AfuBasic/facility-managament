<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Space extends Model
{
    use HasFactory;
    use BelongsToClient;
    
    protected $fillable = [
        'facility_id',
        'client_account_id',
        'name',
        'type',
        'floor',
        'area',
        'capacity',
        'description',
        'status',
    ];

    /**
     * Get the facility that owns the space
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
