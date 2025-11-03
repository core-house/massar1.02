<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone_1',
        'phone_2',
        'type',
        'address_1',
        'address_2',
        'tax_number',
        'role_id',
        'parent_id',
        'notes'
    ];

    public function role()
    {
        return $this->belongsTo(InquirieRole::class, 'role_id');
    }

    public function roles()
    {
        return $this->belongsToMany(InquirieRole::class, 'contact_role', 'contact_id', 'role_id');
    }

    public function parent()
    {
        return $this->belongsTo(Contact::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Contact::class, 'parent_id');
    }
}
