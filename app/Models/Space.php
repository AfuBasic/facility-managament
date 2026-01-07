<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Space extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'facility_id',
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
