<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Branches\Models\Branch;

class Lead extends Model
{
    protected $table = 'leads';

    // تحديد الحقول القابلة للتعبئة
    protected $fillable = [
        'title',
        'client_id',
        'status_id',
        'amount',
        'source_id',
        'assigned_to',
        'description',
        'branch_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function changeStatus(int $newStatusId): void
    {
        $this->update(['status_id' => $newStatusId]);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ChanceSource::class, 'source_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
