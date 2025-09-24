<?php

namespace Modules\Inquiries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkCondition extends Model
{
    protected $fillable = ['name', 'score'];

    public function inquiries(): BelongsToMany
    {
        return $this->belongsToMany(InquiryData::class, 'inquiry_work_condition');
    }
}
