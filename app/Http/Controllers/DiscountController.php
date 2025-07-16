<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\AccHead;
use App\Models\OperHead;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\CreatDiscountRequest;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:عرض قائمة الخصومات المكتسبة')->only(['index']);
        $this->middleware('can:عرض قائمة الخصومات المسموح بها')->only(['index']);

        $this->middleware('can:إضافة قائمة الخصومات المسموح بها')->only(['create', 'store']);
        $this->middleware('can:	إضافة قائمة الخصومات المكتسبة')->only(['create', 'store']);

        $this->middleware('can:تعديل قائمة الخصومات المكتسبة')->only(['edit', 'update']);
        $this->middleware('can:تعديل قائمة الخصومات المسموح بها')->only(['edit', 'update']);

        $this->middleware('can:حذف قائمة الخصومات المكتسبة')->only(['destroy']);
        $this->middleware('can:حذف قائمة الخصومات المسموح بها')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $type = $request->input('type');

        if (!$type) {
            return redirect()->route('discounts.index', ['type' => 30]);
        }

        $discounts = OperHead::with(['acc1Head', 'acc2Head']);

        if ($type == 30) {
            $discounts = $discounts->where(function ($query) {
                $query->where('acc1', 91)->orWhere('acc2', 91);
            });
        } elseif ($type == 31) {
            $discounts = $discounts->where(function ($query) {
                $query->where('acc1', 97)->orWhere('acc2', 97);
            });
        }

        $discounts = $discounts->get();

        return view('discounts.index', compact('discounts', 'type'));
    }

    public function show() {}

    public function create(Request $request)
    {
        $type = (int) $request->get('type');
        $hash = $request->get('q');

        if ($hash !== md5($type)) {
            abort(403, __('نوع الرمز غير صالح'));
        }

        $lastProId = OperHead::max('pro_id');
        $nextProId = $lastProId ? $lastProId + 1 : 1;

        if ($type == 30) {
            // خصم مسموح به: acc1 العملاء - acc2 ثابت (id 91)
            $acc2Fixed = AccHead::findOrFail(91);
            $clientsAccounts = AccHead::where('isdeleted', 0)
                ->where('is_basic', 0)
                ->where('code', 'like', '122%')
                ->select('id', 'aname')->get();

            return view('discounts.create', [
                'type' => $type,
                'nextProId' => $nextProId,
                'acc2Fixed' => $acc2Fixed,
                'clientsAccounts' => $clientsAccounts
            ]);
        } elseif ($type == 31) {
            // خصم مكتسب: acc1 ثابت (id 97) - acc2 الموردين
            $acc1Fixed = AccHead::findOrFail(97);
            $suppliers = AccHead::where('isdeleted', 0)
                ->where('is_basic', 0)
                ->where('code', 'like', '211%')
                ->select('id', 'aname')
                ->get();

            return view('discounts.create', [
                'type' => $type,
                'nextProId' => $nextProId,
                'acc1Fixed' => $acc1Fixed,
                'suppliers' => $suppliers
            ]);
        } else {
            abort(404);
        }
    }

    public function store(CreatDiscountRequest $request)
    {
        $validated = $request->validated();
        $oper = new OperHead();
        $oper->pro_id = $request->pro_id;
        $oper->pro_date = $request->pro_date;
        $oper->info = $request->info ?? null;
        $oper->pro_value = $request->pro_value;

        if ($validated['type'] == 30) {
            // خصم مسموح به: acc1 = العملاء، acc2 ثابت (91)
            $oper->acc1 = $validated['acc1'];
            $oper->acc2 = 91;
        } elseif ($validated['type'] == 31) {
            // خصم مكتسب: acc1 ثابت (97), acc2 = المورد
            $oper->acc1 = 97;
            $oper->acc2 = $validated['acc2'];
        }
        $oper->save();
        Alert::toast('تم حفظ البيانات بنجاح', 'success');
        return redirect()->back()->with('success');
    }

    public function edit(Request $request, OperHead $discount)
    {
        $type = (int) $request->get('type');
        $titles = [
            30 => 'خصم مسموح به',
            31 => 'خصم مكتسب',
        ];
        if (!in_array($type, [30, 31])) {
            abort(403, 'نوع الخصم غير صحيح');
        }

        if ($type == 30) {
            $acc2Fixed = AccHead::findOrFail(91);
            $clientsAccounts = AccHead::where('isdeleted', 0)
                ->where('is_basic', 0)
                ->where('code', 'like', '122%')
                ->select('id', 'aname')
                ->get();

            return view('discounts.edit', compact('discount', 'type', 'acc2Fixed', 'clientsAccounts', 'titles'));
        } elseif ($type == 31) {
            $acc1Fixed = AccHead::findOrFail(97);
            $suppliers = AccHead::where('isdeleted', 0)
                ->where('is_basic', 0)
                ->where('code', 'like', '211%')
                ->select('id', 'aname')
                ->get();

            return view('discounts.edit', compact('discount', 'type', 'acc1Fixed', 'suppliers', 'titles'));
        }
    }

    public function update(CreatDiscountRequest $request, OperHead $discount)
    {
        $discount->pro_date = $request->pro_date;
        $discount->info = $request->info ?? null;
        $discount->pro_value = $request->pro_value;

        if ($request->type == 30) {
            $discount->acc1 = $request->acc1;
            $discount->acc2 = 91;
        } elseif ($request->type == 31) {
            $discount->acc1 = 97;
            $discount->acc2 = $request->acc2;
        }
        $discount->save();

        return redirect()->route('discounts.index')->with('success', 'تم تحديث الخصم بنجاح');
    }

    public function destroy($id)
    {
        $discount = OperHead::findOrFail($id);
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'تم حذف الخصم بنجاح');
    }
}
