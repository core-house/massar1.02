<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSize extends Model
{
    protected $fillable = ['name', 'description'];

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'project_size_id');
    }
}
