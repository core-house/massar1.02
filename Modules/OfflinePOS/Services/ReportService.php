<?php

namespace Modules\OfflinePOS\Services;

use App\Models\OperHead;
use Illuminate\Support\Facades\DB;

/**
 * Service للتقارير
 */
class ReportService
{
    /**
     * أكثر الأصناف مبيعاً
     */
    public function getBestSellers(string $fromDate, string $toDate, int $limit = 10, ?int $branchId = null): array
    {
        $query = DB::table('operation_items')
            ->join('items', 'operation_items.item_id', '=', 'items.id')
            ->join('oper_heads', 'operation_items.op_id', '=', 'oper_heads.id')
            ->whereBetween('oper_heads.pro_date', [$fromDate, $toDate])
            ->where('oper_heads.pro_type', 10) // مبيعات فقط
            ->where('oper_heads.isdeleted', 0)
            ->select(
                'items.id',
                'items.name',
                'items.code',
                DB::raw('SUM(operation_items.qty_out) as total_quantity'),
                DB::raw('SUM(operation_items.detail_value) as total_value')
            )
            ->groupBy('items.id', 'items.name', 'items.code')
            ->orderBy('total_value', 'desc')
            ->limit($limit);

        return $query->get()->toArray();
    }

    /**
     * أفضل العملاء
     */
    public function getTopCustomers(string $fromDate, string $toDate, int $limit = 10, ?int $branchId = null): array
    {
        $query = DB::table('oper_heads')
            ->join('acc_heads', 'oper_heads.acc1', '=', 'acc_heads.id')
            ->whereBetween('oper_heads.pro_date', [$fromDate, $toDate])
            ->where('oper_heads.pro_type', 10)
            ->where('oper_heads.isdeleted', 0)
            ->select(
                'acc_heads.id',
                'acc_heads.aname as name',
                'acc_heads.code',
                DB::raw('COUNT(oper_heads.id) as transaction_count'),
                DB::raw('SUM(oper_heads.fat_net) as total_purchases')
            )
            ->groupBy('acc_heads.id', 'acc_heads.aname', 'acc_heads.code')
            ->orderBy('total_purchases', 'desc')
            ->limit($limit);

        return $query->get()->toArray();
    }

    /**
     * مبيعات يومية
     */
    public function getDailySales(string $date, ?int $branchId = null): array
    {
        $stats = OperHead::where('pro_type', 10)
            ->whereDate('pro_date', $date)
            ->where('isdeleted', 0)
            ->selectRaw('
                COUNT(*) as transaction_count,
                SUM(fat_total) as subtotal,
                SUM(fat_disc) as total_discount,
                SUM(fat_net) as total_sales,
                SUM(paid_from_client) as total_paid
            ')
            ->first();

        $itemsSold = DB::table('operation_items')
            ->join('oper_heads', 'operation_items.op_id', '=', 'oper_heads.id')
            ->whereDate('oper_heads.pro_date', $date)
            ->where('oper_heads.pro_type', 10)
            ->where('oper_heads.isdeleted', 0)
            ->sum('operation_items.qty_out');

        return [
            'date' => $date,
            'transaction_count' => $stats->transaction_count ?? 0,
            'subtotal' => $stats->subtotal ?? 0,
            'total_discount' => $stats->total_discount ?? 0,
            'total_sales' => $stats->total_sales ?? 0,
            'total_paid' => $stats->total_paid ?? 0,
            'items_sold' => $itemsSold ?? 0,
        ];
    }

    /**
     * ملخص المبيعات
     */
    public function getSalesSummary(string $fromDate, string $toDate, ?int $branchId = null): array
    {
        $summary = OperHead::where('pro_type', 10)
            ->whereBetween('pro_date', [$fromDate, $toDate])
            ->where('isdeleted', 0)
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(fat_total) as total_subtotal,
                SUM(fat_disc) as total_discount,
                SUM(fat_net) as total_sales,
                SUM(paid_from_client) as total_paid,
                AVG(fat_net) as average_transaction
            ')
            ->first();

        return [
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'total_transactions' => $summary->total_transactions ?? 0,
            'total_subtotal' => $summary->total_subtotal ?? 0,
            'total_discount' => $summary->total_discount ?? 0,
            'total_sales' => $summary->total_sales ?? 0,
            'total_paid' => $summary->total_paid ?? 0,
            'average_transaction' => round($summary->average_transaction ?? 0, 2),
        ];
    }
}
