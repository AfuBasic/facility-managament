<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCache extends Model
{
    use HasFactory;

    protected $table = 'image_cache';

    protected $fillable = [
        'image_hash',
        'url',
        'secure_url',
        'public_id',
        'width',
        'height',
        'format',
        'file_size',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Increment the usage count for this cached image
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Decrement the usage count for this cached image
     */
    public function decrementUsage(): void
    {
        $this->decrement('usage_count');
    }
}
