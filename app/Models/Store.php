<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClientAccount;
use App\Models\Facility;
use App\Models\User;

class Store extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_account_id',
        'facility_id',
        'name',
        'store_manager_id',
        'description',
        'status',
    ];
    
    protected $casts = [
        'status' => 'string',
    ];
    
    /**
     * Get the client account that owns the store
     */
    public function clientAccount()
    {
        return $this->belongsTo(ClientAccount::class);
    }
    
    /**
     * Get the facility that owns the store
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
    
    /**
     * Get the store manager
     */
    public function storeManager()
    {
        return $this->belongsTo(User::class, 'store_manager_id');
    }
}
