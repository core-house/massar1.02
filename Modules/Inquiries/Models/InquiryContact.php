<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Progress\Models\ProjectProgress;

class InquiryContact extends Model
{
    protected $table = 'inquiry_contacts';

    protected $fillable = [
        'inquiry_id',
        'contact_id',
        'role_id',
        'is_primary',
        'involvement_percentage',
        'assigned_date',
        'responsibilities'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'assigned_date' => 'date',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function role()
    {
        return $this->belongsTo(ContactRole::class);
    }
}
