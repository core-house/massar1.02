<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class InquirieRole extends Model
{
    protected $table = 'inquiries_roles';

    protected $fillable = ['name', 'description'];

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_role', 'role_id', 'contact_id');
    }
}
