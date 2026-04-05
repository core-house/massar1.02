<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Http\Requests;

use App\Models\Item;
use App\Models\OperHead;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounts\Models\AccHead;

class StoreManufacturingInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pro_id' => ['required', 'string', 'max:255'],
            'pro_date' => ['required', 'date'],
            'acc1' => ['required', 'exists:acc_head,id'],
            'acc2' => ['required', 'exists:acc_head,id'],
            'emp_id' => ['required', 'exists:acc_head,id'],
            'operating_account' => ['nullable', 'exists:acc_head,id'],
            'products_data' => ['required', 'json'],
            'raw_materials_data' => ['required', 'json'],
            'expenses_data' => ['nullable', 'json'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Phase 1: Critical Validations
            $this->validateDuplicateInvoice($validator);
            $this->validateDuplicateTemplate($validator);
            $this->validateStockAvailability($validator);
            $this->validateAccountsExist($validator);
            $this->validateNonZeroCosts($validator);
        });
    }

    /**
     * Validation 10: Prevent duplicate manufacturing invoices
     */
    protected function validateDuplicateInvoice($validator): void
    {
        $proId = $this->input('pro_id');
        $branchId = auth()->user()->current_branch_id ?? auth()->user()->branch_id;

        $exists = OperHead::where('pro_type', 59)
            ->where('pro_id', $proId)
            ->where('branch_id', $branchId)
            ->exists();

        if ($exists) {
            $validator->errors()->add(
                'pro_id',
                __('manufacturing::manufacturing.duplicate_invoice_number')
            );
        }
    }

    /**
     * Validation 11: Prevent duplicate template names
     */
    protected function validateDuplicateTemplate($validator): void
    {
        // Only validate if saving as template
        $isTemplate = $this->has('is_template') && ($this->is_template == 1 || $this->is_template == 'true');

        if (! $isTemplate) {
            return;
        }

        $templateName = $this->input('template_name', $this->input('info', ''));
        $branchId = auth()->user()->current_branch_id ?? auth()->user()->branch_id;

        // Check if template with same name exists (pro_type 63 for templates)
        $exists = OperHead::where('pro_type', 63)
            ->where('info', $templateName)
            ->where('branch_id', $branchId)
            ->exists();

        if ($exists) {
            $validator->errors()->add(
                'template_name',
                __('manufacturing::manufacturing.duplicate_template_name')
            );
        }
    }

    /**
     * Validation 4: Raw materials have sufficient stock
     */
    protected function validateStockAvailability($validator): void
    {
        $rawMaterials = json_decode($this->input('raw_materials_data', '[]'), true) ?: [];
        $storeId = $this->input('acc2');

        foreach ($rawMaterials as $index => $material) {
            $itemId = $material['id'] ?? null;
            $quantity = $material['quantity'] ?? 0;
            $unitId = $material['unit_id'] ?? null;

            if (! $itemId || $quantity <= 0) {
                continue;
            }

            // Get item with units
            $item = Item::with('units')->find($itemId);
            if (! $item) {
                $validator->errors()->add(
                    "raw_materials.{$index}",
                    __('manufacturing::manufacturing.item_not_found')
                );

                continue;
            }

            // Calculate base quantity (convert to smallest unit)
            $baseQuantity = $quantity;
            if ($unitId) {
                $unit = $item->units->firstWhere('id', $unitId);
                if ($unit) {
                    $unitFactor = $unit->pivot->u_val ?? 1;
                    $baseQuantity = $quantity * $unitFactor;
                }
            }

            // Get available stock
            $availableStock = \DB::table('operation_items')
                ->join('operhead', 'operation_items.pro_id', '=', 'operhead.id')
                ->where('operation_items.item_id', $itemId)
                ->where('operhead.acc2', $storeId)
                ->where('operhead.isdeleted', 0)
                ->selectRaw('SUM(operation_items.qty_in - operation_items.qty_out) as available')
                ->value('available') ?? 0;

            if ($availableStock < $baseQuantity) {
                $validator->errors()->add(
                    "raw_materials.{$index}",
                    __('manufacturing::manufacturing.insufficient_stock', [
                        'item' => $item->name,
                        'available' => $availableStock,
                        'required' => $baseQuantity,
                    ])
                );
            }
        }
    }

    /**
     * Validation 8: Inventory accounts and WIP accounts exist
     */
    protected function validateAccountsExist($validator): void
    {
        $acc1 = $this->input('acc1'); // Products account
        $acc2 = $this->input('acc2'); // Raw materials account
        $operatingAccount = $this->input('operating_account');

        // Check products account
        $productsAccount = AccHead::find($acc1);
        if (! $productsAccount) {
            $validator->errors()->add('acc1', __('manufacturing::manufacturing.products_account_not_found'));
        }

        // Check raw materials account
        $rawMaterialsAccount = AccHead::find($acc2);
        if (! $rawMaterialsAccount) {
            $validator->errors()->add('acc2', __('manufacturing::manufacturing.raw_materials_account_not_found'));
        }

        // Check operating account if provided
        if ($operatingAccount) {
            $opAccount = AccHead::find($operatingAccount);
            if (! $opAccount) {
                $validator->errors()->add('operating_account', __('manufacturing::manufacturing.operating_account_not_found'));
            }
        }
    }

    /**
     * Validation 7: Production cost components are not zero
     */
    protected function validateNonZeroCosts($validator): void
    {
        $products = json_decode($this->input('products_data', '[]'), true) ?: [];
        $rawMaterials = json_decode($this->input('raw_materials_data', '[]'), true) ?: [];

        // Check products have non-zero costs
        foreach ($products as $index => $product) {
            $quantity = $product['quantity'] ?? 0;
            $unitCost = $product['unit_cost'] ?? 0;
            $totalCost = $product['total_cost'] ?? 0;

            if ($quantity <= 0) {
                $validator->errors()->add(
                    "products.{$index}.quantity",
                    __('manufacturing::manufacturing.product_quantity_must_be_positive')
                );
            }

            if ($unitCost < 0) {
                $validator->errors()->add(
                    "products.{$index}.unit_cost",
                    __('manufacturing::manufacturing.product_cost_cannot_be_negative')
                );
            }
        }

        // Check raw materials have non-zero costs
        foreach ($rawMaterials as $index => $material) {
            $quantity = $material['quantity'] ?? 0;
            $unitCost = $material['unit_cost'] ?? 0;

            if ($quantity <= 0) {
                $validator->errors()->add(
                    "raw_materials.{$index}.quantity",
                    __('manufacturing::manufacturing.raw_material_quantity_must_be_positive')
                );
            }

            if ($unitCost < 0) {
                $validator->errors()->add(
                    "raw_materials.{$index}.unit_cost",
                    __('manufacturing::manufacturing.raw_material_cost_cannot_be_negative')
                );
            }
        }

        // Check total manufacturing cost is not zero
        $totalRawMaterials = collect($rawMaterials)->sum('total_cost');
        $totalExpenses = 0;

        $expenses = json_decode($this->input('expenses_data', '[]'), true) ?: [];
        $totalExpenses = collect($expenses)->sum('amount');

        $totalManufacturingCost = $totalRawMaterials + $totalExpenses;

        if ($totalManufacturingCost <= 0) {
            $validator->errors()->add(
                'total_cost',
                __('manufacturing::manufacturing.total_manufacturing_cost_must_be_positive')
            );
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'pro_id.required' => __('manufacturing::manufacturing.invoice_number_required'),
            'pro_id.unique' => __('manufacturing::manufacturing.invoice_number_exists'),
            'pro_date.required' => __('manufacturing::manufacturing.invoice_date_required'),
            'acc1.required' => __('manufacturing::manufacturing.products_account_required'),
            'acc1.exists' => __('manufacturing::manufacturing.products_account_not_found'),
            'acc2.required' => __('manufacturing::manufacturing.raw_materials_account_required'),
            'acc2.exists' => __('manufacturing::manufacturing.raw_materials_account_not_found'),
            'emp_id.required' => __('manufacturing::manufacturing.employee_required'),
            'emp_id.exists' => __('manufacturing::manufacturing.employee_not_found'),
            'products_data.required' => __('manufacturing::manufacturing.products_required'),
            'raw_materials_data.required' => __('manufacturing::manufacturing.raw_materials_required'),
        ];
    }
}
