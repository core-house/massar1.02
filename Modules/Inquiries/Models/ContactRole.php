<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class ContactRole extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Contacts اللي عندهم الدور ده
    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_role_assignments')
            ->withPivot(['is_primary', 'assigned_date', 'notes'])
            ->withTimestamps();
    }

    // Inquiries اللي استخدم فيها الدور ده
    public function inquiries()
    {
        return $this->belongsToMany(Inquiry::class, 'inquiry_contacts')
            ->withPivot(['contact_id', 'is_primary', 'involvement_percentage'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
