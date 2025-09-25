<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inquiries\Models\InquiryData;

class ProjectDocument extends Model
{
    protected $table = 'project_documents';

    protected $fillable = [
        'name',
        'description',
    ];

    public function inquiries()
    {
        return $this->belongsToMany(InquiryData::class, 'inquiry_project_document')
            ->withPivot('description')
            ->withTimestamps();
    }
}
