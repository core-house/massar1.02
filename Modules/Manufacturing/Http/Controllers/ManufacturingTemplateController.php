<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\Invoices\Models\InvoiceTemplate;

/**
 * Controller for managing manufacturing invoice templates
 */
class ManufacturingTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Manufacturing Invoices')->only(['index', 'apiActiveTemplates']);
    }

    /**
     * API: Get active templates for the load template modal in invoice form
     */
    public function apiActiveTemplates(): \Illuminate\Http\JsonResponse
    {
        $templates = \App\Models\OperHead::withoutGlobalScopes()
            ->where('pro_type', 63)
            ->where('is_manager', 1) // Active only
            ->with(['operationItems.item.units', 'expenses'])
            ->orderBy('pro_date', 'desc')
            ->get();

        $result = $templates->map(function ($template) {
            $products = [];
            $rawMaterials = [];

            foreach ($template->operationItems as $item) {
                $units = ($item->item && $item->item->units)
                    ? $item->item->units->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'u_val' => $u->pivot->u_val ?? 1,
                    ])->toArray()
                    : [];

                $itemData = [
                    'id' => $item->item_id,
                    'name' => $item->item?->name ?? '-',
                    'unit_id' => $item->unit_id, // Read unit_id directly (display unit)
                    'quantity' => (float) ($item->fat_quantity ?? max($item->qty_in, $item->qty_out)), // Prefer fat_quantity
                    'unit_cost' => (float) ($item->fat_price ?? $item->cost_price ?? 0), // Prefer fat_price
                    'total_cost' => (float) ($item->detail_value ?? 0),
                    'average_cost' => (float) ($item->item?->average_cost ?? 0),
                    'units' => $units,
                    'unitsList' => $units,
                ];

                if ($item->pro_tybe == 64 || ($item->additional && (float)$item->additional > 0)) {
                    $itemData['cost_percentage'] = (float) ($item->additional ?? 0);
                    $products[] = $itemData;
                } else {
                    $rawMaterials[] = $itemData;
                }
            }

            return [
                'id' => $template->id,
                'name' => $template->info,
                'pro_id' => $template->pro_id,
                'date' => $template->pro_date,
                'expected_time' => $template->expected_time,
                'acc1' => $template->acc1,
                'acc2' => $template->acc2,
                'data' => [
                    'products' => $products,
                    'rawMaterials' => $rawMaterials,
                    'expenses' => $template->expenses->map(fn($e) => [
                        'account_id' => $e->account_id,
                        'amount' => (float) $e->amount,
                        'description' => $e->description,
                    ])->toArray(),
                ]
            ];
        });

        return response()->json(['success' => true, 'templates' => $result]);
    }

    /**
     * Display manufacturing templates
     */
    public function index(): View
    {
        // Get manufacturing templates from operhead (pro_type = 63)
        $templates = \App\Models\OperHead::where('pro_type', 63)
            ->with(['acc1Head', 'acc2Head', 'employee', 'branch'])
            ->orderBy('pro_date', 'desc')
            ->paginate(15);

        return view('manufacturing::templates.index', compact('templates'));
    }

    /**
     * Get template data for loading in invoice form
     */
    public function getTemplateData(int $templateId)
    {
        $template = \App\Models\OperHead::where('pro_type', 63)
            ->with(['operationItems.item.units'])
            ->findOrFail($templateId);

        // Parse products and raw materials
        $products = [];
        $rawMaterials = [];

        foreach ($template->operationItems as $item) {
            $itemData = [
                'id' => $item->item_id,
                'name' => $item->item?->name ?? '-',
                'unit_id' => $item->unit_id, // Read unit_id directly (display unit)
                'quantity' => (float) ($item->fat_quantity ?? max($item->qty_in, $item->qty_out)), // Prefer fat_quantity
                'unit_cost' => (float) ($item->fat_price ?? $item->cost_price ?? 0), // Prefer fat_price
                'total_cost' => (float) ($item->detail_value ?? 0),
                'average_cost' => (float) ($item->item?->average_cost ?? 0),
                'units' => ($item->item && $item->item->units)
                    ? $item->item->units->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'u_val' => $u->pivot->u_val ?? 1,
                    ])->toArray()
                    : [],
            ];

            // Determine if product or raw material using pro_tybe
            if ($item->pro_tybe == 64) {
                // Template product
                $itemData['cost_percentage'] = (float) ($item->additional ?? 0);
                $itemData['unitsList'] = $itemData['units'];
                $products[] = $itemData;
            } else {
                // Template raw material (pro_tybe == 63)
                $itemData['unitsList'] = $itemData['units'];
                $rawMaterials[] = $itemData;
            }
        }

        // Get expenses
        $expenses = \App\Models\Expense::where('op_id', $template->id)->get()->map(function ($expense) {
            return [
                'amount' => (float) $expense->amount,
                'account_id' => $expense->account_id,
                'description' => $expense->description ?? '',
            ];
        })->toArray();

        return response()->json([
            'products' => $products,
            'rawMaterials' => $rawMaterials,
            'expenses' => $expenses,
        ]);
    }

    /**
     * Toggle template active status
     */
    public function toggleActive(int $templateId): RedirectResponse
    {
        $template = \App\Models\OperHead::where('pro_type', 63)->findOrFail($templateId);
        
        // Toggle is_manager field (0 = inactive, 1 = active)
        $template->update([
            'is_manager' => !$template->is_manager,
        ]);

        return back()->with('success', __('manufacturing::manufacturing.template_status_updated'));
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(int $templateId): View
    {
        $template = \App\Models\OperHead::withoutGlobalScopes()
            ->where('pro_type', 63)
            ->with(['operationItems.item.units', 'employee', 'branch'])
            ->findOrFail($templateId);

        // Separate products and raw materials for the view
        $products = [];
        $rawMaterials = [];

        foreach ($template->operationItems as $item) {
            $itemData = [
                'id' => $item->item_id,
                'name' => $item->item?->name ?? '-',
                'unit_id' => $item->unit_id, // Read unit_id directly (display unit)
                'quantity' => (float) ($item->fat_quantity ?? max($item->qty_in, $item->qty_out)), // Prefer fat_quantity
                'unit_cost' => (float) ($item->fat_price ?? $item->cost_price ?? 0), // Prefer fat_price
                'total_cost' => (float) ($item->detail_value ?? 0),
                'average_cost' => (float) ($item->item?->average_cost ?? 0),
                'units' => ($item->item && $item->item->units)
                    ? $item->item->units->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'u_val' => $u->pivot->u_val ?? 1,
                    ])->toArray()
                    : [],
            ];
            $itemData['unitsList'] = $itemData['units'];

            if ($item->pro_tybe == 64 || ($item->additional && (float)$item->additional > 0)) {
                $itemData['cost_percentage'] = (float) ($item->additional ?? 0);
                $products[] = $itemData;
            } else {
                $rawMaterials[] = $itemData;
            }
        }

        // Load expenses
        $expenses = \App\Models\Expense::where('op_id', $template->id)->get()->map(function ($expense) {
            return [
                'account_id' => $expense->account_id,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
            ];
        });

        // Common data for the form
        $accounts = \Modules\Accounts\Models\AccHead::where('is_basic', 0)->where('isdeleted', 0)->get();
        $rawMaterialAccounts = $accounts;
        $employees = \App\Models\Employee::all();
        $expenseAccounts = \Modules\Accounts\Models\AccHead::where('is_basic', 0)->where('isdeleted', 0)->get();

        return view('manufacturing::templates.edit', compact(
            'template',
            'products',
            'rawMaterials',
            'expenses',
            'accounts',
            'rawMaterialAccounts',
            'employees',
            'expenseAccounts'
        ));
    }

    /**
     * Update a template
     */
    public function update(\Illuminate\Http\Request $request, int $templateId): RedirectResponse
    {
        try {
            $template = \App\Models\OperHead::withoutGlobalScopes()
                ->where('pro_type', 63)
                ->findOrFail($templateId);

            $templateName = $request->input('template_name', $template->info);

            // Parse JSON data
            $products = json_decode($request->input('products_data', '[]'), true) ?: [];
            $rawMaterials = json_decode($request->input('raw_materials_data', '[]'), true) ?: [];
            $expenses = json_decode($request->input('expenses_data', '[]'), true) ?: [];

            $totalRawMaterials = collect($rawMaterials)->sum('total_cost');
            $totalExpenses = collect($expenses)->sum('amount');
            $totalManufacturing = $totalRawMaterials + $totalExpenses;

            \Illuminate\Support\Facades\DB::beginTransaction();

            // Update template header
            $template->update([
                'info' => $templateName,
                'acc1' => $request->input('acc1'),
                'acc2' => $request->input('acc2'),
                'emp_id' => $request->input('emp_id'),
                'pro_date' => $request->input('pro_date', now()->format('Y-m-d')),
                'pro_value' => $totalManufacturing,
                'fat_net' => $totalManufacturing,
                'expected_time' => $request->input('expected_time'),
                'patch_number' => $request->input('patch_number'),
                'pro_id' => $request->input('pro_id'),
            ]);

            // Delete old items
            \App\Models\OperationItems::where('pro_id', $template->id)->delete();
            \App\Models\Expense::where('op_id', $template->id)->delete();

            // Add products
            foreach ($products as $product) {
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

                $displayPrice = $product['unit_cost'];
                $basePrice = $unitFactor > 0 ? ($displayPrice / $unitFactor) : $displayPrice;

                \App\Models\OperationItems::create([
                    'pro_tybe' => 64, // Template Product
                    'pro_id' => $template->id,
                    'item_id' => $product['id'],
                    'unit_id' => $product['unit_id'] ?? null,
                    'fat_unit_id' => $product['unit_id'] ?? null,
                    'qty_in' => 0,
                    'qty_out' => 0,
                    'fat_quantity' => $product['quantity'],
                    'fat_price' => $displayPrice,
                    'cost_price' => $basePrice,
                    'item_price' => $basePrice,
                    'detail_value' => $product['total_cost'],
                    'detail_store' => $request->input('acc1'),
                    'additional' => $product['cost_percentage'] ?? 0,
                    'is_stock' => 0,
                    'branch_id' => auth()->user()->current_branch_id ?? auth()->user()->branch_id,
                ]);
            }

            // Add raw materials
            foreach ($rawMaterials as $material) {
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

                $displayPrice = $material['unit_cost'];
                $basePrice = $unitFactor > 0 ? ($displayPrice / $unitFactor) : $displayPrice;

                \App\Models\OperationItems::create([
                    'pro_tybe' => 63, // Template Raw Material
                    'pro_id' => $template->id,
                    'item_id' => $material['id'],
                    'unit_id' => $material['unit_id'] ?? null,
                    'fat_unit_id' => $material['unit_id'] ?? null,
                    'qty_in' => 0,
                    'qty_out' => 0,
                    'fat_quantity' => $material['quantity'],
                    'fat_price' => $displayPrice,
                    'cost_price' => $basePrice,
                    'item_price' => $basePrice,
                    'detail_value' => $material['total_cost'],
                    'detail_store' => $request->input('acc2'),
                    'is_stock' => 0,
                    'branch_id' => auth()->user()->current_branch_id ?? auth()->user()->branch_id,
                ]);
            }

            // Add expenses
            foreach ($expenses as $expense) {
                \App\Models\Expense::create([
                    'op_id' => $template->id,
                    'account_id' => $expense['account_id'],
                    'amount' => $expense['amount'],
                    'description' => $expense['description'] ?? '',
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('manufacturing.templates.index')
                ->with('success', __('manufacturing::manufacturing.template_updated_successfully'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', __('manufacturing::manufacturing.failed_to_update_template') . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete template
     */
    public function destroy(int $templateId): RedirectResponse
    {
        $template = \App\Models\OperHead::withoutGlobalScopes()
            ->where('pro_type', 63)
            ->findOrFail($templateId);
        
        // Delete related operation items
        \App\Models\OperationItems::where('pro_id', $template->id)->delete();
        
        // Delete related expenses
        \App\Models\Expense::where('op_id', $template->id)->delete();
        
        // Delete the template
        $template->delete();

        return redirect()->route('manufacturing.templates.index')
            ->with('success', __('manufacturing::manufacturing.template_deleted_successfully'));
    }
}
