<?php

namespace Modules\OfflinePOS\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\OfflinePOS\Services\InitDataService;

/**
 * API Controller لتحميل البيانات الأولية للعمل Offline
 * 
 * يوفر جميع البيانات المطلوبة للتخزين المحلي:
 * - الأصناف (مع وحداتها وأرصدتها وأسعارها)
 * - العملاء
 * - المخازن
 * - الموظفين
 * - الصناديق
 * - المستخدم الحالي وصلاحياته
 * - الإعدادات
 */
class InitDataController extends Controller
{
    protected InitDataService $initDataService;

    public function __construct(InitDataService $initDataService)
    {
        $this->initDataService = $initDataService;
    }

    /**
     * جلب جميع البيانات الأولية للعمل offline
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // التحقق من الصلاحية
            if (!Auth::user()->can('download offline pos data')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to download offline data.',
                ], 403);
            }

            // جلب branch_id من request (من middleware)
            $branchId = $request->input('current_branch_id');

            // جلب البيانات
            $data = $this->initDataService->getInitialData($branchId);

            return response()->json([
                'success' => true,
                'data' => $data['data'],
                'metadata' => $data['metadata'],
                'message' => 'Initial data loaded successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('InitData API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'branch_id' => $request->input('current_branch_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load initial data.',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * التحقق من تحديثات البيانات
     * يُستخدم للتحقق من وجود بيانات جديدة دون تحميل كل شيء
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUpdates(Request $request): JsonResponse
    {
        try {
            $branchId = $request->input('current_branch_id');
            $lastSyncTimestamp = $request->input('last_sync', null);

            $updates = $this->initDataService->checkForUpdates($branchId, $lastSyncTimestamp);

            return response()->json([
                'success' => true,
                'has_updates' => $updates['has_updates'],
                'updated_sections' => $updates['sections'],
                'message' => $updates['has_updates'] 
                    ? 'Updates available.' 
                    : 'No updates available.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('CheckUpdates API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check for updates.',
            ], 500);
        }
    }

    /**
     * تحميل بيانات قسم معين فقط (للتحديثات الجزئية)
     * 
     * @param Request $request
     * @param string $section (items, customers, stores, etc.)
     * @return JsonResponse
     */
    public function getSection(Request $request, string $section): JsonResponse
    {
        try {
            $branchId = $request->input('current_branch_id');
            
            $allowedSections = ['items', 'customers', 'stores', 'employees', 'cash_boxes', 'categories'];
            
            if (!in_array($section, $allowedSections)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid section requested.',
                ], 400);
            }

            $data = $this->initDataService->getSectionData($section, $branchId);

            return response()->json([
                'success' => true,
                'section' => $section,
                'data' => $data,
                'message' => ucfirst($section) . ' data loaded successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error("GetSection API Error [{$section}]: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load section data.',
            ], 500);
        }
    }
}
