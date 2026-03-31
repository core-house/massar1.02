<?php

namespace Modules\Manufacturing\Services;

use App\Models\Expense;
use App\Models\Item;
use App\Models\JournalDetail;
use App\Models\JournalHead;
use App\Models\OperationItems;
use App\Models\OperHead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\Services\RecalculationServiceHelper;

class ManufacturingInvoiceService
{
    public function saveManufacturingInvoice($component, $isTemplate = false)
    {
        // dd($component->all());
        // 1. إعداد البيانات للتحقق من صحتها
        // $data = [
        //     'pro_id' => $component->pro_id,
        //     'rawAccount' => $component->rawAccount,
        //     'productAccount' => $component->productAccount,
        //     'employee' => $component->employee,
        //     'invoiceDate' => $component->invoiceDate,
        //     'OperatingAccount' => $component->OperatingAccount,
        //     'totalManufacturingCost' => $component->totalManufacturingCost,
        //     'totalRawMaterialsCost' => $component->totalRawMaterialsCost,
        //     'totalAdditionalExpenses' => $component->totalAdditionalExpenses,
        //     'selectedRawMaterials' => $component->selectedRawMaterials,
        //     'selectedProducts' => $component->selectedProducts,
        //     'additionalExpenses' => $component->additionalExpenses ?? [],
        //     'description' => $component->description,
        // ];

        // 2. قواعد التحقق من صحة البيانات
        // $rules = [
        //     'pro_id' => 'required|numeric|min:1',
        //     'rawAccount' => 'required|numeric|exists:acc_head,id',
        //     'productAccount' => 'required|numeric|exists:acc_head,id',
        //     'employee' => 'required|numeric|exists:acc_head,id',
        //     'invoiceDate' => 'required|date|before_or_equal:today',
        //     'OperatingAccount' => 'required|numeric|exists:acc_head,id',
        //     'totalManufacturingCost' => 'required|numeric|min:0.01',
        //     'totalRawMaterialsCost' => 'required|numeric|min:0.01',
        //     'totalAdditionalExpenses' => 'nullable|numeric|min:0',
        //     'description' => 'nullable|string|max:500',

        //     // التحقق من المواد الخام
        //     'selectedRawMaterials' => 'required|array|min:1',
        //     'selectedRawMaterials.*.item_id' => 'required|numeric|exists:items,id',
        //     'selectedRawMaterials.*.quantity' => 'required|numeric|min:0.01|max:999999',
        //     'selectedRawMaterials.*.unit_cost' => 'required|numeric|min:0|max:999999',
        //     'selectedRawMaterials.*.total_cost' => 'required|numeric|min:0.01|max:999999999',

        //     // التحقق من المنتجات
        //     'selectedProducts' => 'required|array|min:1',
        //     'selectedProducts.*.product_id' => 'required|numeric|exists:items,id',
        //     'selectedProducts.*.quantity' => 'required|numeric|min:0.01|max:999999',
        //     'selectedProducts.*.unit_cost' => 'required|numeric|min:0|max:999999',
        //     'selectedProducts.*.total_cost' => 'required|numeric|min:0|max:999999999',
        //     'selectedProducts.*.cost_percentage' => 'required|numeric|min:0|max:100',

        //     // التحقق من المصاريف الإضافية
        //     'additionalExpenses' => 'nullable|array',
        //     'additionalExpenses.*.description' => 'required_with:additionalExpenses|string|min:3|max:255',
        //     'additionalExpenses.*.amount' => 'required_with:additionalExpenses|numeric|min:0.01|max:999999',
        //     'additionalExpenses.*.account_id' => 'required_with:additionalExpenses|numeric|exists:acc_head,id',
        // ];

        // // 3. رسائل الخطأ المخصصة
        // $messages = [
        //     'required' => 'هذا الحقل مطلوب',
        //     'numeric' => 'يجب إدخال قيمة رقمية صحيحة',
        //     'min' => 'القيمة أقل من الحد المسموح',
        //     'max' => 'القيمة أكبر من الحد المسموح',
        //     'exists' => 'القيمة المختارة غير صحيحة',
        //     'date' => 'يجب إدخال تاريخ صحيح',
        //     'before_or_equal' => 'التاريخ لا يمكن أن يكون في المستقبل',
        //     'array' => 'البيانات يجب أن تكون في صورة قائمة',
        //     'string' => 'يجب إدخال نص',

        //     // رسائل مخصصة للحقول المحددة
        //     'pro_id.required' => 'رقم الفاتورة مطلوب',
        //     'rawAccount.required' => 'حساب المواد الخام مطلوب',
        //     'productAccount.required' => 'حساب المنتجات مطلوب',
        //     'employee.required' => 'الموظف مطلوب',
        //     'invoiceDate.required' => 'تاريخ الفاتورة مطلوب',
        //     'OperatingAccount.required' => 'حساب التشغيل مطلوب',
        //     'totalManufacturingCost.min' => 'إجمالي تكلفة التصنيع يجب أن يكون أكبر من صفر',
        //     'totalRawMaterialsCost.min' => 'إجمالي تكلفة المواد الخام يجب أن يكون أكبر من صفر',

        //     'selectedRawMaterials.required' => 'يجب اختيار مواد خام على الأقل',
        //     'selectedRawMaterials.min' => 'يجب اختيار مادة خام واحدة على الأقل',
        //     'selectedRawMaterials.*.item_id.required' => 'يجب اختيار المادة الخام',
        //     'selectedRawMaterials.*.item_id.exists' => 'المادة الخام المختارة غير موجودة',
        //     'selectedRawMaterials.*.quantity.required' => 'كمية المادة الخام مطلوبة',
        //     'selectedRawMaterials.*.quantity.min' => 'كمية المادة الخام يجب أن تكون أكبر من صفر',
        //     'selectedRawMaterials.*.unit_cost.required' => 'سعر الوحدة للمادة الخام مطلوب',
        //     'selectedRawMaterials.*.total_cost.required' => 'إجمالي تكلفة المادة الخام مطلوب',

        //     'selectedProducts.required' => 'يجب اختيار منتجات على الأقل',
        //     'selectedProducts.min' => 'يجب اختيار منتج واحد على الأقل',
        //     'selectedProducts.*.product_id.required' => 'يجب اختيار المنتج',
        //     'selectedProducts.*.product_id.exists' => 'المنتج المختار غير موجود',
        //     'selectedProducts.*.quantity.required' => 'كمية المنتج مطلوبة',
        //     'selectedProducts.*.quantity.min' => 'كمية المنتج يجب أن تكون أكبر من صفر',
        //     'selectedProducts.*.unit_cost.required' => 'سعر الوحدة للمنتج مطلوب',
        //     'selectedProducts.*.total_cost.required' => 'إجمالي تكلفة المنتج مطلوب',
        //     'selectedProducts.*.cost_percentage.required' => 'نسبة التكلفة مطلوبة',
        //     'selectedProducts.*.cost_percentage.max' => 'نسبة التكلفة لا يمكن أن تزيد عن 100%',

        //     'additionalExpenses.*.description.required_with' => 'وصف المصروف مطلوب',
        //     'additionalExpenses.*.description.min' => 'وصف المصروف يجب أن يكون 3 أحرف على الأقل',
        //     'additionalExpenses.*.amount.required_with' => 'مبلغ المصروف مطلوب',
        //     'additionalExpenses.*.amount.min' => 'مبلغ المصروف يجب أن يكون أكبر من صفر',
        //     'additionalExpenses.*.account_id.required_with' => 'حساب المصروف مطلوب',
        //     'additionalExpenses.*.account_id.exists' => 'حساب المصروف غير صحيح',
        // ];

        // 4. تشغيل التحقق من صحة البيانات
        // $validator = validator($data, $rules, $messages);

        // if ($validator->fails()) {
        //     $errors = $validator->errors();
        //     $firstError = $errors->first();

        //     $component->dispatch('error-swal', [
        //         'title' => 'خطأ في البيانات!',
        //         'text' => $firstError,
        //         'icon' => 'error'
        //     ]);

        //     return false;
        // }

        // 5. تحققات إضافية مخصصة

        // التحقق من أن مجموع نسب التكلفة = 100%
        // $totalPercentage = collect($component->selectedProducts)
        //     ->sum(fn($product) => (float)$product['cost_percentage']);

        // if (abs($totalPercentage - 100) > 0.01) {
        //     $component->dispatch('error-swal', [
        //         'title' => 'خطأ في النسب!',
        //         'text' => 'مجموع نسب توزيع التكلفة يجب أن يساوي 100%. المجموع الحالي: ' . number_format($totalPercentage, 2) . '%',
        //         'icon' => 'error'
        //     ]);
        //     return false;
        // }

        // // التحقق من عدم تكرار المواد الخام
        // $rawMaterialIds = collect($component->selectedRawMaterials)->pluck('item_id');
        // if ($rawMaterialIds->count() !== $rawMaterialIds->unique()->count()) {
        //     $component->dispatch('error-swal', [
        //         'title' => 'خطأ في المواد الخام!',
        //         'text' => 'لا يمكن تكرار نفس المادة الخام أكثر من مرة',
        //         'icon' => 'error'
        //     ]);
        //     return false;
        // }

        // // التحقق من عدم تكرار المنتجات
        // $productIds = collect($component->selectedProducts)->pluck('product_id');
        // if ($productIds->count() !== $productIds->unique()->count()) {
        //     $component->dispatch('error-swal', [
        //         'title' => 'خطأ في المنتجات!',
        //         'text' => 'لا يمكن تكرار نفس المنتج أكثر من مرة',
        //         'icon' => 'error'
        //     ]);
        //     return false;
        // }

        // // التحقق من أن إجمالي التكلفة صحيح
        // $calculatedTotal = $component->totalRawMaterialsCost + $component->totalAdditionalExpenses;
        // if (abs($calculatedTotal - $component->totalManufacturingCost) > 0.01) {
        //     $component->dispatch('error-swal', [
        //         'title' => 'خطأ في الحسابات!',
        //         'text' => 'إجمالي تكلفة التصنيع غير صحيح. يجب أن يساوي مجموع تكلفة المواد الخام والمصاريف الإضافية',
        //         'icon' => 'error'
        //     ]);
        //     return false;
        // }

        // ✅ التحقق من أن تكلفة المواد الخام + المصروفات = تكلفة المنتجات المصنعة
        if (! $isTemplate) {
            $totalRawMaterialsCost = $component->totalRawMaterialsCost;
            $totalAdditionalExpenses = $component->totalAdditionalExpenses;
            $totalManufacturingCost = $totalRawMaterialsCost + $totalAdditionalExpenses;

            $totalProductsCost = $component->totalProductsCost;

            // السماح بفرق بسيط (0.01) بسبب التقريب
            if (abs($totalManufacturingCost - $totalProductsCost) > 0.01) {
                $difference = abs($totalManufacturingCost - $totalProductsCost);

                if (method_exists($component, 'dispatch')) {
                    $component->dispatch('error-swal', [
                        'title' => __('items.manufacturing_cost_validation_error'),
                        'html' => sprintf(
                            '<div style="text-align: right; direction: rtl;">
                            <p style="margin-bottom: 15px; font-size: 16px;">%s</p>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: bold;">%s:</span>
                                    <span style="color: #dc3545; font-weight: bold;">%.2f ج.م</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: bold;">%s:</span>
                                    <span style="color: #28a745; font-weight: bold;">%.2f ج.م</span>
                                </div>
                                <hr style="margin: 10px 0; border-color: #dee2e6;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="font-weight: bold; color: #dc3545;">%s:</span>
                                    <span style="color: #dc3545; font-weight: bold; font-size: 18px;">%.2f ج.م</span>
                                </div>
                            </div>
                        </div>',
                            __('items.manufacturing_cost_mismatch'),
                            __('items.raw_materials_and_expenses_cost'),
                            $totalManufacturingCost,
                            __('items.manufactured_products_cost'),
                            $totalProductsCost,
                            __('items.cost_difference'),
                            $difference
                        ),
                        'icon' => 'error',
                        'confirmButtonText' => 'حسناً',
                    ]);
                }

                return false;
            }
        }

        try {
            DB::beginTransaction();
            $operation = OperHead::create([
                'pro_id' => $component->pro_id,
                'pro_type' => $isTemplate ? 63 : 59,
                // acc1 = مخزن المنتجات التامة | acc2 = مخزن المواد الخام | acc3 = حساب مركز التشغيل
                'acc1' => $component->productAccount,
                'acc2' => $component->rawAccount,
                'acc3' => 73, // مركز التشغيل الرئيسي - hardcoded
                'emp_id' => $component->employee,
                'store_id' => $component->productAccount,
                'is_stock' => $isTemplate ? 0 : 1, // 0 للنموذج، 1 للفاتورة العادية
                'is_finance' => 0,
                'is_manager' => $isTemplate ? 1 : 0,
                'expected_time' => $component->expectedTime ?? $component->actualTime ?? null,
                'details' => $component->details ?? null, // Save actual time as string in details
                'is_journal' => $isTemplate ? 0 : 1, // 0 للنموذج، 1 للفاتورة العادية
                'pro_date' => $component->invoiceDate,
                'pro_value' => $component->totalManufacturingCost,
                'fat_net' => $component->totalManufacturingCost,
                'info' => $component->description,
                'user' => Auth::id(),
                'branch_id' => $component->branch_id,
                'manufacturing_order_id' => $component->order_id,
                'manufacturing_stage_id' => $component->stage_id,
                'is_template' => $isTemplate ? 1 : 0, // إضافة هذا الحقل إذا كان موجوداً في الجدول
                'pro_serial' => $component->pro_serial ?? null,
            ]);

            // ✅ Batch loading للـ unit factors و base unit IDs لتقليل N+1 queries
            $rawMaterialItemIds = collect($component->selectedRawMaterials)
                ->pluck('item_id')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $unitFactorsMap = [];
            $baseUnitIdsMap = [];

            if (! empty($rawMaterialItemIds)) {
                // Batch load جميع unit factors
                $allUnitFactors = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->get()
                    ->groupBy('item_id');

                // Batch load جميع base unit IDs
                $baseUnits = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->where('u_val', 1)
                    ->select('item_id', 'unit_id')
                    ->get()
                    ->keyBy('item_id');

                // Fallback: أول وحدة (أصغر u_val)
                $fallbackUnits = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->orderBy('u_val', 'asc')
                    ->get()
                    ->groupBy('item_id')
                    ->map(function ($units) {
                        return $units->first();
                    });

                // بناء Maps للوصول السريع
                foreach ($allUnitFactors as $itemId => $units) {
                    foreach ($units as $unit) {
                        $unitFactorsMap[$itemId.'_'.$unit->unit_id] = $unit->u_val ?? 1;
                    }
                }

                foreach ($rawMaterialItemIds as $itemId) {
                    $baseUnitIdsMap[$itemId] = $baseUnits->get($itemId)?->unit_id
                        ?? $fallbackUnits->get($itemId)?->unit_id
                        ?? null;
                }
            }

            foreach ($component->selectedRawMaterials as $raw) {
                $displayUnitId = $raw['unit_id'] ?? null;

                // Log for debugging
                if ($isTemplate) {
                    Log::info('💾 Saving raw material to template', [
                        'item_id' => $raw['item_id'],
                        'unit_id_from_request' => $raw['unit_id'] ?? 'NULL',
                        'displayUnitId' => $displayUnitId,
                    ]);
                }

                // ✅ استخدام Map بدلاً من استعلام منفصل
                $unitFactor = $displayUnitId
                    ? ($unitFactorsMap[$raw['item_id'].'_'.$displayUnitId] ?? 1)
                    : 1;
                $baseUnitId = $baseUnitIdsMap[$raw['item_id']] ?? $displayUnitId;

                $originalQty = $raw['quantity'];
                $baseQty = $originalQty * $unitFactor;

                $originalPrice = $raw['average_cost'] ?? $raw['unit_cost'] ?? 0;
                $basePrice = $unitFactor > 0 ? $originalPrice / $unitFactor : $originalPrice;

                OperationItems::create([
                    'pro_tybe' => $isTemplate ? 63 : 59, // 63 for template raw materials, 59 for regular
                    'detail_store' => $component->rawAccount,
                    'pro_id' => $operation->id,
                    'item_id' => $raw['item_id'],
                    'unit_id' => $displayUnitId ?? $baseUnitId, // Save display unit directly in unit_id
                    'unit_value' => $unitFactor, // Save unit factor for reference
                    'qty_in' => 0,
                    'qty_out' => $isTemplate ? 0 : $baseQty, // Base quantity for stock (only for regular invoices)
                    'fat_quantity' => $originalQty, // Display quantity (always saved)
                    'fat_price' => $originalPrice, // Display price (always saved)
                    'item_price' => $basePrice,
                    'cost_price' => $basePrice,
                    'detail_value' => $raw['total_cost'],
                    'is_stock' => $isTemplate ? 0 : 1,
                    'branch_id' => $component->branch_id,
                ]);
            }

            // ✅ Batch loading للـ products items و units
            $productItemIds = collect($component->selectedProducts)
                ->pluck('product_id')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $productItemsMap = [];
            $productUnitFactorsMap = [];
            $productBaseUnitIdsMap = [];

            if (! empty($productItemIds)) {
                // Batch load جميع products
                $productItemsMap = Item::whereIn('id', $productItemIds)
                    ->with(['units' => fn ($q) => $q->orderBy('pivot_u_val', 'asc')])
                    ->get()
                    ->keyBy('id');

                // Batch load unit factors للـ products
                $allProductUnitFactors = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->get()
                    ->groupBy('item_id');

                // Batch load base unit IDs
                $productBaseUnits = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->where('u_val', 1)
                    ->select('item_id', 'unit_id')
                    ->get()
                    ->keyBy('item_id');

                $productFallbackUnits = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->orderBy('u_val', 'asc')
                    ->get()
                    ->groupBy('item_id')
                    ->map(function ($units) {
                        return $units->first();
                    });

                foreach ($allProductUnitFactors as $itemId => $units) {
                    foreach ($units as $unit) {
                        $productUnitFactorsMap[$itemId.'_'.$unit->unit_id] = $unit->u_val ?? 1;
                    }
                }

                foreach ($productItemIds as $itemId) {
                    $productBaseUnitIdsMap[$itemId] = $productBaseUnits->get($itemId)?->unit_id
                        ?? $productFallbackUnits->get($itemId)?->unit_id
                        ?? null;
                }
            }

            // ✅ Batch load old quantities لجميع المنتجات في استعلام واحد
            $oldQuantitiesMap = [];
            if (! empty($productItemIds) && ! $isTemplate) {
                $oldQuantities = OperationItems::whereIn('item_id', $productItemIds)
                    ->where('is_stock', 1)
                    ->selectRaw('item_id, SUM(qty_in - qty_out) as total')
                    ->groupBy('item_id')
                    ->pluck('total', 'item_id')
                    ->toArray();

                $oldQuantitiesMap = $oldQuantities;
            }

            foreach ($component->selectedProducts as $product) {
                $item = $productItemsMap[$product['product_id']] ?? null;

                $displayUnitId = $product['unit_id'] ?? null;

                // Log for debugging
                if ($isTemplate) {
                    Log::info('💾 Saving product to template', [
                        'product_id' => $product['product_id'],
                        'unit_id_from_request' => $product['unit_id'] ?? 'NULL',
                        'displayUnitId' => $displayUnitId,
                    ]);
                }

                // ✅ استخدام Map بدلاً من استعلام منفصل
                if (! $displayUnitId && $item && $item->units->isNotEmpty()) {
                    $defaultUnit = $item->units->first();
                    $displayUnitId = $defaultUnit->id;
                    $unitFactor = $defaultUnit->pivot->u_val ?? 1;
                } else {
                    $unitFactor = $displayUnitId
                        ? ($productUnitFactorsMap[$product['product_id'].'_'.$displayUnitId] ?? 1)
                        : 1;
                }

                $baseUnitId = $productBaseUnitIdsMap[$product['product_id']] ?? $displayUnitId;

                $originalQty = $product['quantity'];
                $baseQty = $originalQty * $unitFactor;

                $originalPrice = $product['unit_cost'];
                $basePrice = $unitFactor > 0 ? $originalPrice / $unitFactor : $originalPrice;

                OperationItems::create([
                    'pro_tybe' => $isTemplate ? 64 : 59, // 64 for template products, 59 for regular
                    'detail_store' => $component->productAccount,
                    'pro_id' => $operation->id,
                    'item_id' => $product['product_id'],
                    'unit_id' => $displayUnitId ?? $baseUnitId, // Save display unit directly in unit_id
                    'unit_value' => $unitFactor, // Save unit factor for reference
                    'qty_in' => $isTemplate ? 0 : $baseQty, // Base quantity for stock (only for regular invoices)
                    'qty_out' => 0,
                    'fat_quantity' => $originalQty, // Display quantity (always saved)
                    'fat_price' => $originalPrice, // Display price (always saved)
                    'item_price' => $basePrice,
                    'cost_price' => $basePrice,
                    'detail_value' => $product['total_cost'],
                    'additional' => $product['cost_percentage'] ?? 0,
                    'is_stock' => $isTemplate ? 0 : 1,
                    'branch_id' => $component->branch_id,
                ]);

                // ✅ Update average cost using batch loaded data
                if (! $isTemplate && $item) {
                    $oldQtyInBase = $oldQuantitiesMap[$product['product_id']] ?? 0;
                    $oldAverage = $item->average_cost ?? 0;

                    // Calculate total cost and quantity in base units
                    $totalCost = ($oldQtyInBase * $oldAverage) + ($baseQty * $basePrice);
                    $totalQuantity = $oldQtyInBase + $baseQty;

                    $newAverage = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

                    // تحديث سعر الشراء المتوسط
                    $item->update([
                        'average_cost' => $newAverage,
                    ]);
                }
            }

            // Save additional expenses
            foreach ($component->additionalExpenses as $expense) {
                Expense::create([
                    'title' => $component->description ?: 'فاتورة تصنيع',
                    'pro_type' => $operation->pro_type,
                    'op_id' => $operation->id,
                    'amount' => $expense['amount'],
                    'account_id' => $expense['account_id'] ?? $component->expenseAccount ?? null,
                    'description' => 'مصروف إضافي: '.($expense['description'] ?? 'غير محدد').' - فاتورة: '.$component->pro_id,
                ]);
            }

            if (! $isTemplate) {
                $journalId = (JournalHead::max('journal_id') ?? 0) + 1;
                $totalRaw = $component->totalRawMaterialsCost;
                $totalExpenses = $component->totalAdditionalExpenses;
                $operatingAccountId = 73; // مركز التشغيل الرئيسي - hardcoded

                JournalHead::create([
                    'journal_id' => $journalId,
                    'total' => $totalRaw,
                    'date' => $component->invoiceDate,
                    'op_id' => $operation->id,
                    'pro_type' => 59,
                    'details' => 'صرف مواد خام للتصنيع',
                    'user' => Auth::id(),
                    'branch_id' => $component->branch_id,
                ]);
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $operatingAccountId,
                    'debit' => $totalRaw,
                    'credit' => 0,
                    'type' => 1,
                    'info' => 'صرف مواد خام للتصنيع',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $component->rawAccount,
                    'debit' => 0,
                    'credit' => $totalRaw,
                    'type' => 1,
                    'info' => 'صرف مواد خام للتصنيع',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);

                if ($totalExpenses > 0 && isset($component->additionalExpenses)) {
                    $journalId++;

                    JournalHead::create([
                        'journal_id' => $journalId,
                        'total' => $totalExpenses,
                        'date' => $component->invoiceDate,
                        'op_id' => $operation->id,
                        'pro_type' => 59,
                        'details' => 'مصاريف إضافية للتصنيع',
                        'user' => Auth::id(),
                        'branch_id' => $component->branch_id,

                    ]);
                    JournalDetail::create([
                        'journal_id' => $journalId,
                        'account_id' => $operatingAccountId,
                        'debit' => $totalExpenses,
                        'credit' => 0,
                        'type' => 1,
                        'info' => 'مصاريف إضافية للتصنيع',
                        'op_id' => $operation->id,
                        'branch_id' => $component->branch_id,
                    ]);

                    foreach ($component->additionalExpenses as $expense) {
                        JournalDetail::create([
                            'journal_id' => $journalId,
                            'account_id' => $expense['account_id'] ?? $component->expenseAccount ?? null,
                            'debit' => 0,
                            'credit' => $expense['amount'],
                            'type' => 1,
                            'info' => 'مصاريف إضافية للتصنيع',
                            'op_id' => $operation->id,
                            'branch_id' => $component->branch_id,
                        ]);
                    }

                    // قيد المصروفات (كود 5)
                    // $journalId++;
                    // JournalHead::create([
                    //     'journal_id' => $journalId,
                    //     'total' => $totalExpenses,
                    //     'date' => $component->invoiceDate,
                    //     'op_id' => $operation->id,
                    //     'pro_type' => 59,
                    //     'details' => 'مصروفات تصنيع',
                    //     'user' => Auth::id(),
                    //     'branch_id' => $component->branch_id,
                    // ]);

                    // foreach ($component->additionalExpenses as $expense) {
                    //     JournalDetail::create([
                    //         'journal_id' => $journalId,
                    //         'account_id' => $expense['account_id'],
                    //         'debit' => $expense['amount'],
                    //         'credit' => 0,
                    //         'type' => 1,
                    //         'info' => 'مصروفات تصنيع',
                    //         'op_id' => $operation->id,
                    //         'branch_id' => $component->branch_id,
                    //     ]);
                    // }
                    // JournalDetail::create([
                    //     'journal_id' => $journalId,
                    //     'account_id' => $component->OperatingAccount,
                    //     'debit' => 0,
                    //     'credit' => $totalExpenses,
                    //     'type' => 1,
                    //     'info' => 'مصروفات تصنيع',
                    //     'op_id' => $operation->id,
                    //     'branch_id' => $component->branch_id,
                    // ]);
                }

                $journalId++;
                JournalHead::create([
                    'journal_id' => $journalId,
                    'total' => $totalRaw + $totalExpenses,
                    'date' => $component->invoiceDate,
                    'op_id' => $operation->id,
                    'pro_type' => 59,
                    'details' => 'إنتاج منتجات تامة',
                    'user' => Auth::id(),
                    'branch_id' => $component->branch_id,
                ]);
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $component->productAccount,
                    'debit' => $totalRaw + $totalExpenses,
                    'credit' => 0,
                    'type' => 1,
                    'info' => 'إنتاج منتجات تامة',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $operatingAccountId,
                    'debit' => 0,
                    'credit' => $totalRaw + $totalExpenses,
                    'type' => 1,
                    'info' => 'إنتاج منتجات تامة',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);
            }
            DB::commit();

            // إعادة حساب average_cost والأرباح والقيود بعد إضافة فاتورة التصنيع
            // مهم: فاتورة التصنيع تحتوي على:
            // 1. خامات (qty_out > 0) - تتأثر بمتوسط التكلفة من فواتير المشتريات
            // 2. منتجات (qty_in > 0) - تتأثر بتكلفة الخامات في نفس فاتورة التصنيع
            if (! $isTemplate) {
                try {
                    // جمع جميع الأصناف المتأثرة (خامات + منتجات) من رأس الفاتورة
                    $allItemIds = $operation->operationItems()
                        ->pluck('item_id')
                        ->unique()
                        ->values()
                        ->toArray();

                    // ✅ إعادة حساب average_cost للمنتجات التامة فقط (qty_in > 0)
                    // المواد الخام (qty_out > 0) لا تحتاج إعادة حساب هنا لأن:
                    // - تكلفتها مصدرها فواتير المشتريات (قبل تاريخ التصنيع)
                    // - استدعاء recalculateAverageCost بتاريخ التصنيع سيصفّر تكلفتها
                    $productItemIds = $operation->operationItems()
                        ->where('qty_in', '>', 0)
                        ->pluck('item_id')
                        ->unique()
                        ->values()
                        ->toArray();

                    if (! empty($productItemIds)) {
                        RecalculationServiceHelper::recalculateAverageCost($productItemIds, $component->invoiceDate);
                    }

                    // إعادة حساب الأرباح والقيود لجميع الأصناف المتأثرة (خامات + منتجات)
                    // فقط للفواتير التي بعد تاريخ فاتورة التصنيع
                    RecalculationServiceHelper::recalculateProfitsAndJournals(
                        $allItemIds,
                        $component->invoiceDate,
                        $operation->id,
                        $operation->created_at?->format('Y-m-d H:i:s')
                    );
                } catch (\Exception $e) {
                    Log::error('Error recalculating after manufacturing invoice creation: '.$e->getMessage());
                    Log::error('Stack trace: '.$e->getTraceAsString());
                    // لا نوقف العملية، فقط نسجل الخطأ
                }
            }

            // Dispatch success event only if component has dispatch method (Livewire)
            if (method_exists($component, 'dispatch')) {
                $component->dispatch('success-swal', [
                    'title' => 'تم الحفظ!',
                    'text' => 'تم حفظ فاتورة التصنيع بنجاح.',
                    'icon' => 'success',
                    'reload' => true,
                ]);
            }

            return $operation->id;
        } catch (\Exception $e) {
            DB::rollBack();

            // Set isSaving only if component has this property (Livewire)
            if (property_exists($component, 'isSaving')) {
                $component->isSaving = false;
            }

            // Dispatch error event only if component has dispatch method (Livewire)
            if (method_exists($component, 'dispatch')) {
                $component->dispatch('error-swal', [
                    'title' => 'خطأ !',
                    'text' => 'حدث خطا اثناء الحفظ: '.$e->getMessage(),
                    'icon' => 'error',
                ]);
            }

            // Log error for debugging
            Log::error('Manufacturing invoice save error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            // Re-throw exception so controller can handle it
            throw $e;
        }
    }

    public function updateManufacturingInvoice($component, $invoiceId)
    {
        try {
            DB::beginTransaction();

            // 1. العثور على الفاتورة القديمة
            $operation = OperHead::with('operationItems')->find($invoiceId);
            if (! $operation) {
                $component->dispatch('error-swal', [
                    'title' => 'خطأ!',
                    'text' => 'الفاتورة غير موجودة.',
                    'icon' => 'error',
                ]);

                return false;
            }

            // حفظ معلومات الفاتورة القديمة قبل الحذف
            $oldOperationDate = $operation->pro_date;
            $oldOperationCreatedAt = $operation->created_at?->format('Y-m-d H:i:s');
            $oldItemIds = $operation->operationItems()
                ->where('is_stock', 1)
                ->pluck('item_id')
                ->unique()
                ->toArray();

            // 2. حذف البيانات القديمة
            OperationItems::where('pro_id', $operation->id)->delete();
            Expense::where('op_id', $operation->id)->delete();
            JournalDetail::where('op_id', $operation->id)->delete();
            JournalHead::where('op_id', $operation->id)->delete();

            // 3. تحديث بيانات الفاتورة الرئيسية
            $operation->update([
                'pro_id' => $component->pro_id,
                'pro_type' => 59,
                // acc1 = مخزن المنتجات التامة | acc2 = مخزن المواد الخام | acc3 = حساب مركز التشغيل
                'acc1' => $component->productAccount,
                'acc2' => $component->rawAccount,
                'acc3' => 73, // مركز التشغيل الرئيسي - hardcoded
                'emp_id' => $component->employee,
                'store_id' => $component->productAccount,
                'is_stock' => 1,
                'is_finance' => 0,
                'is_manager' => 0,
                'is_journal' => 1,
                'pro_date' => $component->invoiceDate,
                'pro_value' => $component->totalManufacturingCost,
                'fat_net' => $component->totalManufacturingCost,
                'info' => $component->description,
                'user' => Auth::id(),
                'branch_id' => $component->branch_id,
                'manufacturing_order_id' => $component->order_id,
                'manufacturing_stage_id' => $component->stage_id,
                'pro_serial' => $component->pro_serial ?? null,
            ]);

            // ✅ Batch loading للـ unit factors و base unit IDs لتقليل N+1 queries
            $rawMaterialItemIds = collect($component->selectedRawMaterials)
                ->pluck('item_id')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $unitFactorsMap = [];
            $baseUnitIdsMap = [];

            if (! empty($rawMaterialItemIds)) {
                $allUnitFactors = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->get()
                    ->groupBy('item_id');

                $baseUnits = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->where('u_val', 1)
                    ->select('item_id', 'unit_id')
                    ->get()
                    ->keyBy('item_id');

                $fallbackUnits = DB::table('item_units')
                    ->whereIn('item_id', $rawMaterialItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->orderBy('u_val', 'asc')
                    ->get()
                    ->groupBy('item_id')
                    ->map(function ($units) {
                        return $units->first();
                    });

                foreach ($allUnitFactors as $itemId => $units) {
                    foreach ($units as $unit) {
                        $unitFactorsMap[$itemId.'_'.$unit->unit_id] = $unit->u_val ?? 1;
                    }
                }

                foreach ($rawMaterialItemIds as $itemId) {
                    $baseUnitIdsMap[$itemId] = $baseUnits->get($itemId)?->unit_id
                        ?? $fallbackUnits->get($itemId)?->unit_id
                        ?? null;
                }
            }

            // 4. إنشاء بيانات المواد الخام الجديدة
            foreach ($component->selectedRawMaterials as $raw) {
                $displayUnitId = $raw['unit_id'] ?? null;
                // ✅ استخدام Map بدلاً من استعلام منفصل
                $unitFactor = $displayUnitId
                    ? ($unitFactorsMap[$raw['item_id'].'_'.$displayUnitId] ?? 1)
                    : 1;
                $baseUnitId = $baseUnitIdsMap[$raw['item_id']] ?? $displayUnitId;

                $originalQty = $raw['quantity'];
                $baseQty = $originalQty * $unitFactor;

                $originalPrice = $raw['average_cost'] ?? $raw['unit_cost'] ?? 0;
                $basePrice = $unitFactor > 0 ? $originalPrice / $unitFactor : $originalPrice;

                OperationItems::create([
                    'pro_tybe' => 59,
                    'detail_store' => $component->rawAccount,
                    'pro_id' => $operation->id,
                    'item_id' => $raw['item_id'],
                    'unit_id' => $displayUnitId ?: $baseUnitId,
                    'unit_value' => $unitFactor,
                    'qty_in' => 0,
                    'qty_out' => $baseQty,
                    'fat_quantity' => $originalQty,
                    'fat_price' => $originalPrice,
                    'item_price' => $basePrice,
                    'cost_price' => $basePrice,
                    'detail_value' => $raw['total_cost'],
                    'is_stock' => 1,
                    'branch_id' => $component->branch_id,
                ]);
            }

            // ✅ Batch loading للـ products
            $productItemIds = collect($component->selectedProducts)
                ->pluck('product_id')
                ->unique()
                ->filter()
                ->values()
                ->toArray();

            $productItemsMap = [];
            $productUnitFactorsMap = [];
            $productBaseUnitIdsMap = [];

            if (! empty($productItemIds)) {
                $productItemsMap = Item::whereIn('id', $productItemIds)
                    ->with(['units' => fn ($q) => $q->orderBy('pivot_u_val', 'asc')])
                    ->get()
                    ->keyBy('id');

                $allProductUnitFactors = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->get()
                    ->groupBy('item_id');

                $productBaseUnits = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->where('u_val', 1)
                    ->select('item_id', 'unit_id')
                    ->get()
                    ->keyBy('item_id');

                $productFallbackUnits = DB::table('item_units')
                    ->whereIn('item_id', $productItemIds)
                    ->select('item_id', 'unit_id', 'u_val')
                    ->orderBy('u_val', 'asc')
                    ->get()
                    ->groupBy('item_id')
                    ->map(function ($units) {
                        return $units->first();
                    });

                foreach ($allProductUnitFactors as $itemId => $units) {
                    foreach ($units as $unit) {
                        $productUnitFactorsMap[$itemId.'_'.$unit->unit_id] = $unit->u_val ?? 1;
                    }
                }

                foreach ($productItemIds as $itemId) {
                    $productBaseUnitIdsMap[$itemId] = $productBaseUnits->get($itemId)?->unit_id
                        ?? $productFallbackUnits->get($itemId)?->unit_id
                        ?? null;
                }
            }

            // ✅ Batch load old quantities
            $oldQuantitiesMap = [];
            if (! empty($productItemIds)) {
                $oldQuantities = OperationItems::whereIn('item_id', $productItemIds)
                    ->where('is_stock', 1)
                    ->selectRaw('item_id, SUM(qty_in - qty_out) as total')
                    ->groupBy('item_id')
                    ->pluck('total', 'item_id')
                    ->toArray();

                $oldQuantitiesMap = $oldQuantities;
            }

            // 5. إنشاء بيانات المنتجات الجديدة
            foreach ($component->selectedProducts as $product) {
                $item = $productItemsMap[$product['product_id']] ?? null;

                $displayUnitId = $product['unit_id'] ?? null;

                // ✅ استخدام Map بدلاً من استعلام منفصل
                if (! $displayUnitId && $item && $item->units->isNotEmpty()) {
                    $defaultUnit = $item->units->first();
                    $displayUnitId = $defaultUnit->id;
                    $unitFactor = $defaultUnit->pivot->u_val ?? 1;
                } else {
                    $unitFactor = $displayUnitId
                        ? ($productUnitFactorsMap[$product['product_id'].'_'.$displayUnitId] ?? 1)
                        : 1;
                }

                $baseUnitId = $productBaseUnitIdsMap[$product['product_id']] ?? $displayUnitId;

                $originalQty = $product['quantity'];
                $baseQty = $originalQty * $unitFactor;

                $originalPrice = $product['unit_cost'];
                $basePrice = $unitFactor > 0 ? $originalPrice / $unitFactor : $originalPrice;

                if ($item) {
                    $oldQtyInBase = $oldQuantitiesMap[$product['product_id']] ?? 0;
                    $oldAverage = $item->average_cost ?? 0;
                    $totalCost = ($oldQtyInBase * $oldAverage) + ($baseQty * $basePrice);
                    $totalQuantity = $oldQtyInBase + $baseQty;

                    $newAverage = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;

                    $item->update([
                        'average_cost' => $newAverage,
                    ]);
                }

                OperationItems::create([
                    'pro_tybe' => 59,
                    'detail_store' => $component->productAccount,
                    'pro_id' => $operation->id,
                    'item_id' => $product['product_id'],
                    'unit_id' => $displayUnitId ?: $baseUnitId,
                    'unit_value' => $unitFactor,
                    'qty_in' => $baseQty,
                    'qty_out' => 0,
                    'fat_quantity' => $originalQty,
                    'fat_price' => $originalPrice,
                    'item_price' => $basePrice,
                    'cost_price' => $basePrice,
                    'detail_value' => $product['total_cost'],
                    'additional' => $product['cost_percentage'] ?? 0,
                    'is_stock' => 1,
                    'branch_id' => $component->branch_id,
                ]);
            }

            // 5.5 إنشاء المصروفات الإضافية
            foreach ($component->additionalExpenses as $expense) {
                Expense::create([
                    'title' => $component->description ?: 'فاتورة تصنيع',
                    'pro_type' => $operation->pro_type,
                    'op_id' => $operation->id,
                    'amount' => $expense['amount'],
                    'account_id' => $expense['account_id'] ?? $component->expenseAccount ?? null,
                    'description' => 'مصروف إضافي: '.($expense['description'] ?? 'غير محدد').' - فاتورة: '.$component->pro_id,
                ]);
            }

            // 6. إنشاء قيود المحاسبة
            $journalId = (JournalHead::max('journal_id') ?? 0) + 1;
            $totalRaw = $component->totalRawMaterialsCost;
            $totalExpenses = $component->totalAdditionalExpenses;
            $operatingAccountId = 73; // مركز التشغيل الرئيسي - hardcoded

            // قيد صرف المواد الخام
            JournalHead::create([
                'journal_id' => $journalId,
                'total' => $totalRaw,
                'date' => $component->invoiceDate,
                'op_id' => $operation->id,
                'pro_type' => 59,
                'details' => 'صرف مواد خام للتصنيع',
                'user' => Auth::id(),
                'branch_id' => $component->branch_id,
            ]);
            JournalDetail::create([
                'journal_id' => $journalId,
                'account_id' => $operatingAccountId,
                'debit' => $totalRaw,
                'credit' => 0,
                'type' => 1,
                'info' => 'صرف مواد خام للتصنيع',
                'op_id' => $operation->id,
                'branch_id' => $component->branch_id,
            ]);
            JournalDetail::create([
                'journal_id' => $journalId,
                'account_id' => $component->rawAccount,
                'debit' => 0,
                'credit' => $totalRaw,
                'type' => 1,
                'info' => 'صرف مواد خام للتصنيع',
                'op_id' => $operation->id,
                'branch_id' => $component->branch_id,
            ]);

            // قيد المصروفات الإضافية
            if ($totalExpenses > 0 && isset($component->additionalExpenses)) {
                $journalId++;

                JournalHead::create([
                    'journal_id' => $journalId,
                    'total' => $totalExpenses,
                    'date' => $component->invoiceDate,
                    'op_id' => $operation->id,
                    'pro_type' => 59,
                    'details' => 'مصاريف إضافية للتصنيع',
                    'user' => Auth::id(),
                    'branch_id' => $component->branch_id,
                ]);
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $operatingAccountId,
                    'debit' => $totalExpenses,
                    'credit' => 0,
                    'type' => 1,
                    'info' => 'مصاريف إضافية للتصنيع',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);

                foreach ($component->additionalExpenses as $expense) {
                    JournalDetail::create([
                        'journal_id' => $journalId,
                        'account_id' => $expense['account_id'] ?? $component->expenseAccount ?? null,
                        'debit' => 0,
                        'credit' => $expense['amount'],
                        'type' => 1,
                        'info' => 'مصاريف إضافية للتصنيع',
                        'op_id' => $operation->id,
                        'branch_id' => $component->branch_id,
                    ]);
                }

                // قيد المصروفات (كود 5)
                $journalId++;
                JournalHead::create([
                    'journal_id' => $journalId,
                    'total' => $totalExpenses,
                    'date' => $component->invoiceDate,
                    'op_id' => $operation->id,
                    'pro_type' => 59,
                    'details' => 'مصروفات تصنيع',
                    'user' => Auth::id(),
                    'branch_id' => $component->branch_id,
                ]);

                foreach ($component->additionalExpenses as $expense) {
                    JournalDetail::create([
                        'journal_id' => $journalId,
                        'account_id' => $expense['account_id'] ?? $component->expenseAccount ?? null,
                        'debit' => $expense['amount'],
                        'credit' => 0,
                        'type' => 1,
                        'info' => 'مصروفات تصنيع',
                        'op_id' => $operation->id,
                        'branch_id' => $component->branch_id,
                    ]);
                }
                JournalDetail::create([
                    'journal_id' => $journalId,
                    'account_id' => $operatingAccountId,
                    'debit' => 0,
                    'credit' => $totalExpenses,
                    'type' => 1,
                    'info' => 'مصروفات تصنيع',
                    'op_id' => $operation->id,
                    'branch_id' => $component->branch_id,
                ]);
            }

            // قيد إنتاج المنتجات
            $journalId++;
            JournalHead::create([
                'journal_id' => $journalId,
                'total' => $totalRaw + $totalExpenses,
                'date' => $component->invoiceDate,
                'op_id' => $operation->id,
                'pro_type' => 59,
                'details' => 'إنتاج منتجات تامة',
                'user' => Auth::id(),
                'branch_id' => $component->branch_id,
            ]);
            JournalDetail::create([
                'journal_id' => $journalId,
                'account_id' => $component->productAccount,
                'debit' => $totalRaw + $totalExpenses,
                'credit' => 0,
                'type' => 1,
                'info' => 'إنتاج منتجات تامة',
                'op_id' => $operation->id,
                'branch_id' => $component->branch_id,
            ]);
            JournalDetail::create([
                'journal_id' => $journalId,
                'account_id' => $operatingAccountId,
                'debit' => 0,
                'credit' => $totalRaw + $totalExpenses,
                'type' => 1,
                'info' => 'إنتاج منتجات تامة',
                'op_id' => $operation->id,
                'branch_id' => $component->branch_id,
            ]);

            DB::commit();

            // إعادة حساب average_cost والأرباح والقيود بعد التعديل
            // مهم: استخدام التاريخ والوقت معاً لضمان الترتيب الزمني الصحيح
            if (! empty($oldItemIds) && ! empty($oldOperationDate)) {
                try {
                    // جمع جميع الأصناف المتأثرة (القديمة + الجديدة)
                    $newItemIds = $operation->operationItems()
                        ->where('is_stock', 1)
                        ->pluck('item_id')
                        ->unique()
                        ->toArray();

                    $allAffectedItemIds = array_unique(array_merge($oldItemIds, $newItemIds));

                    // استخدام Helper لاختيار تلقائي للطريقة المناسبة (Queue/Stored Procedure/PHP)
                    // إعادة حساب average_cost (فاتورة التصنيع تؤثر على average_cost للمنتجات)
                    RecalculationServiceHelper::recalculateAverageCost($allAffectedItemIds, $oldOperationDate);

                    // إعادة حساب الأرباح والقيود للفواتير المتأثرة
                    // استخدام التاريخ والوقت معاً لضمان معالجة الفواتير في نفس اليوم بالترتيب الصحيح
                    RecalculationServiceHelper::recalculateProfitsAndJournals(
                        $allAffectedItemIds,
                        $oldOperationDate,
                        $operation->id, // استثناء الفاتورة الحالية من إعادة الحساب
                        $oldOperationCreatedAt // استخدام الوقت الأصلي للفاتورة لمقارنة الفواتير في نفس اليوم
                    );
                } catch (\Exception $e) {
                    Log::error('Error recalculating after manufacturing invoice update: '.$e->getMessage());
                    Log::error('Stack trace: '.$e->getTraceAsString());
                    // لا نوقف العملية، فقط نسجل الخطأ
                }
            }

            $component->isEditing = false;
            $component->originalInvoiceId = null;
            $component->dispatch('success-swal', title: 'تم التعديل!', text: 'تم تعديل فاتورة التصنيع بنجاح.', icon: 'success');

            return $operation->id;
        } catch (\Exception $e) {
            DB::rollBack();
            $component->dispatch('error-swal', title: 'خطأ!', text: 'حدث خطأ أثناء تعديل الفاتورة: '.$e->getMessage(), icon: 'error');

            return false;
        }
    }

    /**
     * ✅ محسّن: استخدام Cache لتقليل استعلامات قاعدة البيانات
     * هذه الدالة لا تزال مستخدمة في بعض الأماكن القديمة
     */
    private function getUnitFactor(int $itemId, ?int $unitId): float
    {
        if (! $unitId) {
            return 1;
        }

        $cacheKey = "unit_factor_{$itemId}_{$unitId}";

        return cache()->remember($cacheKey, 3600, function () use ($itemId, $unitId) {
            return DB::table('item_units')
                ->where('item_id', $itemId)
                ->where('unit_id', $unitId)
                ->value('u_val') ?? 1;
        });
    }

    /**
     * ✅ محسّن: استخدام Cache لتقليل استعلامات قاعدة البيانات
     * هذه الدالة لا تزال مستخدمة في بعض الأماكن القديمة
     */
    private function getBaseUnitId(int $itemId, ?int $fallbackUnitId = null): ?int
    {
        $cacheKey = "base_unit_id_{$itemId}";

        return cache()->remember($cacheKey, 3600, function () use ($itemId, $fallbackUnitId) {
            $baseUnitId = DB::table('item_units')
                ->where('item_id', $itemId)
                ->where('u_val', 1)
                ->value('unit_id');

            if (! $baseUnitId) {
                $baseUnitId = DB::table('item_units')
                    ->where('item_id', $itemId)
                    ->orderBy('u_val', 'asc')
                    ->value('unit_id');
            }

            return $baseUnitId ?? $fallbackUnitId;
        });
    }

    /**
     * حذف فاتورة تصنيع مع إعادة حساب average_cost والأرباح
     */
    public function deleteManufacturingInvoice(int $invoiceId): bool
    {
        try {
            $operation = OperHead::with('operationItems')->find($invoiceId);
            if (! $operation || $operation->pro_type != 59) {
                return false;
            }

            // حفظ معلومات الفاتورة قبل الحذف
            $operationDate = $operation->pro_date;
            $operationCreatedAt = $operation->created_at?->format('Y-m-d H:i:s');

            // جمع أصناف المنتجات فقط (qty_in > 0) إذا كانت الفاتورة مخزنية (is_stock = 1 على operhead)
            // المواد الخام لا تحتاج إعادة حساب متوسط تكلفة لأنها مصدرها فواتير المشتريات
            $itemIds = [];
            if ($operation->is_stock) {
                $itemIds = $operation->operationItems()
                    ->where('qty_in', '>', 0) // أصناف المنتجات التامة فقط
                    ->pluck('item_id')
                    ->unique()
                    ->toArray();
            }

            DB::beginTransaction();

            // حذف البيانات
            OperationItems::where('pro_id', $operation->id)->delete();
            Expense::where('op_id', $operation->id)->delete();
            JournalDetail::where('op_id', $operation->id)->delete();
            JournalHead::where('op_id', $operation->id)->delete();
            $operation->delete();

            DB::commit();

            // إعادة حساب average_cost والأرباح والقيود بعد الحذف
            // مهم: استخدام التاريخ والوقت معاً لضمان الترتيب الزمني الصحيح
            if (! empty($itemIds) && ! empty($operationDate)) {
                try {
                    // استخدام Helper لاختيار تلقائي للطريقة المناسبة (Queue/Stored Procedure/PHP)
                    // في حالة الحذف، نحسب من جميع الفواتير غير المحذوفة (لا من fromDate فقط)
                    RecalculationServiceHelper::recalculateAverageCost(
                        $itemIds,
                        $operationDate,
                        false, // forceQueue
                        true   // isDelete - مهم جداً!
                    );

                    // إعادة حساب الأرباح والقيود للفواتير المتأثرة
                    // استخدام التاريخ والوقت معاً لمعالجة الفواتير بعد الفاتورة المحذوفة (بما في ذلك نفس اليوم)
                    RecalculationServiceHelper::recalculateProfitsAndJournals(
                        $itemIds,
                        $operationDate,
                        null, // لا نستثني أي فاتورة عند الحذف (لأن الفاتورة محذوفة بالفعل)
                        $operationCreatedAt // استخدام الوقت لمعالجة الفواتير في نفس اليوم بالترتيب الصحيح
                    );
                } catch (\Exception $e) {
                    Log::error('Error recalculating after manufacturing invoice delete: '.$e->getMessage());
                    Log::error('Stack trace: '.$e->getTraceAsString());
                    // لا نوقف العملية، فقط نسجل الخطأ
                }
            }

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting manufacturing invoice: '.$e->getMessage());

            return false;
        }
    }
}
