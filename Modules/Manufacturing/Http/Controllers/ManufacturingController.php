<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Models\OperationItems;
use App\Models\OperHead;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Branches\Models\Branch;

class ManufacturingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Manufacturing Invoices')->only(['index', 'show', 'stageInvoicesReport', 'manufacturingStatistics']);
        $this->middleware('permission:create Manufacturing Invoices')->only(['create', 'store']);
        $this->middleware('permission:edit Manufacturing Invoices')->only(['edit', 'update']);
        $this->middleware('permission:delete Manufacturing Invoices')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $repository = app(\Modules\Manufacturing\Repositories\ManufacturingInvoiceRepository::class);

        // Get statistics
        $statistics = $repository->getStatistics();

        // Get branches for filter
        $branches = Branch::select('id', 'name')->get();

        // Get invoices with filters
        $query = \App\Models\OperHead::where('pro_type', 59)
            ->with(['acc1Head:id,aname', 'acc2Head:id,aname', 'employee:id,aname', 'branch:id,name', 'user:id,name']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pro_id', 'like', "%{$search}%")
                    ->orWhere('info', 'like', "%{$search}%");
            });
        }

        if ($request->filled('dateFrom')) {
            $query->where('pro_date', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->where('pro_date', '<=', $request->dateTo);
        }

        if ($request->filled('branchFilter')) {
            $query->where('branch_id', $request->branchFilter);
        }

        // Sorting
        $sortField = $request->get('sortField', 'pro_date');
        $sortDirection = $request->get('sortDirection', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('perPage', 15);
        $invoices = $query->paginate($perPage)->withQueryString();

        // Load users for created_by column (to avoid N+1 problem)
        $userIds = $invoices->pluck('user')->unique()->filter();
        $users = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();

        return view('manufacturing::manufacturing.index', compact('invoices', 'statistics', 'branches', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $dataService = app(\Modules\Manufacturing\Services\ManufacturingDataPreparationService::class);

        $data = $dataService->prepareCreateFormData(
            orderId: $request->query('order_id'),
            stageId: $request->query('stage_id')
        );

        // Extract data for view - convert arrays to collections of objects
        $nextInvoiceNumber = $data['nextProId'];
        $accounts = collect($data['stores'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $rawMaterialAccounts = collect($data['stores'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $employees = collect($data['employees'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $expenseAccounts = collect($data['expenseAccounts'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $operatingCenters = collect($data['operatingCenters'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $defaultOperatingAccount = $data['defaultOperatingAccount'];

        return view('manufacturing::manufacturing.create', compact(
            'nextInvoiceNumber',
            'accounts',
            'rawMaterialAccounts',
            'employees',
            'expenseAccounts',
            'operatingCenters',
            'defaultOperatingAccount'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Modules\Manufacturing\Http\Requests\StoreManufacturingInvoiceRequest $request)
    {
        try {
            // Parse JSON data from request
            $products = json_decode($request->input('products_data', '[]'), true) ?: [];
            $rawMaterials = json_decode($request->input('raw_materials_data', '[]'), true) ?: [];
            $expenses = json_decode($request->input('expenses_data', '[]'), true) ?: [];

            // Calculate totals
            $totalRawMaterials = collect($rawMaterials)->sum('total_cost');
            $totalExpenses = collect($expenses)->sum('amount');
            $totalProducts = collect($products)->sum('total_cost');
            $totalManufacturing = $totalRawMaterials + $totalExpenses;

            // Create a DTO object that mimics Livewire component structure
            $componentData = (object) [
                'pro_id' => $request->input('pro_id'),
                'rawAccount' => $request->input('acc2'), // Raw materials account
                'productAccount' => $request->input('acc1'), // Products account
                'employee' => $request->input('emp_id'),
                'invoiceDate' => $request->input('pro_date'),
                'OperatingAccount' => $request->input('operating_account'),
                'totalManufacturingCost' => $totalManufacturing,
                'totalRawMaterialsCost' => $totalRawMaterials,
                'totalProductsCost' => $totalProducts,
                'totalAdditionalExpenses' => $totalExpenses,
                'description' => $request->input('info', ''),
                'branch_id' => auth()->user()->current_branch_id ?? auth()->user()->branch_id,
                'order_id' => $request->input('order_id'),
                'stage_id' => $request->input('stage_id'),
                'actualTime' => $request->input('actual_time'),
                'pro_serial' => $request->input('pro_serial'),

                // Transform products data to match service expectations
                'selectedProducts' => collect($products)->map(function ($product) {
                    return [
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                        'unit_id' => $product['unit_id'] ?? null,
                        'unit_cost' => $product['unit_cost'],
                        'total_cost' => $product['total_cost'],
                        'cost_percentage' => $product['cost_percentage'] ?? 0,
                    ];
                })->toArray(),

                // Transform raw materials data to match service expectations
                'selectedRawMaterials' => collect($rawMaterials)->map(function ($material) {
                    return [
                        'item_id' => $material['id'],
                        'quantity' => $material['quantity'],
                        'unit_id' => $material['unit_id'] ?? null,
                        'unit_cost' => $material['unit_cost'],
                        'average_cost' => $material['average_cost'] ?? $material['unit_cost'],
                        'total_cost' => $material['total_cost'],
                    ];
                })->toArray(),

                // Transform expenses data to match service expectations
                'additionalExpenses' => collect($expenses)->map(function ($expense) {
                    return [
                        'amount' => $expense['amount'],
                        'account_id' => $expense['account_id'],
                        'description' => $expense['description'] ?? '',
                    ];
                })->toArray(),
            ];

            // Check if saving as template
            $isTemplate = $request->has('is_template') && ($request->is_template == 1 || $request->is_template == 'true');
            $templateName = $request->input('template_name', $request->input('info', ''));
            $expectedTime = $request->input('expected_time', $request->input('actual_time', ''));
            $endTime = $request->input('end_time', $request->input('actual_time', ''));
            $loadedTemplateName = $request->input('loaded_template_name', '');

            // Clean up empty time values
            $expectedTime = trim($expectedTime) !== '' ? $expectedTime : null;
            $endTime = trim($endTime) !== '' ? $endTime : null;

            if ($isTemplate) {
                $componentData->description = $templateName; // حفظ اسم النموذج في description
                $componentData->actualTime = $expectedTime; // حفظ الوقت المتوقع في حقل actualTime للنموذج
                $componentData->expectedTime = $expectedTime; // حفظ في expected_time أيضاً
                $componentData->endTime = null; // Templates don't have end time
                $componentData->details = null; // No details for templates
            } else {
                // Regular invoice
                // Convert hours to time format (HH:MM:SS) for expected_time
                if ($expectedTime !== null && is_numeric($expectedTime)) {
                    $hours = (int) $expectedTime;
                    $minutes = ($expectedTime - $hours) * 60;
                    $componentData->expectedTime = sprintf('%02d:%02d:00', $hours, (int) $minutes);
                } else {
                    $componentData->expectedTime = $expectedTime;
                }

                // Save actual time (end_time) as string in details field
                $componentData->endTime = null; // Don't use end_time field
                $componentData->details = $endTime; // Save actual time as string in details

                // If loaded from template, add template name to description
                if ($loadedTemplateName) {
                    $componentData->description = ($componentData->description ? $componentData->description.' - ' : '').
                                                 'من النموذج: '.$loadedTemplateName;
                }
            }

            // Use the existing ManufacturingInvoiceService
            $service = app(\Modules\Manufacturing\Services\ManufacturingInvoiceService::class);

            try {
                $invoiceId = $service->saveManufacturingInvoice($componentData, $isTemplate);
            } catch (\Exception $serviceException) {
                // Log the actual error
                \Log::error('Manufacturing invoice save exception', [
                    'error' => $serviceException->getMessage(),
                    'trace' => $serviceException->getTraceAsString(),
                    'component_data' => $componentData,
                    'is_template' => $isTemplate,
                ]);

                // Check if this is an AJAX request
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $isTemplate ? __('manufacturing::manufacturing.failed_to_save_template') : __('manufacturing::manufacturing.failed_to_create_invoice'),
                        'errors' => [
                            'general' => [$serviceException->getMessage()],
                        ],
                    ], 500);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', ($isTemplate ? __('manufacturing::manufacturing.failed_to_save_template') : __('manufacturing::manufacturing.failed_to_create_invoice')).': '.$serviceException->getMessage());
            }

            if ($invoiceId) {
                // Check if this is an AJAX request
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $isTemplate ? __('manufacturing::manufacturing.template_saved_successfully') : __('manufacturing::manufacturing.invoice_created_successfully'),
                        'invoice_id' => $invoiceId,
                        'redirect' => $isTemplate ? route('manufacturing.templates.index') : route('manufacturing.show', $invoiceId),
                    ]);
                }

                if ($isTemplate) {
                    return redirect()->route('manufacturing.templates.index')
                        ->with('success', __('manufacturing::manufacturing.template_saved_successfully'));
                }

                return redirect()->route('manufacturing.show', $invoiceId)
                    ->with('success', __('manufacturing::manufacturing.invoice_created_successfully'));
            } else {
                // Service returned false without exception - check logs for actual error
                \Log::error('Manufacturing invoice save failed without exception', [
                    'component_data' => $componentData,
                    'is_template' => $isTemplate,
                ]);

                // Check if this is an AJAX request
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $isTemplate ? __('manufacturing::manufacturing.failed_to_save_template') : __('manufacturing::manufacturing.failed_to_create_invoice'),
                        'errors' => [
                            'general' => [__('manufacturing::manufacturing.failed_to_create_invoice').'. '.__('manufacturing::manufacturing.check_logs_for_details')],
                        ],
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $isTemplate ? __('manufacturing::manufacturing.failed_to_save_template') : __('manufacturing::manufacturing.failed_to_create_invoice'));
            }
        } catch (\Exception $e) {
            // Check if this is an AJAX request
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('manufacturing::manufacturing.failed_to_create_invoice').': '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('manufacturing::manufacturing.failed_to_create_invoice').': '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = \App\Models\OperHead::with([
            'acc1Head',
            'acc2Head',
            'acc3Head',
            'employee',
            'store',
            'branch',
            'operationItems.item',
            'operationItems.unit',
        ])->findOrFail($id);

        // Parse products and raw materials
        $allItems = $invoice->operationItems()->with(['item', 'unit'])->get();

        $products = collect();
        $rawMaterials = collect();

        foreach ($allItems as $item) {
            $qtyIn = (float) ($item->qty_in ?? 0);
            $qtyOut = (float) ($item->qty_out ?? 0);
            $isProduct = false;

            if ($qtyIn > 0 && $qtyOut == 0) {
                $isProduct = true;
            } elseif ($qtyOut > 0 && $qtyIn == 0) {
                $isProduct = false;
            } elseif ($item->detail_store == $invoice->acc2) {
                if ($invoice->acc1 == $invoice->acc2) {
                    if (($item->additional ?? 0) > 0) {
                        $isProduct = true;
                    }
                } else {
                    $isProduct = true;
                }
            }

            if ($isProduct) {
                $products->push([
                    'name' => $item->item->name ?? '-',
                    'quantity' => $item->fat_quantity ?? $item->qty_in ?? 0,
                    'unit_cost' => $item->fat_price ?? $item->cost_price ?? 0,
                    'cost_percentage' => $item->additional ?? 0,
                    'total_cost' => $item->detail_value ?? 0,
                ]);
            } else {
                $unitName = $this->getDisplayUnitName($item);
                $rawMaterials->push([
                    'name' => $item->item->name ?? '-',
                    'quantity' => $item->fat_quantity ?? $item->qty_out ?? 0,
                    'unit_name' => $unitName,
                    'unit_cost' => $item->fat_price ?? $item->cost_price ?? 0,
                    'total_cost' => $item->detail_value ?? 0,
                ]);
            }
        }

        // Load expenses
        $expensesData = \App\Models\Expense::where('op_id', $invoice->id)->get();
        $accountIds = $expensesData->pluck('account_id')->filter()->unique();
        $accounts = \Modules\Accounts\Models\AccHead::whereIn('id', $accountIds)
            ->pluck('aname', 'id')
            ->toArray();

        $expenses = $expensesData->map(function ($expense) use ($accounts) {
            $description = str_replace('مصروف إضافي: ', '', $expense->description);
            $description = preg_replace('/ - فاتورة:.*$/', '', $description);

            return [
                'description' => trim($description),
                'account_name' => $accounts[$expense->account_id] ?? '-',
                'amount' => $expense->amount ?? 0,
            ];
        });

        // Calculate totals
        $totals = [
            'products' => $products->sum('total_cost'),
            'raw_materials' => $rawMaterials->sum('total_cost'),
            'expenses' => $expenses->sum('amount'),
            'manufacturing_cost' => 0,
            'discount_percentage' => (float) ($invoice->fat_disc_per ?? 0),
            'discount_value' => (float) ($invoice->fat_disc ?? 0),
            'tax_percentage' => (float) ($invoice->fat_tax_per ?? 0),
            'tax_value' => (float) ($invoice->fat_tax ?? 0),
            'vat_percentage' => (float) ($invoice->vat_percentage ?? 0),
            'vat_value' => (float) ($invoice->vat_value ?? 0),
            'withholding_tax_percentage' => (float) ($invoice->withholding_tax_percentage ?? 0),
            'withholding_tax_value' => (float) ($invoice->withholding_tax_value ?? 0),
        ];
        $totals['manufacturing_cost'] = $totals['raw_materials'] + $totals['expenses'];

        return view('manufacturing::show', compact('invoice', 'products', 'rawMaterials', 'expenses', 'totals'));
    }

    /**
     * Get display unit name for item
     */
    private function getDisplayUnitName($item): string
    {
        if ($item->fat_unit_id) {
            $displayUnit = \App\Models\Unit::find($item->fat_unit_id);
            if ($displayUnit) {
                return $displayUnit->name;
            }
        }

        $qtyOut = (float) ($item->qty_out ?? 0);
        $fatQty = (float) ($item->fat_quantity ?? 0);

        if ($qtyOut > 0 && $fatQty > 0 && $item->item) {
            $ratio = $qtyOut / $fatQty;
            $units = $item->item->units()->get();
            foreach ($units as $unit) {
                $uVal = (float) ($unit->pivot->u_val ?? 0);
                if ($uVal > 0 && abs($uVal - $ratio) < 0.0001) {
                    return $unit->name;
                }
            }
        }

        if ($item->unit) {
            return $item->unit->name;
        }

        return '-';
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dataService = app(\Modules\Manufacturing\Services\ManufacturingDataPreparationService::class);

        $data = $dataService->prepareEditFormData((int) $id);

        // Extract data for view - convert arrays to collections of objects
        $invoice = $data['invoice'];
        $accounts = collect($data['stores'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $rawMaterialAccounts = collect($data['stores'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $employees = collect($data['employees'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $expenseAccounts = collect($data['expenseAccounts'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        $operatingCenters = collect($data['operatingCenters'])->map(function ($name, $id) {
            return (object) ['id' => $id, 'aname' => $name];
        })->values();

        // Get products, raw materials, and expenses
        $products = $data['products'];
        $rawMaterials = $data['rawMaterials'];
        $expenses = $data['expenses'];

        return view('manufacturing::manufacturing.edit', compact(
            'invoice',
            'accounts',
            'rawMaterialAccounts',
            'employees',
            'expenseAccounts',
            'operatingCenters',
            'products',
            'rawMaterials',
            'expenses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $invoice = OperHead::findOrFail($id);

            // Validate basic fields
            $validated = $request->validate([
                'acc1' => 'required|exists:acc_head,id',
                'acc2' => 'required|exists:acc_head,id',
                'emp_id' => 'required|exists:acc_head,id',
                'pro_date' => 'required|date',
                'pro_id' => 'required|string',
                'patch_number' => 'nullable|string',
                'info' => 'nullable|string',
            ]);

            // Parse products, raw materials, and expenses from request
            $products = json_decode($request->input('products_data', '[]'), true);
            $rawMaterials = json_decode($request->input('raw_materials_data', '[]'), true);
            $expenses = json_decode($request->input('expenses_data', '[]'), true);

            // Calculate total value
            $totalValue = collect($products)->sum('total_cost');

            // Update invoice
            $invoice->update([
                'pro_id' => $validated['pro_id'],
                'pro_date' => $validated['pro_date'],
                'acc1' => $validated['acc1'],
                'acc2' => $validated['acc2'],
                'emp_id' => $validated['emp_id'],
                'pro_value' => $totalValue,
                'patch_number' => $validated['patch_number'] ?? null,
                'info' => $validated['info'] ?? null,
            ]);

            // Delete old items and expenses
            OperationItems::where('pro_id', $invoice->id)->delete();
            \App\Models\Expense::where('op_id', $invoice->id)->delete();

            // Add products (qty_in)
            foreach ($products as $product) {
                // Get unit factor to calculate base quantity
                $unitFactor = 1;
                if (isset($product['unit_id'])) {
                    $item = \App\Models\Item::with('units')->find($product['id']);
                    if ($item) {
                        $unit = $item->units->firstWhere('id', $product['unit_id']);
                        if ($unit) {
                            $unitFactor = $unit->pivot->u_val ?? 1;
                        }
                    }
                }

                // Calculate base quantity (qty_in = display quantity × unit factor)
                $displayQuantity = $product['quantity'];
                $baseQuantity = $displayQuantity * $unitFactor;

                // Calculate base price (cost_price = display price ÷ unit factor)
                $displayPrice = $product['unit_cost'];
                $basePrice = $unitFactor > 0 ? ($displayPrice / $unitFactor) : $displayPrice;

                OperationItems::create([
                    'pro_tybe' => 59,
                    'pro_id' => $invoice->id,
                    'item_id' => $product['id'],
                    'unit_id' => $product['unit_id'] ?? null,
                    'fat_unit_id' => $product['unit_id'] ?? null,
                    'qty_in' => $baseQuantity, // Base unit quantity
                    'fat_quantity' => $displayQuantity, // Display quantity
                    'cost_price' => $basePrice, // Base unit price
                    'fat_price' => $displayPrice, // Display price
                    'detail_value' => $product['total_cost'],
                    'detail_store' => $validated['acc1'],
                    'additional' => $product['cost_percentage'] ?? 0,
                ]);
            }

            // Add raw materials (qty_out)
            foreach ($rawMaterials as $material) {
                // Get unit factor to calculate base quantity
                $unitFactor = 1;
                if (isset($material['unit_id'])) {
                    $item = \App\Models\Item::with('units')->find($material['id']);
                    if ($item) {
                        $unit = $item->units->firstWhere('id', $material['unit_id']);
                        if ($unit) {
                            $unitFactor = $unit->pivot->u_val ?? 1;
                        }
                    }
                }

                // Calculate base quantity (qty_out = display quantity × unit factor)
                $displayQuantity = $material['quantity'];
                $baseQuantity = $displayQuantity * $unitFactor;

                // Calculate base price (cost_price = display price ÷ unit factor)
                $displayPrice = $material['unit_cost'];
                $basePrice = $unitFactor > 0 ? ($displayPrice / $unitFactor) : $displayPrice;

                OperationItems::create([
                    'pro_tybe' => 59,
                    'pro_id' => $invoice->id,
                    'item_id' => $material['id'],
                    'unit_id' => $material['unit_id'] ?? null,
                    'fat_unit_id' => $material['unit_id'] ?? null,
                    'qty_out' => $baseQuantity, // Base unit quantity
                    'fat_quantity' => $displayQuantity, // Display quantity
                    'cost_price' => $basePrice, // Base unit price
                    'fat_price' => $displayPrice, // Display price
                    'detail_value' => $material['total_cost'],
                    'detail_store' => $validated['acc2'],
                ]);
            }

            // Add expenses
            foreach ($expenses as $expense) {
                \App\Models\Expense::create([
                    'op_id' => $invoice->id,
                    'account_id' => $expense['account_id'],
                    'amount' => $expense['amount'],
                    'description' => __('manufacturing::manufacturing.additional_expense').': '.($expense['description'] ?? '').' - '.__('manufacturing::manufacturing.invoice').': '.$validated['pro_id'],
                    'expense_date' => $validated['pro_date'],
                ]);
            }

            DB::commit();

            // Update journal entries
            $this->updateJournalEntries($invoice, $totalValue, $validated);

            return redirect()->route('manufacturing.show', $invoice->id)
                ->with('success', __('manufacturing::manufacturing.invoice_updated_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', __('manufacturing::manufacturing.failed_to_update_invoice').': '.$e->getMessage());
        }
    }

    /**
     * Manufacturing statistics page
     */
    public function manufacturingStatistics()
    {
        // إجمالي عدد عمليات التصنيع
        $query = OperHead::where('pro_type', 59);
        $totalManufacturing = $query->count();

        // إجمالي تكلفة التصنيع
        $totalCost = $query->sum('pro_value');

        // متوسط تكلفة العملية
        $avgCost = $totalManufacturing > 0 ? round($totalCost / $totalManufacturing, 2) : 0;

        // أعلى وأقل تكلفة
        $maxCost = $query->max('pro_value') ?? 0;
        $minCost = $query->min('pro_value') ?? 0;

        // عمليات التصنيع خلال الشهر الحالي
        $currentMonthManufacturing = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y'))
            ->whereMonth('pro_date', date('m'))
            ->count();

        $currentMonthCost = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y'))
            ->whereMonth('pro_date', date('m'))
            ->sum('pro_value');

        // عمليات التصنيع خلال السنة الحالية
        $currentYearManufacturing = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y'))
            ->count();

        $currentYearCost = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y'))
            ->sum('pro_value');

        // أكثر 5 مواد خام استخدامًا
        $topRawMaterials = OperationItems::where('pro_tybe', 59)
            ->where('qty_out', '>', 0)
            ->selectRaw('item_id, COUNT(*) as count, SUM(detail_value) as total')
            ->groupBy('item_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with(['item:id,name'])
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item->name ?? __('Unknown'),
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            });

        // التصنيع حسب الأشهر
        $monthlyManufacturing = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $month = date('m', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));

            $count = OperHead::where('pro_type', 59)
                ->whereYear('pro_date', $year)
                ->whereMonth('pro_date', $month)
                ->count();

            $value = OperHead::where('pro_type', 59)
                ->whereYear('pro_date', $year)
                ->whereMonth('pro_date', $month)
                ->sum('pro_value');

            $monthName = [
                '01' => __('January'),
                '02' => __('February'),
                '03' => __('March'),
                '04' => __('April'),
                '05' => __('May'),
                '06' => __('June'),
                '07' => __('July'),
                '08' => __('August'),
                '09' => __('September'),
                '10' => __('October'),
                '11' => __('November'),
                '12' => __('December'),
            ][$month] ?? '';

            $monthlyManufacturing[] = [
                'month' => date('M Y', strtotime($date)),
                'month_ar' => $monthName.' '.$year,
                'count' => $count,
                'value' => $value,
            ];
        }

        // نطاقات التكاليف
        $costRanges = DB::table('operhead')
            ->where('pro_type', 59)
            ->select(
                DB::raw('CASE
                    WHEN pro_value < 100 THEN "'.__('Less than 100').'"
                    WHEN pro_value >= 100 AND pro_value < 500 THEN "'.__('100 - 500').'"
                    WHEN pro_value >= 500 AND pro_value < 1000 THEN "'.__('500 - 1000').'"
                    WHEN pro_value >= 1000 AND pro_value < 5000 THEN "'.__('1000 - 5000').'"
                    ELSE "'.__('More than 5000').'"
                END as `range`'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(pro_value) as total')
            )
            ->groupBy(DB::raw('`range`'))
            ->get()
            ->map(function ($item) {
                return [
                    'range' => $item->range,
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            });

        // أحدث عمليات التصنيع
        $recentManufacturing = OperHead::where('pro_type', 59)
            ->with(['acc1Head:id,aname', 'acc2Head:id,aname'])
            ->orderByDesc('pro_date')
            ->limit(10)
            ->get()
            ->map(function ($operation) {
                return [
                    'id' => $operation->id,
                    'pro_id' => $operation->pro_id,
                    'account_name' => $operation->acc1Head->aname ?? $operation->acc2Head->aname ?? '-',
                    'value' => $operation->pro_value,
                    'date' => $operation->pro_date,
                    'info' => $operation->info ?? '-',
                ];
            });

        // إحصائيات حسب الفرع
        $branchStats = OperHead::where('pro_type', 59)
            ->select('branch_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(pro_value) as total'))
            ->groupBy('branch_id')
            ->with('branch:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'branch_name' => $item->branch->name ?? __('Not Specified'),
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            });

        // مقارنة بين الشهر الحالي والسابق
        $lastMonthManufacturing = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y', strtotime('-1 month')))
            ->whereMonth('pro_date', date('m', strtotime('-1 month')))
            ->count();

        $lastMonthCost = OperHead::where('pro_type', 59)
            ->whereYear('pro_date', date('Y', strtotime('-1 month')))
            ->whereMonth('pro_date', date('m', strtotime('-1 month')))
            ->sum('pro_value');

        $countChange = $lastMonthManufacturing > 0
            ? round((($currentMonthManufacturing - $lastMonthManufacturing) / $lastMonthManufacturing) * 100, 2)
            : 0;

        $costChange = $lastMonthCost > 0
            ? round((($currentMonthCost - $lastMonthCost) / $lastMonthCost) * 100, 2)
            : 0;

        $statistics = compact(
            'totalManufacturing',
            'totalCost',
            'avgCost',
            'maxCost',
            'minCost',
            'currentMonthManufacturing',
            'currentMonthCost',
            'currentYearManufacturing',
            'currentYearCost',
            'topRawMaterials',
            'monthlyManufacturing',
            'costRanges',
            'recentManufacturing',
            'branchStats',
            'lastMonthManufacturing',
            'lastMonthCost',
            'countChange',
            'costChange'
        );

        return view('manufacturing::manufacturing.statistics', compact('statistics'));
    }

    /**
     * Stage invoices report page
     */
    public function stageInvoicesReport(Request $request)
    {
        // Set default date range
        $dateFrom = $request->get('dateFrom', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('dateTo', now()->endOfMonth()->format('Y-m-d'));
        $selectedOrderId = $request->get('selectedOrderId', '');
        $selectedStageId = $request->get('selectedStageId', '');
        $searchTerm = $request->get('searchTerm', '');

        // Get all manufacturing orders
        $orders = \Modules\Manufacturing\Models\ManufacturingOrder::with(['stages', 'invoices'])
            ->where('is_template', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stages for selected order
        $stages = collect();
        if ($selectedOrderId) {
            $order = \Modules\Manufacturing\Models\ManufacturingOrder::with('stages')->find($selectedOrderId);
            if ($order) {
                $stages = $order->stages;
            }
        }

        // Build query for invoices
        $invoicesQuery = OperHead::with(['manufacturingOrder', 'manufacturingStage', 'branch'])
            ->where('pro_type', 59)
            ->whereNotNull('manufacturing_order_id');

        // Apply filters
        if ($selectedOrderId) {
            $invoicesQuery->where('manufacturing_order_id', $selectedOrderId);
        }

        if ($selectedStageId) {
            $invoicesQuery->where('manufacturing_stage_id', $selectedStageId);
        }

        if ($dateFrom) {
            $invoicesQuery->whereDate('pro_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $invoicesQuery->whereDate('pro_date', '<=', $dateTo);
        }

        if ($searchTerm) {
            $invoicesQuery->where(function ($q) use ($searchTerm) {
                $q->where('pro_id', 'like', "%{$searchTerm}%")
                    ->orWhere('info', 'like', "%{$searchTerm}%");
            });
        }

        $invoices = $invoicesQuery->orderBy('pro_date', 'desc')->paginate(15)->withQueryString();

        // Calculate statistics
        $totalInvoices = $invoicesQuery->count();
        $totalValue = $invoicesQuery->sum('pro_value');

        return view('manufacturing::stage-invoices-report', compact(
            'orders',
            'stages',
            'invoices',
            'totalInvoices',
            'totalValue',
            'dateFrom',
            'dateTo',
            'selectedOrderId',
            'selectedStageId',
            'searchTerm'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = app(\Modules\Manufacturing\Services\ManufacturingInvoiceService::class);
            $result = $service->deleteManufacturingInvoice((int) $id);

            if ($result) {
                return redirect()->route('manufacturing.index')
                    ->with('success', __('Invoice deleted successfully'));
            }

            return redirect()->route('manufacturing.index')
                ->with('error', __('Invoice not found or could not be deleted.'));
        } catch (\Exception $e) {
            return redirect()->route('manufacturing.index')
                ->with('error', __('Failed to delete invoice: ').$e->getMessage());
        }
    }

    /**
     * Update journal entries for manufacturing invoice
     */
    private function updateJournalEntries($invoice, $totalValue, $validated): void
    {
        try {
            // Get existing journal entries
            $journalHeads = DB::table('journal_heads')
                ->where('op_id', $invoice->id)
                ->where('pro_type', 59)
                ->get();

            if ($journalHeads->isEmpty()) {
                return;
            }

            foreach ($journalHeads as $journalHead) {
                // Update journal head total
                DB::table('journal_heads')
                    ->where('id', $journalHead->id)
                    ->update([
                        'total' => $totalValue,
                        'date' => $validated['pro_date'],
                        'mdtime' => now(),
                    ]);

                // Update journal details
                DB::table('journal_details')
                    ->where('journal_id', $journalHead->id)
                    ->update([
                        'debit' => DB::raw("CASE WHEN debit > 0 THEN {$totalValue} ELSE 0 END"),
                        'credit' => DB::raw("CASE WHEN credit > 0 THEN {$totalValue} ELSE 0 END"),
                        'mdtime' => now(),
                    ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update journal entries', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
