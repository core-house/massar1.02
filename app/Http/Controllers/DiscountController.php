<?php

namespace App\Http\Controllers;

use App\Models\AccHead;
use App\Models\OperHead;
use App\Models\JournalHead;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;    
use App\Http\Requests\CreatDiscountRequest;

class DiscountController extends Controller
{
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

        try {
            DB::beginTransaction();

            // إنشاء pro_id جديد حسب نوع الخصم
            $lastProId = OperHead::where('pro_type', $validated['type'])->max('pro_id');
            $newProId = $lastProId ? $lastProId + 1 : 1;

            // تحديد الحسابات حسب نوع الخصم
            if ($validated['type'] == 30) {
                // خصم مسموح به: acc1 = العملاء، acc2 = حساب الخصم (ثابت 91)
                $acc1 = $validated['acc1'];
                $acc2 = 91;
            } elseif ($validated['type'] == 31) {
                // خصم مكتسب: acc1 = حساب الخصم (ثابت 97), acc2 = المورد
                $acc1 = 97;
                $acc2 = $validated['acc2'];
            } else {
                throw new \Exception("نوع الخصم غير معروف.");
            }

            // إنشاء رأس العملية
            $oper = OperHead::create([
                'pro_id'        => $newProId,
                'pro_date'      => $validated['pro_date'],
                'pro_type'      => $validated['type'],
                'pro_num'       => $validated['pro_num'] ?? null,
                'pro_serial'    => null,
                'acc1'          => $acc1,
                'acc2'          => $acc2,
                'pro_value'     => $validated['pro_value'],
                'details'       => $validated['details'] ?? null,
                'isdeleted'     => 0,
                'tenant'        => 0,
                'branch'        => 1,
                'is_finance'    => 1,
                'is_journal'    => 1,
                'journal_type'  => 2,
                'emp_id'        => $validated['emp_id'],
                'emp2_id'       => $validated['emp2_id'] ?? null,
                'acc1_before'   => 0,
                'acc1_after'    => 0,
                'acc2_before'   => 0,
                'acc2_after'    => 0,
                'cost_center'   => $validated['cost_center'] ?? null,
                'user'          => Auth::id(),
                'info'          => $validated['info'] ?? null,
                'info2'         => $validated['info2'] ?? null,
                'info3'         => $validated['info3'] ?? null,
            ]);

            // إنشاء journal_id جديد
            $lastJournalId = JournalHead::max('journal_id');
            $newJournalId = $lastJournalId ? $lastJournalId + 1 : 1;

            // رأس اليومية
            $journalHead = JournalHead::create([
                'journal_id' => $newJournalId,
                'total'      => $validated['pro_value'],
                'date'       => $validated['pro_date'],
                'op_id'      => $oper->id,
                'pro_type'   => $validated['type'],
                'details'    => $validated['details'] ?? null,
                'user'       => Auth::id(),
            ]);

            // الطرف المدين
            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $acc1,
                'debit'      => $validated['pro_value'],
                'credit'     => 0,
                'type'       => 0,
                'info'       => $validated['info'] ?? null,
                'op_id'      => $oper->id,
                'isdeleted'  => 0,
            ]);

            // الطرف الدائن
            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $acc2,
                'debit'      => 0,
                'credit'     => $validated['pro_value'],
                'type'       => 1,
                'info'       => $validated['info'] ?? null,
                'op_id'      => $oper->id,
                'isdeleted'  => 0,
            ]);

            DB::commit();

            return redirect()->route('discounts.index')->with('success', 'تم حفظ الخصم وقيده بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
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
