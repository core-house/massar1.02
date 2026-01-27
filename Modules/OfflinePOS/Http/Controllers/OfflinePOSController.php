<?php

namespace Modules\OfflinePOS\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OperHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Main Controller للواجهة الرئيسية لـ Offline POS
 */
class OfflinePOSController extends Controller
{
    /**
     * الصفحة الرئيسية - Dashboard
     */
    public function index(Request $request)
    {
        $branchId = $request->input('current_branch_id');

        // إحصائيات اليوم
        $todayStats = [
            'total_sales' => OperHead::where('pro_type', 10)
                ->whereDate('pro_date', today())
                ->where('isdeleted', 0)
                ->sum('fat_net'),
            
            'transactions_count' => OperHead::where('pro_type', 10)
                ->whereDate('pro_date', today())
                ->where('isdeleted', 0)
                ->count(),
            
            'items_sold' => \DB::table('operation_items')
                ->join('oper_heads', 'operation_items.op_id', '=', 'oper_heads.id')
                ->whereDate('oper_heads.pro_date', today())
                ->where('oper_heads.pro_type', 10)
                ->where('oper_heads.isdeleted', 0)
                ->sum('operation_items.qty_out'),
        ];

        return view('offlinepos::index', compact('todayStats'));
    }

    /**
     * صفحة التثبيت - لتنزيل البيانات المحلية
     */
    public function install(Request $request)
    {
        $branchId = $request->input('current_branch_id');
        
        return view('offlinepos::install', compact('branchId'));
    }

    /**
     * شاشة نقاط البيع الرئيسية
     */
    public function pos(Request $request)
    {
        $branchId = $request->input('current_branch_id');
        
        return view('offlinepos::pos', compact('branchId'));
    }

    /**
     * عرض معاملة محددة
     */
    public function show(Request $request, $id)
    {
        $transaction = OperHead::with(['operationItems.item', 'acc1Head', 'acc2Head', 'employee'])
            ->where('pro_type', 10)
            ->findOrFail($id);

        return view('offlinepos::show', compact('transaction'));
    }

    /**
     * التقارير
     */
    public function reports(Request $request)
    {
        $branchId = $request->input('current_branch_id');
        
        return view('offlinepos::reports', compact('branchId'));
    }

    /**
     * صفحة Offline
     */
    public function offline()
    {
        return view('offlinepos::offline');
    }
}
