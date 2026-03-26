<?php

declare(strict_types=1);

namespace Modules\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPoint extends Model
{
    protected $table = 'salary_points';
    protected $guarded = ['id'];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}

