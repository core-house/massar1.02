<?php

namespace Modules\OfflinePOS\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\OfflinePOS\Services\SyncService;
use Modules\OfflinePOS\Models\OfflineSyncLog;

/**
 * API Controller للمزامنة
 * 
 * يتعامل مع:
 * - مزامنة معاملة واحدة
 * - مزامنة جماعية (batch)
 * - التحقق من حالة المزامنة
 */
class SyncController extends Controller
{
    protected SyncService $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * مزامنة معاملة واحدة
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function syncTransaction(Request $request): JsonResponse
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'local_id' => 'required|string|max:100',
            'transaction' => 'required|array',
            'transaction.transaction_type' => 'required|in:sale,return',
            'transaction.date' => 'required|date',
            'transaction.customer_id' => 'required|integer',
            'transaction.items' => 'required|array|min:1',
            'transaction.total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // التحقق من الصلاحية
            if (!Auth::user()->can('sync offline pos transactions')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to sync transactions.',
                ], 403);
            }

            // التحقق من عدم تكرار المزامنة
            $existingSync = OfflineSyncLog::where('local_transaction_id', $request->local_id)
                ->where('status', 'synced')
                ->first();

            if ($existingSync) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction already synced.',
                    'data' => [
                        'server_transaction_id' => $existingSync->server_transaction_id,
                        'synced_at' => $existingSync->synced_at,
                    ],
                ], 200);
            }

            // جلب branch_id
            $branchId = $request->input('current_branch_id');

            // المزامنة
            $result = $this->syncService->syncSingleTransaction(
                $request->local_id,
                $request->transaction,
                $branchId
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction synced successfully.',
                    'data' => [
                        'server_transaction_id' => $result['server_transaction_id'],
                        'invoice_number' => $result['invoice_number'],
                        'created_at' => $result['created_at'],
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Failed to sync transaction.',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('SyncTransaction API Error: ' . $e->getMessage(), [
                'local_id' => $request->local_id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync transaction.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * مزامنة جماعية (Batch Sync)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function batchSync(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'required|array|min:1|max:50', // حد أقصى 50 معاملة
            'transactions.*.local_id' => 'required|string',
            'transactions.*.transaction' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if (!Auth::user()->can('sync offline pos transactions')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to sync transactions.',
                ], 403);
            }

            $branchId = $request->input('current_branch_id');
            
            $results = $this->syncService->batchSync(
                $request->transactions,
                $branchId
            );

            return response()->json([
                'success' => true,
                'message' => 'Batch sync completed.',
                'results' => $results['results'],
                'summary' => [
                    'total' => $results['total'],
                    'synced' => $results['synced'],
                    'failed' => $results['failed'],
                    'already_synced' => $results['already_synced'],
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('BatchSync API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Batch sync failed.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * التحقق من حالة المزامنة
     * 
     * @param Request $request
     * @param string $localId
     * @return JsonResponse
     */
    public function checkStatus(Request $request, string $localId): JsonResponse
    {
        try {
            $syncLog = OfflineSyncLog::where('local_transaction_id', $localId)
                ->first();

            if (!$syncLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found.',
                    'status' => 'not_found',
                ], 404);
            }

            // التحقق من أن المعاملة تخص نفس الفرع
            $branchId = $request->input('current_branch_id');
            if ($syncLog->branch_id && $syncLog->branch_id != $branchId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction belongs to different branch.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'local_id' => $syncLog->local_transaction_id,
                    'server_id' => $syncLog->server_transaction_id,
                    'status' => $syncLog->status,
                    'sync_attempts' => $syncLog->sync_attempts,
                    'last_sync_attempt' => $syncLog->last_sync_attempt,
                    'synced_at' => $syncLog->synced_at,
                    'error_message' => $syncLog->error_message,
                    'can_retry' => $syncLog->canRetry(),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('CheckStatus API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check sync status.',
            ], 500);
        }
    }

    /**
     * إعادة محاولة مزامنة معاملة فاشلة
     * 
     * @param Request $request
     * @param string $localId
     * @return JsonResponse
     */
    public function retrySync(Request $request, string $localId): JsonResponse
    {
        try {
            $syncLog = OfflineSyncLog::where('local_transaction_id', $localId)
                ->where('status', 'error')
                ->first();

            if (!$syncLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed transaction not found.',
                ], 404);
            }

            if (!$syncLog->canRetry()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum retry attempts reached.',
                ], 400);
            }

            $branchId = $request->input('current_branch_id');

            // إعادة المحاولة
            $result = $this->syncService->retrySyncTransaction($syncLog, $branchId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction synced successfully on retry.',
                    'data' => [
                        'server_transaction_id' => $result['server_transaction_id'],
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Retry failed.',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('RetrySync API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retry sync.',
            ], 500);
        }
    }

    /**
     * جلب قائمة المعاملات المعلقة
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPendingTransactions(Request $request): JsonResponse
    {
        try {
            $branchId = $request->input('current_branch_id');

            $pending = OfflineSyncLog::forBranch($branchId)
                ->pending()
                ->oldest()
                ->limit(100)
                ->get(['local_transaction_id', 'created_at', 'sync_attempts']);

            return response()->json([
                'success' => true,
                'data' => $pending,
                'count' => $pending->count(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('GetPendingTransactions API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get pending transactions.',
            ], 500);
        }
    }
}
