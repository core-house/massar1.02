<?php

namespace Modules\OfflinePOS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfflineTransaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offline_transactions_temp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'local_id',
        'branch_id',
        'data',
        'processing_status',
        'processing_error',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scopes
     */

    /**
     * تصفية حسب الفرع
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * المعاملات في قائمة الانتظار
     */
    public function scopeQueued($query)
    {
        return $query->where('processing_status', 'queued');
    }

    /**
     * المعاملات قيد المعالجة
     */
    public function scopeProcessing($query)
    {
        return $query->where('processing_status', 'processing');
    }

    /**
     * المعاملات المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * المعاملات الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    /**
     * Helpers
     */

    /**
     * تحديد المعاملة كقيد المعالجة
     */
    public function markAsProcessing()
    {
        $this->update([
            'processing_status' => 'processing',
        ]);
    }

    /**
     * تحديد المعاملة كمكتملة
     */
    public function markAsCompleted()
    {
        $this->update([
            'processing_status' => 'completed',
        ]);
    }

    /**
     * تحديد المعاملة كفاشلة
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'processing_status' => 'failed',
            'processing_error' => $errorMessage,
        ]);
    }
}
