<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasHashid;

class Asset extends Model
{
    use HasFactory, HasHashid;

    protected $fillable = [
        'client_account_id',
        'facility_id',
        'store_id',
        'user_id',
        'serial',
        'units',
        'name',
        'description',
        'supplier_contact_id',
        'type',
        'minimum',
        'maximum',
        'purchased_at',
        'notes',
        'space_id',
    ];

    protected $casts = [
        'purchased_at' => 'date',
        'type' => 'string',
    ];

    /**
     * Get the client account that owns the asset
     */
    public function clientAccount()
    {
        return $this->belongsTo(ClientAccount::class);
    }

    /**
     * Get the facility that owns the asset
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the store that owns the asset
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user assigned to the asset
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the supplier contact
     */
    public function supplierContact()
    {
        return $this->belongsTo(Contact::class, 'supplier_contact_id');
    }

    /**
     * Get the space where the asset is located
     */
    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * Get the images for the asset
     */
    public function images()
    {
        return $this->hasMany(AssetImage::class);
    }

    /**
     * Get the history records for the asset
     */
    public function history()
    {
        return $this->hasMany(AssetHistory::class);
    }
}
