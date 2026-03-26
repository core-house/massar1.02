<?php

namespace Modules\Invoices\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Invoices\Http\Requests\InvoiceTemplateRequest;
use Modules\Invoices\Models\InvoiceTemplate;

class InvoiceTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view Invoice Templates')->only(['index']);
        $this->middleware('permission:create Invoice Templates')->only(['create', 'store']);
        $this->middleware('permission:edit Invoice Templates')->only(['edit', 'update', 'toggleActive']);
        $this->middleware('permission:delete Invoice Templates')->only(['destroy']);
    }

    public function index()
    {
        $templates = InvoiceTemplate::with('invoiceTypes')
            ->orderBy('sort_order')
            ->paginate(15);

        return view('invoices::invoice-templates.index', compact('templates'));
    }

    public function create()
    {
        $availableColumns = InvoiceTemplate::availableColumns();
        $availableSections = InvoiceTemplate::availableSections();
        $invoiceTypes = [
            10 => 'فاتورة مبيعات',
            11 => 'فاتورة مشتريات',
            12 => 'مردود مبيعات',
            13 => 'مردود مشتريات',
            14 => 'أمر بيع',
            15 => 'أمر شراء',
            16 => 'عرض سعر لعميل',
            17 => 'عرض سعر من مورد',
            18 => 'فاتورة توالف',
            19 => 'أمر صرف',
            20 => 'أمر إضافة',
            21 => 'تحويل من مخزن لمخزن',
            22 => 'أمر حجز',
            24 => 'فاتورة خدمة',
            25 => 'طلب احتياج',
        ];

        return view('invoices::invoice-templates.create', compact('availableColumns', 'availableSections', 'invoiceTypes'));
    }

    public function store(InvoiceTemplateRequest $request)
    {
        $validated = $request->validated();

        $template = InvoiceTemplate::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'visible_columns' => $validated['visible_columns'],
            'column_widths' => $validated['column_widths'] ?? [],
            'column_order' => $validated['column_order'] ?? [],
            'printable_sections' => $validated['printable_sections'] ?? InvoiceTemplate::defaultPrintableSections(),
            'preamble_text' => $validated['preamble_text'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        foreach ($validated['invoice_types'] as $invoiceType) {
            $isDefault = isset($validated['is_default']) &&
                in_array($invoiceType, $validated['is_default']);

            if ($isDefault) {
                \Modules\Invoices\Models\InvoiceTypeTemplate::where('invoice_type', $invoiceType)
                    ->where('template_id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }

            $template->invoiceTypes()->create([
                'invoice_type' => $invoiceType,
                'is_default' => $isDefault,
            ]);
        }

        return redirect()->route('invoice-templates.index')
            ->with('success', 'تم إنشاء النموذج بنجاح');
    }

    public function edit(InvoiceTemplate $template)
    {
        $template->load('invoiceTypes');
        $availableColumns = InvoiceTemplate::availableColumns();
        $availableSections = InvoiceTemplate::availableSections();
        $invoiceTypes = [
            10 => 'فاتورة مبيعات',
            11 => 'فاتورة مشتريات',
            12 => 'مردود مبيعات',
            13 => 'مردود مشتريات',
            14 => 'أمر بيع',
            15 => 'أمر شراء',
            16 => 'عرض سعر لعميل',
            17 => 'عرض سعر من مورد',
            18 => 'فاتورة توالف',
            19 => 'أمر صرف',
            20 => 'أمر إضافة',
            21 => 'تحويل من مخزن لمخزن',
            22 => 'أمر حجز',
            24 => 'فاتورة خدمة',
            25 => 'طلب احتياج',
        ];

        return view('invoices::invoice-templates.edit', compact('template', 'availableColumns', 'availableSections', 'invoiceTypes'));
    }

    public function update(InvoiceTemplateRequest $request, InvoiceTemplate $template)
    {
        $validated = $request->validated();

        $template->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'visible_columns' => $validated['visible_columns'],
            'column_widths' => $validated['column_widths'] ?? [],
            'column_order' => $validated['column_order'] ?? [],
            'printable_sections' => $validated['printable_sections'] ?? $template->printable_sections ?? InvoiceTemplate::defaultPrintableSections(),
            'preamble_text' => $validated['preamble_text'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $template->invoiceTypes()->delete();

        foreach ($validated['invoice_types'] as $invoiceType) {
            $isDefault = isset($validated['is_default']) &&
                in_array($invoiceType, $validated['is_default']);


            if ($isDefault) {
                \Modules\Invoices\Models\InvoiceTypeTemplate::where('invoice_type', $invoiceType)
                    ->where('template_id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }

            $template->invoiceTypes()->create([
                'invoice_type' => $invoiceType,
                'is_default' => $isDefault,
            ]);
        }

        return redirect()->route('invoice-templates.index')
            ->with('success', 'تم تحديث النموذج بنجاح');
    }

    public function destroy(InvoiceTemplate $template)
    {
        $template->delete();

        return redirect()->route('invoice-templates.index')
            ->with('success', 'تم حذف النموذج بنجاح');
    }

    public function show(InvoiceTemplate $template)
    {
        $template->load('invoiceTypes');

        return view('invoices::invoice-templates.show', compact('template'));
    }

    public function toggleActive(InvoiceTemplate $template)
    {
        $template->update([
            'is_active' => ! $template->is_active,
        ]);

        return back()->with('success', 'تم تحديث حالة النموذج');
    }

    /**
     * Get template data for AJAX requests
     */
    public function getTemplateData(InvoiceTemplate $template)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $template->id,
                'name' => $template->name,
                'code' => $template->code,
                'visible_columns' => $template->visible_columns,
                'column_widths' => $template->column_widths,
                'column_order' => $template->column_order,
            ],
        ]);
    }
}
