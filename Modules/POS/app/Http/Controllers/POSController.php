<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Item;
use App\Models\JournalDetail;
use App\Models\JournalHead;
use App\Models\OperHead;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounts\Models\AccHead;
use Modules\POS\Models\Driver;
use Modules\POS\Models\DeliveryArea;
use Modules\POS\Models\RestaurantTable;
use Modules\POS\app\Services\POSService;
use Modules\POS\app\Services\POSTransactionService;
use RealRashid\SweetAlert\Facades\Alert;

class POSController extends Controller
{
    public function __construct(
        private readonly POSService $posService,
        private readonly POSTransactionService $transactionService
    ) {}
    /**
     * عرض واجهة POS الرئيسية
     */
    public function index()
    {
        if (! auth()->check() || ! auth()->user()->can('view POS System')) {
            abort(403, 'ليس لديك صلاحية لاستخدام نظام نقاط البيع.');
        }

        $userId = auth()->id();
        $recentTransactions = $this->posService->getRecentTransactions($userId);
        $todayStats = $this->posService->getTodayStats($userId);

        return view('pos::index', compact('recentTransactions', 'todayStats'));
    }

    /**
     * إنشاء معاملة POS جديدة
     */
    public function create()
    {
        if (! auth()->check() || ! auth()->user()->can('create POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لإنشاء معاملات نقاط البيع.');
        }

        $data = $this->posService->getBaseCreateData();
        $itemsDataResult = $this->posService->getItemsData(50);

        $invoiceTypes = \App\Models\ProType::whereIn('id', [102, 103])
            ->select('id', 'ptext')
            ->get();

        return view('pos::create', array_merge($data, $itemsDataResult, [
            'invoiceTypes' => $invoiceTypes
        ]));
    }

    /**
     * واجهة POS المطعم
     */
    public function restaurant()
    {
        if (! auth()->check() || ! auth()->user()->can('create POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لاستخدام نظام نقاط البيع.');
        }

        $data = $this->posService->getRestaurantData();

        return view('pos::restaurant', $data);
    }
    public function searchItems(Request $request)
    {
        $searchTerm = $request->input('term', '');
        if (strlen($searchTerm) < 2) {
            return response()->json(['items' => []]);
        }

        $items = $this->posService->searchItems($searchTerm);
        return response()->json(['items' => $items]);
    }

    /**
     * البحث عن الأصناف بالباركود (AJAX) - محسّن للسرعة
     */
    public function searchByBarcode(Request $request)
    {
        $barcode = $request->input('barcode', '');
        if (empty($barcode)) {
            return response()->json(['items' => []]);
        }

        $result = $this->posService->searchByBarcode($barcode);
        return response()->json($result);
    }

    /**
     * جلب تفاصيل الصنف (AJAX)
     */
    public function getItemDetails($id)
    {
        $details = $this->posService->getItemDetails((int) $id);

        if (! $details) {
            return response()->json(['error' => 'الصنف غير موجود'], 404);
        }

        return response()->json($details);
    }

    /**
     * عرض شاشة فحص السعر بالباركود
     */
    public function priceCheck()
    {
        // التحقق من صلاحية الوصول
        if (! auth()->check() || ! auth()->user()->can('view POS System')) {
            abort(403, 'ليس لديك صلاحية لاستخدام شاشة فحص السعر.');
        }

        return view('pos::price-check');
    }

    /**
     * جلب بيانات السعر بالباركود (AJAX)
     */
    public function getPriceByBarcode($barcode)
    {
        $result = $this->posService->getPriceByBarcode($barcode);
        if (!$result['success']) {
            return response()->json($result, 404);
        }
        return response()->json($result);
    }

    /**
     * جلب كافة الأصناف (AJAX) - لتحديث البيانات محلياً
     */
    public function getAllItemsDetails()
    {
        $itemsData = $this->posService->getAllItemsDetails();
        return response()->json(['items' => $itemsData]);
    }

    /**
     * جلب أصناف التصنيف (AJAX)
     */
    public function getCategoryItems($categoryId)
    {
        $items = $this->posService->getCategoryItems((int) $categoryId);
        return response()->json(['items' => $items]);
    }

    /**
     * حفظ الفاتورة (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|integer',
            'customer_id' => 'nullable|exists:acc_head,id',
            'store_id' => 'nullable|exists:acc_head,id',
            'cash_account_id' => 'nullable|exists:acc_head,id',
            'bank_account_id' => 'nullable|exists:acc_head,id',
            'employee_id' => 'nullable|exists:acc_head,id',
            'payment_method' => 'nullable|string',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'table_id' => 'nullable|integer',
            'local_id' => 'nullable|uuid',
            'invoice_type' => 'nullable|integer|in:102,103', // 102=كاشير, 103=مطعم
            'order_type' => 'nullable|string|in:dining,takeaway,delivery',
            'driver_id' => 'nullable|integer',
            'contact_id' => 'nullable|integer',
            'price_group_id' => 'nullable|integer',
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        try {
            // ── فاتورة المطعم: تُعالج بـ RestaurantInvoiceService ──────────
            $invoiceType = (int) ($validated['invoice_type'] ?? 102);

            if ($invoiceType === 103) {
                $result = DB::transaction(function () use ($validated) {
                    return (new RestaurantInvoiceService())->save($validated);
                });

                // إطلاق حدث الطباعة للمطبخ
                $cashierTx = CashierTransaction::find($result['cashier_transaction_id']);
                if ($cashierTx) {
                    event(new \Modules\POS\app\Events\TransactionSaved($cashierTx));
                }

                return response()->json([
                    'success'        => true,
                    'message'        => 'تم الحفظ بنجاح',
                    'transaction_id' => $result['operhead_id'],
                    'invoice_number' => $result['invoice_number'],
                    'server_id'      => $result['cashier_transaction_id'],
                    'operhead_id'    => $result['operhead_id'],
                    'local_id'       => $validated['local_id'] ?? null,
                ]);
            }

            // ── فاتورة الكاشير (102): عبر CashierInvoiceService ──────────
            $branchId = Auth::user()->branch_id ?? 1;

            $result = DB::transaction(function () use ($validated, $branchId): array {
                return (new \Modules\POS\app\Services\CashierInvoiceService())
                    ->createFullInvoice($validated, Auth::id(), $branchId);
            });

            $cashierTx = CashierTransaction::find($result['cashier_transaction_id']);
            if ($cashierTx) {
                event(new \Modules\POS\app\Events\TransactionSaved($cashierTx));
            }

            return response()->json([
                'success'        => true,
                'message'        => 'تم الحفظ بنجاح',
                'transaction_id' => $result['operhead_id'],
                'invoice_number' => $result['invoice_number'],
                'server_id'      => $result['cashier_transaction_id'],
                'operhead_id'    => $result['operhead_id'],
                'local_id'       => $validated['local_id'] ?? null,
            ]);

            // ── الكود القديم (محتفظ به مؤقتاً للمرجعية - لن يُنفَّذ) ──────
            // @codeCoverageIgnoreStart
            $invoiceLabel = 'فاتورة كاشير';

            // حساب المبالغ
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }
            $discount = 0;
            $additional = 0;
            $total = $subtotal - $discount + $additional;
            $paidAmount = ($validated['cash_amount'] ?? 0) + ($validated['card_amount'] ?? 0);

            // جلب رقم الفاتورة التالي
            $nextProId = OperHead::max('pro_id') + 1 ?? 1;

            // تحديد الحسابات
            $customerId = $validated['customer_id'] ?? null;
            $cashAccountId = $validated['cash_account_id'] ?? null;
            $bankAccountId = $validated['bank_account_id'] ?? null;
            $storeId = $validated['store_id'] ?? null;
            $employeeId = $validated['employee_id'] ?? null;
            $branchId = Auth::user()->branch_id ?? 1;

            // تحديد حساب الدفع (صندوق للدفع النقدي، بنك للدفع بالبطاقة)
            $paymentAccountId = null;
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            if ($paymentMethod === 'cash' || $paymentMethod === 'mixed') {
                $paymentAccountId = $cashAccountId ?? $storeId;
            } elseif ($paymentMethod === 'card') {
                $paymentAccountId = $bankAccountId ?? $storeId;
            } else {
                $paymentAccountId = $cashAccountId ?? $bankAccountId ?? $storeId;
            }

            // إنشاء رأس المعاملة (OperHead)
            $operHead = OperHead::create([
                'pro_id' => $nextProId,
                'pro_date' => now()->format('Y-m-d'),
                'accural_date' => now()->format('Y-m-d'),
                'pro_type' => $invoiceType,
                'acc1' => $customerId, // العميل
                'acc2' => $paymentAccountId, // الصندوق أو البنك أو المخزن
                'store_id' => $storeId,
                'emp_id' => $employeeId,
                'fat_total' => $subtotal,
                'fat_disc' => $discount,
                'fat_disc_per' => 0,
                'fat_plus' => $additional,
                'fat_plus_per' => 0,
                'fat_net' => $total,
                'pro_value' => $total,
                'paid_from_client' => $paidAmount,
                'info' => $validated['notes'] ?? $invoiceLabel,
                'details' => $validated['notes'] ?? $invoiceLabel,
                'isdeleted' => 0,
                'is_stock' => 1, // معاملة مخزنية
                'is_finance' => 1, // معاملة مالية
                'is_journal' => 1, // تحتاج قيد محاسبي
                'journal_type' => 2,
                'user' => Auth::id(),
                'branch_id' => $branchId,
            ]);

            // إنشاء تفاصيل الأصناف (OperationItems)
            foreach ($validated['items'] as $item) {
                $itemModel = Item::find($item['id']);
                $unitId = $item['unit_id'] ?? $itemModel->units()->first()?->id ?? null;
                $quantity = $item['quantity'];
                $price = $item['price'];
                $totalValue = $quantity * $price;

                DB::table('operation_items')->insert([
                    'pro_id' => $operHead->id,
                    'item_id' => $item['id'],
                    'unit_id' => $unitId,
                    'qty_in' => 0,
                    'qty_out' => $quantity, // كمية خارجة (مبيعات)
                    'item_price' => $price,
                    'cost_price' => 0, // سيتم حسابه لاحقاً
                    'current_stock_value' => 0,
                    'item_discount' => 0,
                    'additional' => 0,
                    'detail_value' => $totalValue,
                    'profit' => 0,
                    'notes' => null,
                    'is_stock' => 1,
                    'isdeleted' => 0,
                    'branch_id' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ========== القيد الأول: قيد المبيعات ==========
            $lastJournalId = JournalHead::max('journal_id') ?? 0;
            $salesJournalId = $lastJournalId + 1;
            $salesJournalHead = JournalHead::create([
                'journal_id' => $salesJournalId,
                'total' => $total,
                'op_id' => $operHead->id,
                'pro_type' => $invoiceType,
                'date' => now()->format('Y-m-d'),
                'details' => 'قيد مبيعات - '.$invoiceLabel.' رقم '.$nextProId,
                'user' => Auth::id(),
                'branch_id' => $branchId,
            ]);

            // مدين - العميل (للمبيعات)
            if ($customerId) {
                JournalDetail::create([
                    'journal_id' => $salesJournalId,
                    'account_id' => $customerId,
                    'debit' => $total,
                    'credit' => 0,
                    'type' => 0,
                    'info' => 'مدين - عميل (مبيعات)',
                    'op_id' => $operHead->id,
                    'isdeleted' => 0,
                    'branch' => $branchId,
                ]);
            }

            // دائن - حساب المبيعات (47)
            JournalDetail::create([
                'journal_id' => $salesJournalId,
                'account_id' => 47, // حساب المبيعات
                'debit' => 0,
                'credit' => $total,
                'type' => 1,
                'info' => 'دائن - مبيعات',
                'op_id' => $operHead->id,
                'isdeleted' => 0,
                'branch' => $branchId,
            ]);

            // ========== القيد الثاني: قيد الدفع ==========
            $cashAmount = $validated['cash_amount'] ?? 0;
            $cardAmount = $validated['card_amount'] ?? 0;
            $paymentMethod = $validated['payment_method'] ?? 'cash';

            if ($paidAmount > 0) {
                if ($paymentMethod === 'mixed' && $cashAmount > 0 && $cardAmount > 0) {
                    // الدفع المختلط - قيدين منفصلين

                    // قيد الدفع النقدي
                    $cashJournalId = $salesJournalId + 1;
                    JournalHead::create([
                        'journal_id' => $cashJournalId,
                        'total' => $cashAmount,
                        'op_id' => $operHead->id,
                        'pro_type' => $invoiceType,
                        'date' => now()->format('Y-m-d'),
                        'details' => 'قيد دفع نقدي - '.$invoiceLabel.' رقم '.$nextProId,
                        'user' => Auth::id(),
                        'branch_id' => $branchId,
                    ]);

                    // مدين - الصندوق
                    if ($cashAccountId) {
                        JournalDetail::create([
                            'journal_id' => $cashJournalId,
                            'account_id' => $cashAccountId,
                            'debit' => $cashAmount,
                            'credit' => 0,
                            'type' => 0,
                            'info' => 'مدين - صندوق (دفع نقدي)',
                            'op_id' => $operHead->id,
                            'isdeleted' => 0,
                            'branch' => $branchId,
                        ]);
                    }

                    // دائن - العميل
                    if ($customerId) {
                        JournalDetail::create([
                            'journal_id' => $cashJournalId,
                            'account_id' => $customerId,
                            'debit' => 0,
                            'credit' => $cashAmount,
                            'type' => 1,
                            'info' => 'دائن - عميل (دفع نقدي)',
                            'op_id' => $operHead->id,
                            'isdeleted' => 0,
                            'branch' => $branchId,
                        ]);
                    }

                    // قيد الدفع بالبطاقة
                    $cardJournalId = $cashJournalId + 1;
                    JournalHead::create([
                        'journal_id' => $cardJournalId,
                        'total' => $cardAmount,
                        'op_id' => $operHead->id,
                        'pro_type' => $invoiceType,
                        'date' => now()->format('Y-m-d'),
                        'details' => 'قيد دفع بالبطاقة - '.$invoiceLabel.' رقم '.$nextProId,
                        'user' => Auth::id(),
                        'branch_id' => $branchId,
                    ]);

                    // مدين - البنك
                    if ($bankAccountId) {
                        JournalDetail::create([
                            'journal_id' => $cardJournalId,
                            'account_id' => $bankAccountId,
                            'debit' => $cardAmount,
                            'credit' => 0,
                            'type' => 0,
                            'info' => 'مدين - بنك (دفع بالبطاقة)',
                            'op_id' => $operHead->id,
                            'isdeleted' => 0,
                            'branch' => $branchId,
                        ]);
                    }

                    // دائن - العميل
                    if ($customerId) {
                        JournalDetail::create([
                            'journal_id' => $cardJournalId,
                            'account_id' => $customerId,
                            'debit' => 0,
                            'credit' => $cardAmount,
                            'type' => 1,
                            'info' => 'دائن - عميل (دفع بالبطاقة)',
                            'op_id' => $operHead->id,
                            'isdeleted' => 0,
                            'branch' => $branchId,
                        ]);
                    }
                } else {
                    // دفع واحد (نقدي أو بطاقة)
                    $paymentAccountId = null;
                    $paymentAmount = 0;

                    if ($paymentMethod === 'cash' || ($paymentMethod === 'mixed' && $cashAmount > 0)) {
                        $paymentAccountId = $cashAccountId ?? $storeId;
                        $paymentAmount = $cashAmount > 0 ? $cashAmount : $paidAmount;
                    } elseif ($paymentMethod === 'card' || ($paymentMethod === 'mixed' && $cardAmount > 0)) {
                        $paymentAccountId = $bankAccountId ?? $storeId;
                        $paymentAmount = $cardAmount > 0 ? $cardAmount : $paidAmount;
                    }

                    if ($paymentAccountId && $paymentAmount > 0) {
                        $paymentJournalId = $salesJournalId + 1;
                        JournalHead::create([
                            'journal_id' => $paymentJournalId,
                            'total' => $paymentAmount,
                            'op_id' => $operHead->id,
                            'pro_type' => $invoiceType,
                            'date' => now()->format('Y-m-d'),
                            'details' => 'قيد دفع - '.$invoiceLabel.' رقم '.$nextProId,
                            'user' => Auth::id(),
                            'branch_id' => $branchId,
                        ]);

                        // مدين - الصندوق أو البنك
                        JournalDetail::create([
                            'journal_id' => $paymentJournalId,
                            'account_id' => $paymentAccountId,
                            'debit' => $paymentAmount,
                            'credit' => 0,
                            'type' => 0,
                            'info' => 'مدين - '.($cashAccountId ? 'صندوق' : 'بنك'),
                            'op_id' => $operHead->id,
                            'isdeleted' => 0,
                            'branch' => $branchId,
                        ]);

                        // دائن - العميل
                        if ($customerId) {
                            JournalDetail::create([
                                'journal_id' => $paymentJournalId,
                                'account_id' => $customerId,
                                'debit' => 0,
                                'credit' => $paymentAmount,
                                'type' => 1,
                                'info' => 'دائن - عميل (دفع)',
                                'op_id' => $operHead->id,
                                'isdeleted' => 0,
                                'branch' => $branchId,
                            ]);
                        }
                    }
                }
            }

            // @codeCoverageIgnoreEnd
        } catch (\Exception $e) {
            \Log::error('POS Store Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحفظ: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * تعليق الفاتورة (Hold/Suspend Order)
     */
    public function holdOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'customer_id' => 'nullable|exists:acc_head,id',
            'store_id' => 'nullable|exists:acc_head,id',
            'cash_account_id' => 'nullable|exists:acc_head,id',
            'bank_account_id' => 'nullable|exists:acc_head,id',
            'employee_id' => 'nullable|exists:acc_head,id',
            'payment_method' => 'nullable|string',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'table_id' => 'nullable|integer',
            'local_id' => 'nullable|uuid',
        ]);

        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $userId = auth()->id();
            
            $heldOrder = $this->transactionService->holdOrder($validated, $userId, $branchId);

            return response()->json([
                'success' => true,
                'message' => 'تم تعليق الفاتورة بنجاح',
                'held_order_id' => $heldOrder->id,
                'local_id' => $heldOrder->local_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Hold Order Error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تعليق الفاتورة'], 500);
        }
    }

    /**
     * جلب قائمة الفواتير المعلقة
     */
    public function getHeldOrders(Request $request)
    {
        try {
            $branchId = Auth::user()->branch_id ?? 1;

            $heldOrders = CashierTransaction::with(['customer:id,aname', 'store:id,aname', 'user:id,name'])
                ->held()
                ->where('branch_id', $branchId)
                ->orderBy('held_at', 'desc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'local_id' => $order->local_id,
                        'total' => (float) $order->total,
                        'customer_name' => $order->customer->aname ?? 'عميل نقدي',
                        'store_name' => $order->store->aname ?? 'غير محدد',
                        'user_name' => $order->user->name ?? 'غير محدد',
                        'items_count' => count($order->items ?? []),
                        'held_at' => $order->held_at->format('Y-m-d H:i:s'),
                        'held_at_formatted' => $order->held_at->diffForHumans(),
                        'notes' => $order->notes,
                    ];
                });

            return response()->json([
                'success' => true,
                'held_orders' => $heldOrders,
                'count' => $heldOrders->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching held orders: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الفواتير المعلقة',
                'held_orders' => [],
            ], 500);
        }
    }

    /**
     * استدعاء فاتورة معلقة (Recall/Resume Order)
     */
    public function recallOrder($id)
    {
        try {
            $branchId = Auth::user()->branch_id ?? 1;

            $heldOrder = CashierTransaction::held()
                ->where('branch_id', $branchId)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $heldOrder->id,
                    'local_id' => $heldOrder->local_id,
                    'customer_id' => $heldOrder->customer_id,
                    'store_id' => $heldOrder->store_id,
                    'cash_account_id' => $heldOrder->cash_account_id,
                    'bank_account_id' => null, // يمكن إضافته لاحقاً
                    'employee_id' => $heldOrder->employee_id,
                    'items' => $heldOrder->items,
                    'subtotal' => (float) $heldOrder->subtotal,
                    'discount' => (float) $heldOrder->discount,
                    'additional' => (float) $heldOrder->additional,
                    'total' => (float) $heldOrder->total,
                    'payment_method' => $heldOrder->payment_method ?? 'cash',
                    'cash_amount' => (float) $heldOrder->cash_amount,
                    'card_amount' => (float) $heldOrder->card_amount,
                    'notes' => $heldOrder->notes,
                    'table_id' => $heldOrder->table_id,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error recalling held order: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استدعاء الفاتورة: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * إكمال فاتورة معلقة (Complete Held Order)
     */
    public function completeHeldOrder(Request $request, $id)
    {
        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $userId = auth()->id();

            $result = $this->transactionService->completeHeldOrder((int) $id, $userId, $branchId);

            return response()->json([
                'success' => true,
                'message' => 'تم إكمال الفاتورة بنجاح',
                'transaction_id' => $result['transaction_id'],
                'invoice_number' => $result['invoice_number'],
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Complete Held Order Error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إكمال الفاتورة'], 500);
        }
    }

    /**
     * حذف فاتورة معلقة
     */
    public function deleteHeldOrder($id)
    {
        try {
            $branchId = Auth::user()->branch_id ?? 1;

            $heldOrder = CashierTransaction::held()
                ->where('branch_id', $branchId)
                ->findOrFail($id);

            $heldOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الفاتورة المعلقة بنجاح',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting held order: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الفاتورة: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * مزامنة المعاملات المعلقة (للـ offline sync).
     *
     * يُنشئ الفاتورة الكاملة (OperHead + OperationItems + قيود محاسبية)
     * بنفس منطق store() عبر CashierInvoiceService.
     */
    public function syncTransactions(Request $request)
    {
        $transactions = $request->input('transactions', []);

        if (empty($transactions)) {
            return response()->json(['success' => false, 'message' => 'لا توجد معاملات للمزامنة'], 400);
        }

        $synced  = [];
        $failed  = [];
        $userId  = Auth::id();
        $branchId = Auth::user()->branch_id ?? 1;

        $cashierService     = new \Modules\POS\app\Services\CashierInvoiceService();
        $restaurantService  = new RestaurantInvoiceService();

        foreach ($transactions as $txData) {
            try {
                $invoiceType = (int) ($txData['invoice_type'] ?? 102);

                $result = DB::transaction(function () use ($txData, $invoiceType, $userId, $branchId, $cashierService, $restaurantService): array {
                    if ($invoiceType === 103) {
                        // فاتورة مطعم → RestaurantInvoiceService
                        $r = $restaurantService->save($txData);
                        return [
                            'cashier_transaction_id' => $r['cashier_transaction_id'],
                            'operhead_id'            => $r['operhead_id'],
                            'invoice_number'         => $r['invoice_number'],
                        ];
                    }

                    // فاتورة كاشير → CashierInvoiceService (ينشئ الفاتورة الكاملة)
                    return $cashierService->createFullInvoice($txData, $userId, $branchId);
                });

                $synced[] = [
                    'local_id'    => $txData['local_id'] ?? null,
                    'server_id'   => $result['cashier_transaction_id'],
                    'operhead_id' => $result['operhead_id'],
                ];
            } catch (\Exception $e) {
                \Log::error('POS Sync Error', [
                    'local_id' => $txData['local_id'] ?? null,
                    'error'    => $e->getMessage(),
                ]);
                $failed[] = [
                    'local_id' => $txData['local_id'] ?? null,
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'synced'  => $synced,
            'failed'  => $failed,
            'message' => 'تمت المزامنة',
        ]);
    }

    /**
     * عرض معاملة POS محددة
     */
    public function show($id)
    {
        $transaction = OperHead::with(['operationItems.item', 'operationItems.unit', 'acc1Head', 'acc2Head', 'employee', 'user'])
            ->where('pro_type', 102) // فواتير كاشير
            ->where('isdeleted', 0)
            ->findOrFail($id);

        // التحقق من الصلاحية
        if (! auth()->check() || ! auth()->user()->can('view POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لعرض معاملات نقاط البيع.');
        }

        return view('pos::show', compact('transaction'));
    }

    /**
     * تحرير معاملة POS
     */
    public function edit($id)
    {
        // التحقق من الصلاحية
        if (! auth()->check() || ! auth()->user()->can('edit POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لتحرير معاملات نقاط البيع.');
        }

        $data = $this->posService->getEditData((int) $id);

        return view('pos::edit', $data);
    }

    /**
     * تحديث معاملة POS
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'customer_id' => 'nullable|exists:acc_head,id',
            'store_id' => 'nullable|exists:acc_head,id',
            'cash_account_id' => 'nullable|exists:acc_head,id',
            'employee_id' => 'nullable|exists:acc_head,id',
            'payment_method' => 'nullable|string',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'table_id' => 'nullable|integer',
        ]);

        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $userId = auth()->id();

            $this->transactionService->updateTransaction((int) $id, $validated, $userId, $branchId);

            Alert::toast('تم تحديث المعاملة بنجاح', 'success');

            return redirect()->route('pos.show', $id);
        } catch (\Exception $e) {
            \Log::error('POS Update Error: '.$e->getMessage());

            Alert::toast('حدث خطأ أثناء التحديث: '.$e->getMessage(), 'error');

            return redirect()->back()->withInput();
        }
    }

    /**
     * طباعة فاتورة POS - 7.8 cm (80mm)
     */
    public function print($operation_id)
    {
        $operation = OperHead::with(['operationItems.item', 'operationItems.unit', 'acc1Head', 'acc2Head', 'user'])->findOrFail($operation_id);

        if ($operation->pro_type !== 102 && $operation->pro_type !== 10) {
            abort(404, 'المعاملة المطلوبة غير موجودة.');
        }

        if (! auth()->check() || ! auth()->user()->can('print POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لطباعة فواتير نقاط البيع.');
        }

        return view('pos::print', [
            'operation' => $operation,
            'pro_id' => $operation->pro_id,
            'pro_date' => $operation->pro_date,
            'accural_date' => $operation->accural_date,
            'serial_number' => $operation->pro_serial,
            'acc1List' => collect([$operation->acc1Head])->filter(),
            'acc2List' => collect([$operation->acc2Head])->filter(),
            'items' => Item::whereIn('id', $operation->operationItems->pluck('item_id'))->get(),
            'invoiceItems' => $operation->operationItems->map(fn($item) => [
                'item_id' => $item->item_id, 'unit_id' => $item->unit_id, 'quantity' => $item->qty_out,
                'price' => $item->item_price, 'discount' => $item->item_discount ?? 0, 'sub_value' => $item->detail_value,
                'item_name' => $item->item->name ?? 'غير محدد', 'unit_name' => $item->unit->name ?? 'قطعة',
            ])->toArray(),
            'subtotal' => $operation->fat_total ?? 0,
            'discount_percentage' => $operation->fat_disc_per ?? 0,
            'discount_value' => $operation->fat_disc ?? 0,
            'additional_percentage' => $operation->fat_plus_per ?? 0,
            'additional_value' => $operation->fat_plus ?? 0,
            'total_after_additional' => $operation->fat_net ?? $operation->pro_value ?? 0,
            'received_from_client' => $operation->paid_from_client ?? 0,
            'notes' => $operation->info ?? $operation->details ?? '',
        ]);
    }

    /**
     * حذف معاملة POS
     */
    public function destroy($id)
    {
        if (! auth()->check() || ! auth()->user()->can('delete POS Transaction')) {
            abort(403, 'ليس لديك صلاحية لحذف معاملات نقاط البيع.');
        }

        try {
            $this->transactionService->deleteTransaction((int) $id);
            Alert::toast('تم حذف المعاملة وسنداتها بنجاح.', 'success');
            return redirect()->route('pos.index');
        } catch (\Exception $e) {
            Alert::toast('حدث خطأ أثناء حذف المعاملة: '.$e->getMessage(), 'error');
            return redirect()->back();
        }
    }

    /**
     * البحث عن العميل بالتليفون (AJAX)
     */
    public function searchCustomerByPhone(Request $request)
    {
        $phone = trim($request->input('phone', ''));
        $loadAll = $request->boolean('load_all');
        
        $result = $this->posService->searchCustomerByPhone($phone, $loadAll);
        return response()->json($result);
    }

    /**
     * جلب رصيد العميل (AJAX)
     */
    public function getCustomerBalance($customerId)
    {
        try {
            $result = $this->posService->getCustomerBalance((int) $customerId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'balance' => 0, 'message' => 'حدث خطأ أثناء جلب الرصيد'], 500);
        }
    }

    /**
     * جلب آخر 50 عملية POS (AJAX)
     */
    public function getRecentTransactions(Request $request)
    {
        try {
            $limit = $request->input('limit', 50);
            $userId = auth()->id();
            $transactions = $this->posService->getRecentTransactions($userId, (int) $limit);

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'count' => $transactions->count(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching recent transactions: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء جلب العمليات', 'transactions' => []], 500);
        }
    }

    /**
     * تقارير POS
     */
    public function reports()
    {
        if (! auth()->check() || ! auth()->user()->can('view POS Reports')) {
            abort(403, 'ليس لديك صلاحية لعرض تقارير نقاط البيع.');
        }

        $todayStats = $this->posService->getReportsData();
        return view('pos::reports', compact('todayStats'));
    }

    /**
     * تسجيل مصروف نثري (Petty Cash)
     */
    public function pettyCash(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'cash_account_id' => 'required|exists:acc_head,id',
            'expense_account_id' => 'required|exists:acc_head,id',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $userId = auth()->id();
            $result = $this->transactionService->recordPettyCash($validated, $userId, $branchId);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل المصروف النثري بنجاح',
                'voucher_id' => $result['voucher_id'],
                'voucher_number' => $result['voucher_number'],
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Pay Out Error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تسجيل المصروف'], 500);
        }
    }

    /**
     * جلب إعدادات الميزان (AJAX)
     */
    public function getScaleSettings()
    {
        $result = $this->posService->getScaleSettings();
        return response()->json($result);
    }

    /**
     * عرض صفحة إعدادات الكاشير
     */
    public function settings()
    {
        if (! auth()->check() || ! auth()->user()->can('view POS System')) {
            abort(403, 'ليس لديك صلاحية لعرض إعدادات الكاشير.');
        }

        $data = $this->posService->getSettingsData();
        return view('pos::settings', $data);
    }

    /**
     * تحديث إعدادات الكاشير
     */
    public function updateSettings(Request $request)
    {
        if (! auth()->check() || ! auth()->user()->can('view POS System')) {
            return response()->json(['success' => false, 'message' => 'ليس لديك صلاحية لتعديل إعدادات الكاشير.'], 403);
        }

        $validated = $request->validate([
            'def_pos_client' => 'nullable|exists:acc_head,id',
            'def_pos_store' => 'nullable|exists:acc_head,id',
            'def_pos_employee' => 'nullable|exists:acc_head,id',
            'def_pos_fund' => 'nullable|exists:acc_head,id',
            'def_pos_bank' => 'nullable|exists:acc_head,id',
            'def_pos_price_group' => 'nullable|exists:prices,id',
            'enable_scale_items' => 'nullable|boolean',
            'scale_code_prefix' => 'nullable|string|max:10',
            'scale_code_digits' => 'nullable|integer|min:1|max:10',
            'scale_quantity_digits' => 'nullable|integer|min:1|max:10',
            'scale_quantity_divisor' => 'nullable|integer|in:10,100,1000',
            'restaurant_kitchen_store' => 'nullable|exists:acc_head,id',
            'restaurant_operating_account' => 'nullable|exists:acc_head,id',
            'restaurant_sales_account' => 'nullable|exists:acc_head,id',
            'restaurant_cogs_account' => 'nullable|exists:acc_head,id',
            'restaurant_inventory_account' => 'nullable|exists:acc_head,id',
        ]);

        try {
            $this->transactionService->updateSettings($validated);
            return response()->json(['success' => true, 'message' => 'تم تحديث إعدادات الكاشير بنجاح']);
        } catch (\Exception $e) {
            \Log::error('POS Settings Update Error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث الإعدادات'], 500);
        }
    }

    /**
     * حفظ عميل توصيل جديد (AJAX)
     */
    public function saveDeliveryCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $customer = $this->transactionService->saveDeliveryCustomer($validated, $branchId);
            return response()->json(['success' => true, 'customer' => $customer, 'message' => __('pos.customer_saved')]);
        } catch (\Exception $e) {
            \Log::error('Save delivery customer error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء حفظ العميل'], 500);
        }
    }

    /**
     * تحديث عنوان عميل التوصيل (AJAX)
     */
    public function updateDeliveryCustomerAddress(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:clients,id',
            'address' => 'required|string|max:500',
            'field' => 'required|in:address,address2',
        ]);

        try {
            $this->transactionService->updateDeliveryCustomerAddress($validated['customer_id'], $validated['address'], $validated['field']);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Update delivery customer address error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث العنوان'], 500);
        }
    }

    /**
     * جلب آخر 3 طلبات لعميل (AJAX)
     */
    public function getCustomerRecommendations($customerId)
    {
        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $result = $this->posService->getCustomerRecommendations((int) $customerId, $branchId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'orders' => [], 'message' => $e->getMessage()], 500);
        }
    }

    public function getInvoice($proId)
    {
        try {
            $result = $this->posService->getInvoiceDetails((int) $proId);
            if (!$result) return response()->json(['success' => false, 'message' => 'الفاتورة غير موجودة'], 404);
            return response()->json(['success' => true, 'invoice' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ'], 500);
        }
    }

    /**
     * إرجاع فاتورة كاشير
     */
    public function returnInvoice(Request $request)
    {
        $validated = $request->validate([
            'original_invoice_id' => 'required|exists:oper_head,id',
            'cash_account_id' => 'nullable|exists:acc_head,id',
            'bank_account_id' => 'nullable|exists:acc_head,id',
            'payment_method' => 'nullable|string|in:cash,card,mixed',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $branchId = auth()->user()->branch_id ?? 1;
            $userId = auth()->id();
            $result = $this->transactionService->returnInvoice((int) $validated['original_invoice_id'], $validated, $userId, $branchId);

            return response()->json([
                'success' => true,
                'message' => 'تم إرجاع فاتورة الكاشير بنجاح',
                'return_invoice_id' => $result['return_invoice_id'],
                'return_invoice_number' => $result['return_invoice_number'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Return Cashier Invoice Error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الإرجاع'], 500);
        }
    }
}
