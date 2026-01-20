<?php

namespace Modules\OfflinePOS\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfflineSyncLog extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offline_sync_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'local_transaction_id',
        'server_transaction_id',
        'user_id',
        'branch_id',
        'status',
        'transaction_data',
        'error_message',
        'sync_attempts',
        'last_sync_attempt',
        'synced_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'transaction_data' => 'array',
        'last_sync_attempt' => 'datetime',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقات
     */

    /**
     * المستخدم الذي أنشأ المعاملة
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
     * المعاملات في انتظار المزامنة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * المعاملات التي تمت مزامنتها
     */
    public function scopeSynced($query)
    {
        return $query->where('status', 'synced');
    }

    /**
     * المعاملات التي فشلت في المزامنة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'error');
    }

    /**
     * المعاملات الأقدم أولاً
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Helpers
     */

    /**
     * زيادة عدد محاولات المزامنة
     */
    public function incrementSyncAttempts()
    {
        $this->increment('sync_attempts');
        $this->update(['last_sync_attempt' => now()]);
    }

    /**
     * تحديث حالة المزامنة
     */
    public function markAsSyncing()
    {
        $this->update([
            'status' => 'syncing',
            'last_sync_attempt' => now(),
        ]);
    }

    /**
     * تحديد المزامنة كناجحة
     */
    public function markAsSynced($serverTransactionId)
    {
        $this->update([
            'status' => 'synced',
            'server_transaction_id' => $serverTransactionId,
            'synced_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * تحديد المزامنة كفاشلة
     */
    public function markAsFailed($errorMessage)
    {
        $this->incrementSyncAttempts();
        $this->update([
            'status' => 'error',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * هل يمكن إعادة المحاولة؟
     */
    public function canRetry($maxAttempts = 5)
    {
        return $this->sync_attempts < $maxAttempts;
    }
}
