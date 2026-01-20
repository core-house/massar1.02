<?php

namespace Modules\OfflinePOS\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\OfflinePOS\Services\ReportService;

/**
 * API Controller للتقارير
 */
class ReportsController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * أكثر الأصناف مبيعاً
     */
    public function bestSellers(Request $request): JsonResponse
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->input('to_date', now()->format('Y-m-d'));
            $limit = $request->input('limit', 10);
            $branchId = $request->input('current_branch_id');

            $data = $this->reportService->getBestSellers($fromDate, $toDate, $limit, $branchId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('BestSellers Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
            ], 500);
        }
    }

    /**
     * أفضل العملاء
     */
    public function topCustomers(Request $request): JsonResponse
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->input('to_date', now()->format('Y-m-d'));
            $limit = $request->input('limit', 10);
            $branchId = $request->input('current_branch_id');

            $data = $this->reportService->getTopCustomers($fromDate, $toDate, $limit, $branchId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('TopCustomers Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
            ], 500);
        }
    }

    /**
     * مبيعات يومية
     */
    public function dailySales(Request $request): JsonResponse
    {
        try {
            $date = $request->input('date', now()->format('Y-m-d'));
            $branchId = $request->input('current_branch_id');

            $data = $this->reportService->getDailySales($date, $branchId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('DailySales Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
            ], 500);
        }
    }

    /**
     * ملخص المبيعات
     */
    public function salesSummary(Request $request): JsonResponse
    {
        try {
            $fromDate = $request->input('from_date', now()->startOfMonth()->format('Y-m-d'));
            $toDate = $request->input('to_date', now()->format('Y-m-d'));
            $branchId = $request->input('current_branch_id');

            $data = $this->reportService->getSalesSummary($fromDate, $toDate, $branchId);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            Log::error('SalesSummary Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report.',
            ], 500);
        }
    }
}
