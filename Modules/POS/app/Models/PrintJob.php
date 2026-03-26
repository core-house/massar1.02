<?php

declare(strict_types=1);

namespace Modules\POS\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintJob extends Model
{
    protected $fillable = [
        'printer_station_id',
        'transaction_id',
        'idempotency_key',
        'content',
        'payload_hash',
        'sequence',
        'status',
        'error_message',
        'error_type',
        'agent_http_status',
        'agent_response_body',
        'attempts',
        'can_auto_retry',
        'is_manual',
        'printed_at',
        'printed_by',
        'sent_at',
        'last_retry_at',
        'retried_by',
        'retried_at',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
        'can_auto_retry' => 'boolean',
        'printed_at' => 'datetime',
        'sent_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'retried_at' => 'datetime',
        'attempts' => 'integer',
        'sequence' => 'integer',
        'agent_http_status' => 'integer',
    ];

    /**
     * Get the printer station that owns this print job.
     */
    public function printerStation(): BelongsTo
    {
        return $this->belongsTo(KitchenPrinterStation::class, 'printer_station_id');
    }

    /**
     * Get the transaction associated with this print job.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CashierTransaction::class, 'transaction_id');
    }

    /**
     * Get the user who printed this job manually.
     */
    public function printedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Get the user who retried this job manually.
     */
    public function retriedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retried_by');
    }

    /**
     * Generate idempotency key from transaction, station, payload hash, and sequence.
     */
    public static function generateIdempotencyKey(
        int $transactionId,
        int $stationId,
        string $payloadHash,
        int $sequence = 1
    ): string {
        return hash('sha256', "{$transactionId}:{$stationId}:{$payloadHash}:{$sequence}");
    }

    /**
     * Generate payload hash from content.
     */
    public static function generatePayloadHash(string $content): string
    {
        return hash('sha256', $content);
    }

    /**
     * Mark the print job as sending.
     */
    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark the print job as printed (successful).
     */
    public function markAsPrinted(?int $httpStatus = null, ?string $responseBody = null): void
    {
        $this->update([
            'status' => 'printed',
            'printed_at' => now(),
            'error_message' => null,
            'error_type' => 'NONE',
            'agent_http_status' => $httpStatus,
            'agent_response_body' => $responseBody,
        ]);
    }

    /**
     * Mark the print job as failed with error classification.
     */
    public function markAsFailed(
        string $errorMessage,
        string $errorType,
        ?int $httpStatus = null,
        ?string $responseBody = null,
        bool $canAutoRetry = true
    ): void {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'error_type' => $errorType,
            'agent_http_status' => $httpStatus,
            'agent_response_body' => $responseBody,
            'attempts' => $this->attempts + 1,
            'last_retry_at' => now(),
            'can_auto_retry' => $canAutoRetry,
        ]);
    }

    /**
     * Mark the print job as queued for retry.
     */
    public function markAsQueued(): void
    {
        $this->update([
            'status' => 'queued',
            'attempts' => $this->attempts + 1,
            'last_retry_at' => now(),
        ]);
    }

    /**
     * Record manual retry audit.
     */
    public function recordManualRetry(int $userId): void
    {
        $this->update([
            'retried_by' => $userId,
            'retried_at' => now(),
            'can_auto_retry' => true, // Re-enable auto retry on manual retry
        ]);
    }

    /**
     * Check if this job can be auto-retried based on error type.
     */
    public function canAutoRetry(): bool
    {
        if (! $this->can_auto_retry) {
            return false;
        }

        // Only auto-retry temporary errors
        return in_array($this->error_type, ['AGENT_DOWN', 'TIMEOUT', 'UNKNOWN', 'NONE']);
    }

    /**
     * Scope: Get jobs that can be auto-retried.
     */
    public function scopeAutoRetryable($query)
    {
        return $query->where('status', 'failed')
            ->where('can_auto_retry', true)
            ->whereIn('error_type', ['AGENT_DOWN', 'TIMEOUT', 'UNKNOWN', 'NONE']);
    }

    /**
     * Scope: Get jobs by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get jobs by error type.
     */
    public function scopeByErrorType($query, string $errorType)
    {
        return $query->where('error_type', $errorType);
    }

    /**
     * Scope: Get recent jobs (last 24 hours by default).
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }
}
