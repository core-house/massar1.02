<?php

namespace Modules\OfflinePOS\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\OfflinePOS\Models\OfflineSyncLog;
use Modules\OfflinePOS\Models\OfflineTransaction;
use Modules\Invoices\Services\SaveInvoiceService;
use Modules\Invoices\Services\Invoice\DetailValueCalculator;
use Modules\Invoices\Services\Invoice\DetailValueValidator;

/**
 * Service للمزامنة
 * يتعامل مع منطق مزامنة المعاملات من offline إلى server
 */
class SyncService
{
    protected TransactionProcessorService $transactionProcessor;

    public function __construct(TransactionProcessorService $transactionProcessor)
    {
        $this->transactionProcessor = $transactionProcessor;
    }

    /**
     * مزامنة معاملة واحدة
     * 
     * @param string $localId
     * @param array $transactionData
     * @param int|null $branchId
     * @return array
     */
    public function syncSingleTransaction(string $localId, array $transactionData, ?int $branchId): array
    {
        DB::beginTransaction();

        try {
            // إنشاء أو جلب sync log
            $syncLog = OfflineSyncLog::firstOrCreate(
                ['local_transaction_id' => $localId],
                [
                    'user_id' => Auth::id(),
                    'branch_id' => $branchId,
                    'status' => 'pending',
                    'transaction_data' => $transactionData,
                ]
            );

            // إذا كانت مزامنة سابقاً
            if ($syncLog->status === 'synced') {
                DB::commit();
                return [
                    'success' => true,
                    'server_transaction_id' => $syncLog->server_transaction_id,
                    'already_synced' => true,
                ];
            }

            // تحديث الحالة إلى syncing
            $syncLog->markAsSyncing();

            // معالجة المعاملة
            $result = $this->transactionProcessor->processTransaction($transactionData, $branchId);

            if ($result['success']) {
                // تحديد المزامنة كناجحة
                $syncLog->markAsSynced($result['transaction_id']);

                DB::commit();

                return [
                    'success' => true,
                    'server_transaction_id' => $result['transaction_id'],
                    'invoice_number' => $result['invoice_number'] ?? null,
                    'created_at' => $result['created_at'] ?? now()->toISOString(),
                ];
            } else {
                // تحديد المزامنة كفاشلة
                $syncLog->markAsFailed($result['error']);

                DB::rollBack();

                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();

            // تحديث sync log في حالة الخطأ
            if (isset($syncLog)) {
                $syncLog->markAsFailed($e->getMessage());
            }

            Log::error('SyncService Error: ' . $e->getMessage(), [
                'local_id' => $localId,
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * مزامنة جماعية
     * 
     * @param array $transactions
     * @param int|null $branchId
     * @return array
     */
    public function batchSync(array $transactions, ?int $branchId): array
    {
        $results = [];
        $summary = [
            'total' => count($transactions),
            'synced' => 0,
            'failed' => 0,
            'already_synced' => 0,
        ];

        foreach ($transactions as $transaction) {
            $localId = $transaction['local_id'];
            $transactionData = $transaction['transaction'];

            $result = $this->syncSingleTransaction($localId, $transactionData, $branchId);

            if ($result['success']) {
                if (isset($result['already_synced']) && $result['already_synced']) {
                    $summary['already_synced']++;
                    $status = 'already_synced';
                } else {
                    $summary['synced']++;
                    $status = 'synced';
                }
            } else {
                $summary['failed']++;
                $status = 'failed';
            }

            $results[] = [
                'local_id' => $localId,
                'status' => $status,
                'server_id' => $result['server_transaction_id'] ?? null,
                'error' => $result['error'] ?? null,
            ];
        }

        return [
            'results' => $results,
            'total' => $summary['total'],
            'synced' => $summary['synced'],
            'failed' => $summary['failed'],
            'already_synced' => $summary['already_synced'],
        ];
    }

    /**
     * إعادة محاولة مزامنة معاملة فاشلة
     * 
     * @param OfflineSyncLog $syncLog
     * @param int|null $branchId
     * @return array
     */
    public function retrySyncTransaction(OfflineSyncLog $syncLog, ?int $branchId): array
    {
        if (!$syncLog->canRetry()) {
            return [
                'success' => false,
                'error' => 'Maximum retry attempts reached.',
            ];
        }

        $transactionData = $syncLog->transaction_data;

        return $this->syncSingleTransaction(
            $syncLog->local_transaction_id,
            $transactionData,
            $branchId
        );
    }

    /**
     * تنظيف السجلات القديمة المزامنة
     * 
     * @param int $daysOld
     * @return int عدد السجلات المحذوفة
     */
    public function cleanupOldSyncedLogs(int $daysOld = 30): int
    {
        return OfflineSyncLog::synced()
            ->where('synced_at', '<', now()->subDays($daysOld))
            ->delete();
    }

    /**
     * جلب إحصائيات المزامنة
     * 
     * @param int|null $branchId
     * @return array
     */
    public function getSyncStatistics(?int $branchId = null): array
    {
        $query = OfflineSyncLog::query();

        if ($branchId) {
            $query->forBranch($branchId);
        }

        return [
            'pending' => (clone $query)->pending()->count(),
            'syncing' => (clone $query)->where('status', 'syncing')->count(),
            'synced' => (clone $query)->synced()->count(),
            'failed' => (clone $query)->failed()->count(),
            'total' => $query->count(),
        ];
    }
}
