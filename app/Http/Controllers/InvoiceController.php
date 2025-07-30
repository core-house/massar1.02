<?php

namespace App\Http\Controllers;

use App\Models\OperHead;
use App\Models\JournalHead;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use Illuminate\Routing\Controller;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\AccHead;
use App\Models\Employee;
use App\Models\Item;

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

    if ($request->get('q') !== md5($type)) {
        abort(403, 'الطلب غير موثوق.');
    }

    return view('invoices.create', [
        'type' => $type,
        'hash' => $request->get('q'),
    ]);

        // $type = (int) $request->get('type');
        // $hash = $request->get('q');

        // if ($hash !== md5($type)) {
        //     abort(403, 'Invalid type hash');
        // }
        // $lastProId = OperHead::max('pro_id');
        // $nextProId = $lastProId ? $lastProId + 1 : 1;

        // $clientsAccounts = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)
        //     ->where('code', 'like', '122%')
        //     ->select('id', 'aname')->get();
        // $suppliersAccounts = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)
        //     ->where('code', 'like', '211%')
        //     ->select('id', 'aname')->get();
        // $stores = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)->where('code', 'like', '123%')
        //     ->select('id', 'aname')->get();
        // $employees = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)->where('code', 'like', '213%')
        //     ->select('id', 'aname')->get();
        // $wasted = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)
        //     ->where('code', 'like', '441%')
        //     ->select('id', 'aname')->get();
        // $accounts = AccHead::where('isdeleted', 0)
        //     ->where('is_basic', 0)
        //     ->where('code', 'not like', '123%')
        //     ->select('id', 'aname')->get();
        // $map = [
        //     10 => ['acc1' => 'clientsAccounts', 'acc2' => 'stores'],
        //     11 => ['acc1' => 'stores', 'acc2' => 'suppliersAccounts'],
        //     12 => ['acc1' => 'stores', 'acc2' => 'clientsAccounts'],
        //     13 => ['acc1' => 'suppliersAccounts', 'acc2' => 'stores'],
        //     14 => ['acc1' => 'clientsAccounts', 'acc2' => 'stores'],
        //     15 => ['acc1' => 'stores', 'acc2' => 'suppliersAccounts'],
        //     16 => ['acc1' => 'clientsAccounts', 'acc2' => 'stores'],
        //     17 => ['acc1' => 'stores', 'acc2' => 'suppliersAccounts'],
        //     18 => ['acc1' => 'wasted', 'acc2' => 'stores'],
        //     19 => ['acc1' => 'accounts', 'acc2' => 'stores'],
        //     20 => ['acc1' => 'stores', 'acc2' => 'accounts'],
        //     21 => ['acc1' => 'stores', 'acc2' => 'stores'],
        //     22 => ['acc1' => 'clientsAccounts', 'acc2' => 'stores'],
        // ];

        // $acc1List = isset($map[$type]) ? ${$map[$type]['acc1']} : collect();

        // // force acc2List to always be stores
        // $acc2List = $stores;

        // return view('invoices.create', compact(
        //     'type',
        //     'acc1List',
        //     'acc2List',
        //     'clientsAccounts',
        //     'suppliersAccounts',
        //     'stores',
        //     'employees',
        //     'wasted',
        //     'nextProId'
        // ));
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
        // حذف جميع العناصر المرتبطة من operation_items
        $operation->operationItems()->delete();
        // حذف قيود اليومية المرتبطة بـ op_id
        JournalDetail::where('op_id', $operation->id)->delete();
        JournalHead::where('op_id', $operation->id)->orWhere('op2', $operation->id)->delete();
        // حذف أي سند آلي مرتبط بـ op2
        $autoVoucher = OperHead::where('op2', $operation->id)->where('is_journal', 1)->where('is_stock', 0)->first();
        if ($autoVoucher) {
            // حذف قيوده اليومية
            JournalDetail::where('op_id', $autoVoucher->id)->delete();
            JournalHead::where('op_id', $autoVoucher->id)->orWhere('op2', $autoVoucher->id)->delete();
            // حذف السند نفسه
            $autoVoucher->delete();
        }
        // حذف العملية نفسها
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
