<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryDocument extends Model
{
    protected $table = 'project_documents';

    protected $fillable = [
        'name'
    ];

    public function inquiries()
    {
        return $this->belongsToMany(
            Inquiry::class,
            'inquiry_project_document',
            'project_document_id',
            'inquiry_id'
        )->withTimestamps();
    }
}
