<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetImage extends Model
{
    use BelongsToClient, HasFactory;

    protected $fillable = [
        'asset_id',
        'image',
    ];

    /**
     * Get the asset that owns the image
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
