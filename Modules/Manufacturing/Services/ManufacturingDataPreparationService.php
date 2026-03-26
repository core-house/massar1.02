<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Services;

use App\Models\OperHead;
use Modules\Manufacturing\Repositories\ManufacturingDataRepository;

class ManufacturingDataPreparationService
{
    public function __construct(
        private ManufacturingDataRepository $repository
    ) {}

    /**
     * Prepare data for create form
     */
    public function prepareCreateFormData(?int $orderId = null, ?int $stageId = null): array
    {
        $branches = userBranches();
        $defaultBranchId = $branches->first()?->id;

        // Get next invoice number
        $nextProId = OperHead::where('pro_type', 59)->max('pro_id') + 1;

        // Get accounts by acc_type (6 = Warehouses, 5 = Employees, 7 = Expenses)
        $stores = \Modules\Accounts\Models\AccHead::where('acc_type', 6)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();
        
        $operatingCenters = $this->repository->getAccountsByCode('1108%');
        
        $employees = \Modules\Accounts\Models\AccHead::where('acc_type', 5)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();
        
        $expenseAccounts = \Modules\Accounts\Models\AccHead::where('acc_type', 7)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();

        // Find main store or use first store
        $defaultStoreId = array_key_first($stores);
        
        // Default values
        $defaultRawAccount = $defaultStoreId;
        $defaultProductAccount = $defaultStoreId;
        $defaultOperatingAccount = array_key_first($operatingCenters);
        $defaultEmployee = array_key_first($employees);
        $defaultExpenseAccount = array_key_first($expenseAccounts);

        return [
            'invoiceId' => null,
            'orderId' => $orderId,
            'stageId' => $stageId,
            'nextProId' => $nextProId,
            'branches' => $branches,
            'defaultBranchId' => $defaultBranchId,
            'stores' => $stores,
            'operatingCenters' => $operatingCenters,
            'employees' => $employees,
            'expenseAccounts' => $expenseAccounts,
            'defaultRawAccount' => $defaultRawAccount,
            'defaultProductAccount' => $defaultProductAccount,
            'defaultOperatingAccount' => $defaultOperatingAccount,
            'defaultEmployee' => $defaultEmployee,
            'defaultExpenseAccount' => $defaultExpenseAccount,
        ];
    }

    /**
     * Prepare data for edit form
     */
    public function prepareEditFormData(int $invoiceId): array
    {
        $invoice = OperHead::with([
            'operationItems.item.units',
            'branch',
            'acc1Head',
            'acc2Head',
            'employee'
        ])->findOrFail($invoiceId);

        $branches = userBranches();

        // Get accounts by acc_type (6 = Warehouses, 5 = Employees, 7 = Expenses)
        $stores = \Modules\Accounts\Models\AccHead::where('acc_type', 6)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();
        
        $operatingCenters = $this->repository->getAccountsByCode('1108%');
        
        $employees = \Modules\Accounts\Models\AccHead::where('acc_type', 5)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();
        
        $expenseAccounts = \Modules\Accounts\Models\AccHead::where('acc_type', 7)
            ->where('isdeleted', 0)
            ->where('is_basic', 0)
            ->pluck('aname', 'id')
            ->toArray();

        // Parse invoice data
        $products = [];
        $rawMaterials = [];
        $expenses = [];

        foreach ($invoice->operationItems as $item) {
            if ($item->qty_in > 0) {
                // Product (output)
                $itemData = $item->item;
                $units = $itemData && $itemData->units ? $itemData->units->map(function($unit) {
                    return [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'u_val' => $unit->pivot->u_val ?? 1,
                    ];
                })->toArray() : [];

                // استخدام fat_quantity و fat_price إذا كانت موجودة
                // إذا كان fat_quantity موجود وأكبر من 0، استخدمه
                // وإلا استخدم qty_in (الكمية بالوحدة الأساسية)
                if ($item->fat_quantity > 0) {
                    $displayQuantity = $item->fat_quantity;
                    $displayPrice = $item->fat_price > 0 ? $item->fat_price : ($item->detail_value / $item->fat_quantity);
                } else {
                    $displayQuantity = $item->qty_in;
                    $displayPrice = $item->qty_in > 0 ? ($item->detail_value / $item->qty_in) : 0;
                }

                // تحديد الوحدة الصحيحة
                // نحاول أولاً fat_unit_id (للتوافق القديم) ثم unit_id (الذي نحفظ فيه الآن الوحدة المختارة)
                $displayUnitId = null;
                if (isset($item->fat_unit_id) && $item->fat_unit_id) {
                    $displayUnitId = $item->fat_unit_id;
                } elseif (isset($item->unit_id) && $item->unit_id) {
                    $displayUnitId = $item->unit_id;
                }

                // إذا كان displayUnitId فارغ لكن fat_quantity موجود، نحاول نحسب الوحدة من المعامل
                if (!$displayUnitId && $item->fat_quantity > 0 && $item->qty_in > 0 && $itemData && $itemData->units) {
                    $factor = $item->qty_in / $item->fat_quantity;

                    // ابحث عن الوحدة التي معاملها يساوي هذا المعامل
                    foreach ($itemData->units as $unit) {
                        $unitFactor = $unit->pivot->u_val ?? 1;
                        if (abs($unitFactor - $factor) < 0.1) {
                            $displayUnitId = $unit->id;
                            break;
                        }
                    }
                }

                // حساب base unit cost (السعر بالوحدة الأساسية) من البيانات المحفوظة
                $baseUnitCost = 0;
                if ($item->qty_in > 0) {
                    $baseUnitCost = $item->detail_value / $item->qty_in;
                }

                // إعادة حساب displayPrice بناءً على الوحدة المختارة
                // إذا كانت الوحدة المختارة ليست الوحدة الأساسية، نحسب السعر بناءً على معامل الوحدة
                if ($displayUnitId && $itemData && $itemData->units) {
                    $selectedUnit = $itemData->units->firstWhere('id', $displayUnitId);
                    if ($selectedUnit) {
                        $unitFactor = $selectedUnit->pivot->u_val ?? 1;
                        $displayPrice = $baseUnitCost * $unitFactor;
                    }
                }

                // Ensure display unit is in the units list
                $hasDisplayUnit = collect($units)->contains('id', $displayUnitId);
                if ($displayUnitId && !$hasDisplayUnit) {
                    $recordedUnit = \App\Models\Unit::find($displayUnitId);
                    if ($recordedUnit) {
                        $units[] = [
                            'id' => $recordedUnit->id,
                            'name' => $recordedUnit->name,
                            'u_val' => $item->unit_value ?? 1,
                        ];
                    }
                }

                $products[] = [
                    'id' => $item->item_id,
                    'name' => $itemData->name ?? '',
                    'quantity' => $displayQuantity,
                    'unit_id' => $displayUnitId,
                    'unit_cost' => $displayPrice, // السعر بالوحدة المحفوظة
                    'average_cost' => $baseUnitCost, // السعر بالوحدة الأساسية (من الفاتورة)
                    'cost_percentage' => $item->additional ?? 0,
                    'total_cost' => $item->detail_value,
                    'units' => $units,
                    'unitsList' => $units,
                ];
            } elseif ($item->qty_out > 0) {
                // Raw material (input)
                $itemData = $item->item;
                $units = $itemData && $itemData->units ? $itemData->units->map(function($unit) {
                    return [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'u_val' => $unit->pivot->u_val ?? 1,
                    ];
                })->toArray() : [];

                // استخدام fat_quantity و fat_price إذا كانت موجودة
                // إذا كان fat_quantity موجود وأكبر من 0، استخدمه
                // وإلا استخدم qty_out (الكمية بالوحدة الأساسية)
                if ($item->fat_quantity > 0) {
                    $displayQuantity = $item->fat_quantity;
                    $displayPrice = $item->fat_price > 0 ? $item->fat_price : ($item->detail_value / $item->fat_quantity);
                } else {
                    $displayQuantity = $item->qty_out;
                    $displayPrice = $item->qty_out > 0 ? ($item->detail_value / $item->qty_out) : 0;
                }

                // تحديد الوحدة الصحيحة
                // نحاول أولاً fat_unit_id (للتوافق القديم) ثم unit_id (الذي نحفظ فيه الآن الوحدة المختارة)
                $displayUnitId = null;
                if (isset($item->fat_unit_id) && $item->fat_unit_id) {
                    $displayUnitId = $item->fat_unit_id;
                } elseif (isset($item->unit_id) && $item->unit_id) {
                    $displayUnitId = $item->unit_id;
                }

                // إذا كان displayUnitId فارغ لكن fat_quantity موجود، نحاول نحسب الوحدة من المعامل
                if (!$displayUnitId && $item->fat_quantity > 0 && $item->qty_out > 0 && $itemData && $itemData->units) {
                    $factor = $item->qty_out / $item->fat_quantity;

                    // ابحث عن الوحدة التي معاملها يساوي هذا المعامل
                    foreach ($itemData->units as $unit) {
                        $unitFactor = $unit->pivot->u_val ?? 1;
                        // استخدم tolerance أكبر للتعامل مع الأخطاء العددية
                        if (abs($unitFactor - $factor) < 0.1) {
                            $displayUnitId = $unit->id;
                            break;
                        }
                    }
                }

                // حساب base unit cost (السعر بالوحدة الأساسية) من البيانات المحفوظة
                $baseUnitCost = 0;
                if ($item->qty_out > 0) {
                    $baseUnitCost = $item->detail_value / $item->qty_out;
                }

                // إعادة حساب displayPrice بناءً على الوحدة المختارة
                // إذا كانت الوحدة المختارة ليست الوحدة الأساسية، نحسب السعر بناءً على معامل الوحدة
                if ($displayUnitId && $itemData && $itemData->units) {
                    $selectedUnit = $itemData->units->firstWhere('id', $displayUnitId);
                    if ($selectedUnit) {
                        $unitFactor = $selectedUnit->pivot->u_val ?? 1;
                        $displayPrice = $baseUnitCost * $unitFactor;
                    }
                }


                // Ensure display unit is in the units list
                $hasDisplayUnit = collect($units)->contains('id', $displayUnitId);
                if ($displayUnitId && !$hasDisplayUnit) {
                    $recordedUnit = \App\Models\Unit::find($displayUnitId);
                    if ($recordedUnit) {
                        $units[] = [
                            'id' => $recordedUnit->id,
                            'name' => $recordedUnit->name,
                            'u_val' => $item->unit_value ?? 1,
                        ];
                    }
                }

                $rawMaterials[] = [
                    'id' => $item->item_id,
                    'name' => $itemData->name ?? '',
                    'quantity' => $displayQuantity,
                    'unit_id' => $displayUnitId,
                    'unit_cost' => $displayPrice, // السعر بالوحدة المحفوظة
                    'total_cost' => $item->detail_value,
                    'available_stock' => 0, // Will be fetched
                    'average_cost' => $baseUnitCost, // السعر بالوحدة الأساسية (من الفاتورة)
                    'units' => $units,
                    'unitsList' => $units,
                ];
            }
        }

        // Load actual expenses from the database
        $dbExpenses = \App\Models\Expense::where('op_id', $invoiceId)->get();
        
        $expenses = $dbExpenses->map(function ($expense) {
            // Extract the true description by stripping the auto-appended prefix and suffix
            $cleanDescription = str_replace('مصروف إضافي: ', '', $expense->description ?? '');
            $cleanDescription = preg_replace('/ - فاتورة:.*$/', '', $cleanDescription);
            
            return [
                'amount' => $expense->amount,
                'account_id' => $expense->account_id,
                'description' => trim($cleanDescription)
            ];
        })->toArray();

        return [
            'invoiceId' => $invoiceId,
            'invoice' => $invoice,
            'products' => $products,
            'rawMaterials' => $rawMaterials,
            'expenses' => $expenses,
            'branches' => $branches,
            'stores' => $stores,
            'operatingCenters' => $operatingCenters,
            'employees' => $employees,
            'expenseAccounts' => $expenseAccounts,
        ];
    }
}
