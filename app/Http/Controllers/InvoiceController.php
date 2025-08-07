<?php

namespace App\Http\Controllers;

use App\Models\{OperHead, JournalHead, AccHead, Employee, Item, JournalDetail};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class InvoiceController extends Controller
{


    public function index(Request $request)
    {
        $type = (int) $request->get('type');

        $permissions = [
            10 => 'عرض فاتورة مبيعات',
            11 => 'عرض فاتورة مشتريات',
            12 => 'عرض مردود مبيعات',
            13 => 'عرض مردود مشتريات',
            14 => 'عرض أمر بيع',
            15 => 'عرض أمر شراء',
            16 => 'عرض عرض سعر لعميل',
            17 => 'عرض عرض سعر من مورد',
            18 => 'عرض فاتورة تالف',
            19 => 'عرض أمر صرف',
            20 => 'عرض أمر إضافة',
            21 => 'عرض تحويل من مخزن لمخزن',
            22 => 'عرض أمر حجز',
        ];

        if (!isset($permissions[$type])) {
            abort(404, 'نوع العملية غير معروف');
        }

        if (!auth()->user()->can($permissions[$type])) {
            abort(403, 'ليس لديك صلاحية لعرض هذا النوع.');
        }

        $invoices = OperHead::with(['acc1Headuser', 'store', 'employee', 'acc1Head', 'acc2Head', 'type'])
            ->where('pro_type', $type)
            ->get();

        return view('invoices.index', compact('invoices', 'type'));
    }

    public function create(Request $request)
    {
        $type = (int) $request->get('type');

        $permissions = [
            10 => 'إضافة فاتورة مبيعات',
            11 => 'إضافة فاتورة مشتريات',
            12 => 'إضافة مردود مبيعات',
            13 => 'إضافة مردود مشتريات',
            14 => 'إضافة أمر بيع',
            15 => 'إضافة أمر شراء',
            16 => 'إضافة عرض سعر لعميل',
            17 => 'إضافة عرض سعر من مورد',
            18 => 'إضافة فاتورة تالف',
            19 => 'إضافة أمر صرف',
            20 => 'إضافة أمر إضافة',
            21 => 'إضافة تحويل من مخزن لمخزن',
            22 => 'إضافة أمر حجز',
        ];

        if (!isset($permissions[$type])) {
            abort(404, 'نوع العملية غير معروف');
        }

        if (!auth()->user()->can($permissions[$type])) {
            abort(403, 'ليس لديك صلاحية لإضافة هذا النوع.');
        }

        // التحقق من الـ hash
        $expectedHash = md5($type);
        $providedHash = $request->get('q');

        if ($providedHash !== $expectedHash) {
            abort(403, 'الطلب غير موثوق.');
        }

        return view('invoices.create', [
            'type' => $type,
            'hash' => $expectedHash,
        ]);
    }

    public function store(Request $request) {}

    public function show(string $id) {}



    public function edit(OperHead $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        $operation = OperHead::findOrFail($id);
        $operation->operationItems()->delete();

        JournalDetail::where('op_id', $operation->id)->delete();
        JournalHead::where('op_id', $operation->id)->orWhere('op2', $operation->id)->delete();
        $autoVoucher = OperHead::where('op2', $operation->id)->where('is_journal', 1)->where('is_stock', 0)->first();

        if ($autoVoucher) {
            JournalDetail::where('op_id', $autoVoucher->id)->delete();
            JournalHead::where('op_id', $autoVoucher->id)->orWhere('op2', $autoVoucher->id)->delete();
            $autoVoucher->delete();
        }

        $operation->delete();
        Alert::toast('تم حذف العملية وسنداتها بنجاح.', 'success');
        return redirect()->back();
    }

    public function print(Request $request, $operation_id)
    {
        $operation = OperHead::with('operationItems')->findOrFail($operation_id);

        $acc1List = AccHead::where('id', $operation->acc1)->get();
        $acc2List = AccHead::where('id', $operation->acc2)->get();
        $employees = Employee::where('id', $operation->emp_id)->get();
        $items = Item::whereIn('id', $operation->operationItems->pluck('item_id'))->get();

        return view('invoices.print-invoice', [
            'pro_id' => $operation->pro_id,
            'pro_date' => $operation->pro_date,
            'accural_date' => $operation->accural_date,
            'serial_number' => $operation->pro_serial,
            'acc1_id' => $operation->acc1,
            'acc2_id' => $operation->acc2,
            'emp_id' => $operation->emp_id,
            'type' => $operation->pro_type,
            'titles' => [
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
            ],
            'acc1Role' => in_array($operation->pro_type, [10, 12, 14, 16, 22]) ? 'مدين' : (in_array($operation->pro_type, [11, 13, 15, 17]) ? 'دائن' : (in_array($operation->pro_type, [18, 19, 20, 21]) ? 'مدين' : 'غير محدد')),
            'acc1List' => $acc1List,
            'acc2List' => $acc2List,
            'employees' => $employees,
            'items' => $items,
            'invoiceItems' => $operation->operationItems->map(function ($item) {
                $unit = \App\Models\Unit::find($item->unit_id);
                return [
                    'item_id' => $item->item_id,
                    'unit_id' => $item->unit_id,
                    'quantity' => $item->qty_in ?: $item->qty_out,
                    'price' => $item->item_price,
                    'discount' => $item->item_discount,
                    'sub_value' => $item->detail_value,
                    'available_units' => collect([$unit]),
                ];
            })->toArray(),
            'subtotal' => $operation->fat_total,
            'discount_percentage' => $operation->fat_disc_per,
            'discount_value' => $operation->fat_disc,
            'additional_percentage' => $operation->fat_plus_per,
            'additional_value' => $operation->fat_plus,
            'total_after_additional' => $operation->fat_net,
            'received_from_client' => $operation->pro_value,
            'notes' => $operation->info,
        ]);
    }
}
