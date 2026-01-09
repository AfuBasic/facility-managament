<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'client_account_id',
        'firstname',
        'lastname',
        'contact_type_id',
        'contact_group_id',
        'gender',
        'email',
        'phone',
        'birthday',
        'address',
        'notes',
        'contact_person_id',
    ];
    
    protected $casts = [
        'gender' => 'string',
        'birthday' => 'date',
    ];
    
    /**
     * Get the client account that owns the contact
     */
    public function clientAccount(): BelongsTo
    {
        return $this->belongsTo(ClientAccount::class);
    }
    
    /**
     * Get the contact type
     */
    public function contactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }
    
    /**
     * Get the contact group
     */
    public function contactGroup(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class);
    }
    
    /**
     * Get the contact person (self-referencing)
     */
    public function contactPerson(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_person_id');
    }
    
    /**
     * Get contacts that have this contact as their contact person
     */
    public function relatedContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'contact_person_id');
    }
    
    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }
}
