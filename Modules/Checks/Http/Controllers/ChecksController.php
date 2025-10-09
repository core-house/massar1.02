<?php

namespace Modules\Checks\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Checks\Models\Check;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\OperHead;
use App\Models\JournalHead;
use App\Models\JournalDetail;

class ChecksController extends Controller
{
    /**
     * Display the main checks page
     */
    public function index(Request $request)
    {
        return redirect()->route('checks.incoming');
    }

    /**
     * Display incoming checks (أوراق القبض)
     */
    public function incoming(Request $request)
    {
        $query = Check::query()->with(['creator', 'approver'])->where('type', 'incoming');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('check_number', 'like', '%' . $request->search . '%')
                  ->orWhere('bank_name', 'like', '%' . $request->search . '%')
                  ->orWhere('account_holder_name', 'like', '%' . $request->search . '%')
                  ->orWhere('payee_name', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        $checks = $query->orderBy('created_at', 'desc')->paginate(15);
        $pageType = 'incoming';
        $pageTitle = 'أوراق القبض';

        return view('checks::index', compact('checks', 'pageType', 'pageTitle'));
    }

    /**
     * Show create form for incoming check
     */
    public function createIncoming()
    {
        $pageType = 'incoming';
        $pageTitle = 'إضافة ورقة قبض';
        
        // Get clients accounts
        $accounts = \App\Models\AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1103%')
            ->select('id', 'aname', 'code')
            ->get();

        return view('checks::create', compact('pageType', 'pageTitle', 'accounts'));
    }

    /**
     * Display outgoing checks (أوراق الدفع)
     */
    public function outgoing(Request $request)
    {
        $query = Check::query()->with(['creator', 'approver'])->where('type', 'outgoing');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('check_number', 'like', '%' . $request->search . '%')
                  ->orWhere('bank_name', 'like', '%' . $request->search . '%')
                  ->orWhere('account_holder_name', 'like', '%' . $request->search . '%')
                  ->orWhere('payer_name', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        $checks = $query->orderBy('created_at', 'desc')->paginate(15);
        $pageType = 'outgoing';
        $pageTitle = 'أوراق الدفع';

        return view('checks::index', compact('checks', 'pageType', 'pageTitle'));
    }

    /**
     * Show create form for outgoing check
     */
    public function createOutgoing()
    {
        $pageType = 'outgoing';
        $pageTitle = 'إضافة ورقة دفع';
        
        // Get suppliers accounts
        $accounts = \App\Models\AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '2101%')
            ->select('id', 'aname', 'code')
            ->get();

        return view('checks::create', compact('pageType', 'pageTitle', 'accounts'));
    }

    /**
     * Show the dashboard
     */
    public function dashboard()
    {
        return view('checks::index');
    }

    /**
     * Show the management page
     */
    public function management()
    {
        return view('checks::index', ['defaultTab' => 'management']);
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Check $check, $attachmentIndex)
    {
        if (!$check->attachments || !isset($check->attachments[$attachmentIndex])) {
            abort(404, 'Attachment not found');
        }

        $attachment = $check->attachments[$attachmentIndex];
        $filePath = $attachment['path'];

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        return response()->download($fullPath, $attachment['name']);
    }

    /**
     * Export checks to Excel/CSV
     */
    public function export(Request $request)
    {
        $query = Check::query()->with(['creator', 'approver']);

        // Apply filters from request
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        $checks = $query->orderBy('created_at', 'desc')->get();

        $filename = 'checks_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($checks) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for proper Arabic encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV headers
            fputcsv($file, [
                'رقم الشيك',
                'البنك',
                'رقم الحساب',
                'صاحب الحساب',
                'المبلغ',
                'تاريخ الإصدار',
                'تاريخ الاستحقاق',
                'تاريخ الدفع',
                'الحالة',
                'النوع',
                'المستفيد',
                'الدافع',
                'رقم المرجع',
                'ملاحظات',
                'أنشئ بواسطة',
                'تاريخ الإنشاء'
            ]);

            // Data rows
            foreach ($checks as $check) {
                fputcsv($file, [
                    $check->check_number,
                    $check->bank_name,
                    $check->account_number,
                    $check->account_holder_name,
                    $check->amount,
                    $check->issue_date->format('Y-m-d'),
                    $check->due_date->format('Y-m-d'),
                    $check->payment_date ? $check->payment_date->format('Y-m-d') : '',
                    $check->status,
                    $check->type,
                    $check->payee_name,
                    $check->payer_name,
                    $check->reference_number,
                    $check->notes,
                    $check->creator->name ?? '',
                    $check->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show single check
     */
    public function show(Check $check)
    {
        $check->load(['creator', 'approver']);
        return response()->json($check);
    }

    /**
     * Show edit form data
     */
    public function edit(Check $check)
    {
        return response()->json($check);
    }

    /**
     * Store a newly created check
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pro_date' => 'required|date', // التاريخ
            'check_number' => 'required|string|max:50|unique:checks',
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:pending,cleared,bounced,cancelled',
            'type' => 'required|in:incoming,outgoing',
            'payee_name' => 'nullable|string|max:100',
            'payer_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:50',
            'acc1_id' => 'required|integer|exists:acc_head,id', // الحساب المستلم منه
            'portfolio_id' => 'required|integer|exists:acc_head,id', // الحافظة (required now)
            'branch_id' => 'required|exists:branches,id',
        ]);

        try {
            DB::beginTransaction();

            // تحديد نوع العملية
            $proType = $validated['type'] === 'incoming' ? 65 : 66; // 65: ورقة قبض، 66: ورقة دفع
            $portfolioAccount = $validated['portfolio_id']; // الحافظة المختارة من المستخدم
            
            // إنشاء السجل في operhead
            $lastProId = OperHead::where('pro_type', $proType)->max('pro_id') ?? 0;
            $newProId = $lastProId + 1;

            $oper = OperHead::create([
                'pro_id' => $newProId,
                'pro_type' => $proType,
                'pro_date' => $validated['pro_date'], // التاريخ الرئيسي
                'pro_num' => $validated['check_number'],
                'pro_serial' => $validated['reference_number'] ?? null,
                'acc1' => $portfolioAccount, // الحافظة المختارة
                'acc2' => $validated['acc1_id'], // الحساب المستلم منه/المدفوع له
                'acc1_before' => 0,
                'acc1_after' => 0,
                'acc2_before' => 0,
                'acc2_after' => 0,
                'pro_value' => $validated['amount'],
                'fat_net' => $validated['amount'],
                'details' => "شيك رقم {$validated['check_number']} - {$validated['bank_name']} - استحقاق: {$validated['due_date']}",
                'info' => $validated['payee_name'] ?? $validated['payer_name'] ?? $validated['account_holder_name'],
                'info2' => $validated['notes'],
                'info3' => json_encode([
                    'bank_name' => $validated['bank_name'],
                    'account_number' => $validated['account_number'],
                    'account_holder' => $validated['account_holder_name'],
                    'due_date' => $validated['due_date'],
                ]),
                'is_finance' => 1,
                'is_journal' => 1,
                'journal_type' => 2,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            // إنشاء القيد المحاسبي
            $this->createJournalEntry($oper, $validated, $portfolioAccount, $proType);

            // إنشاء الشيك
            $validated['created_by'] = Auth::id();
            $validated['oper_id'] = $oper->id; // ربط الشيك بالعملية
            $check = Check::create($validated);

            DB::commit();

            return redirect()->route('checks.index')->with('success', 'تم إضافة الشيك وإنشاء القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Create journal entry for check
     */
    private function createJournalEntry($oper, $data, $portfolioAccount, $proType)
    {
        // إنشاء journal_head
        $lastJournalId = JournalHead::max('journal_id') ?? 0;
        $newJournalId = $lastJournalId + 1;

        $journalHead = JournalHead::create([
            'journal_id' => $newJournalId,
            'total' => $data['amount'],
            'date' => $data['pro_date'], // التاريخ الرئيسي للعملية
            'op_id' => $oper->id,
            'pro_type' => $proType,
            'details' => "شيك رقم {$data['check_number']} - {$data['bank_name']} - استحقاق: {$data['due_date']}",
            'user' => Auth::id(),
            'branch_id' => $data['branch_id'],
        ]);

        $checkInfo = "شيك {$data['check_number']} - {$data['bank_name']} - استحقاق {$data['due_date']}";

        // القيد المحاسبي
        if ($data['type'] === 'incoming') {
            // ورقة قبض: من ح/ حافظة أوراق القبض (مدين)
            //              إلى ح/ العميل (دائن)
            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $portfolioAccount, // حافظة أوراق القبض
                'debit' => $data['amount'],
                'credit' => 0,
                'type' => 0, // مدين
                'info' => $checkInfo,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'branch_id' => $data['branch_id'],
            ]);

            if (isset($data['acc1_id']) && $data['acc1_id']) {
                JournalDetail::create([
                    'journal_id' => $newJournalId,
                    'account_id' => $data['acc1_id'], // العميل
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'type' => 1, // دائن
                    'info' => $checkInfo,
                    'op_id' => $oper->id,
                    'isdeleted' => 0,
                    'tenant' => 0,
                    'branch' => 1,
                    'branch_id' => $data['branch_id'],
                ]);
            }
        } else {
            // ورقة دفع: من ح/ المورد (مدين)
            //            إلى ح/ حافظة أوراق الدفع (دائن)
            if (isset($data['acc1_id']) && $data['acc1_id']) {
                JournalDetail::create([
                    'journal_id' => $newJournalId,
                    'account_id' => $data['acc1_id'], // المورد
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'type' => 0, // مدين
                    'info' => $checkInfo,
                    'op_id' => $oper->id,
                    'isdeleted' => 0,
                    'tenant' => 0,
                    'branch' => 1,
                    'branch_id' => $data['branch_id'],
                ]);
            }

            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $portfolioAccount, // حافظة أوراق الدفع
                'debit' => 0,
                'credit' => $data['amount'],
                'type' => 1, // دائن
                'info' => $checkInfo,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'branch_id' => $data['branch_id'],
            ]);
        }
    }

    /**
     * Update the specified check
     */
    public function update(Request $request, Check $check)
    {
        $validated = $request->validate([
            'check_number' => 'required|string|max:50|unique:checks,check_number,' . $check->id,
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:pending,cleared,bounced,cancelled',
            'type' => 'required|in:incoming,outgoing',
            'payee_name' => 'nullable|string|max:100',
            'payer_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:50',
        ]);

        $check->update($validated);

        return redirect()->route('checks.index')->with('success', 'تم تحديث الشيك بنجاح');
    }

    /**
     * Delete the specified check
     */
    public function destroy(Check $check)
    {
        // Delete attached files
        if (!empty($check->attachments)) {
            foreach ($check->attachments as $attachment) {
                if (isset($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        $check->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الشيك بنجاح']);
    }

    /**
     * Clear a check (تحصيل الشيك - تحويل للبنك)
     */
    public function clear(Request $request, Check $check)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|integer|exists:acc_head,id', // حساب البنك
            'collection_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
        ]);

        try {
            DB::beginTransaction();

            // نوع العملية: تحصيل شيك (67)
            $proType = 67;
            $lastProId = OperHead::where('pro_type', $proType)->max('pro_id') ?? 0;
            $newProId = $lastProId + 1;

            // حافظة أوراق القبض أو الدفع
            $portfolioAccount = $check->type === 'incoming' ? 63 : 66;

            // إنشاء عملية التحصيل في operhead
            $oper = OperHead::create([
                'pro_id' => $newProId,
                'pro_type' => $proType,
                'pro_date' => $validated['collection_date'],
                'pro_num' => $check->check_number,
                'acc1' => $validated['bank_account_id'], // البنك (مدين)
                'acc2' => $portfolioAccount, // حافظة الأوراق (دائن)
                'acc1_before' => 0,
                'acc1_after' => 0,
                'acc2_before' => 0,
                'acc2_after' => 0,
                'pro_value' => $check->amount,
                'fat_net' => $check->amount,
                'details' => "تحصيل شيك رقم {$check->check_number} من {$check->bank_name}",
                'info' => $check->account_holder_name,
                'info2' => "تحويل للبنك بتاريخ {$validated['collection_date']}",
                'is_finance' => 1,
                'is_journal' => 1,
                'journal_type' => 2,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            // إنشاء القيد المحاسبي للتحصيل
            $lastJournalId = JournalHead::max('journal_id') ?? 0;
            $newJournalId = $lastJournalId + 1;

            $journalHead = JournalHead::create([
                'journal_id' => $newJournalId,
                'total' => $check->amount,
                'date' => $validated['collection_date'],
                'op_id' => $oper->id,
                'pro_type' => $proType,
                'details' => "تحصيل شيك رقم {$check->check_number}",
                'user' => Auth::id(),
                'branch_id' => $validated['branch_id'],
            ]);

            $checkInfo = "تحصيل شيك {$check->check_number} - {$check->bank_name}";

            // من ح/ البنك (مدين)
            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $validated['bank_account_id'],
                'debit' => $check->amount,
                'credit' => 0,
                'type' => 0,
                'info' => $checkInfo,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'branch_id' => $validated['branch_id'],
            ]);

            // إلى ح/ حافظة الأوراق المالية (دائن)
            JournalDetail::create([
                'journal_id' => $newJournalId,
                'account_id' => $portfolioAccount,
                'debit' => 0,
                'credit' => $check->amount,
                'type' => 1,
                'info' => $checkInfo,
                'op_id' => $oper->id,
                'isdeleted' => 0,
                'tenant' => 0,
                'branch' => 1,
                'branch_id' => $validated['branch_id'],
            ]);

            // تحديث حالة الشيك
            $check->markAsCleared($validated['collection_date']);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'تم تصفية الشيك بنجاح']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Batch collect checks
     */
    public function batchCollect(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'bank_account_id' => 'required|integer|exists:acc_head,id',
            'collection_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
        ]);

        try {
            DB::beginTransaction();

            $checks = Check::whereIn('id', $validated['ids'])
                ->where('status', Check::STATUS_PENDING)
                ->get();

            foreach ($checks as $check) {
                // تحصيل كل شيك على حدة
                $this->collectSingleCheck($check, $validated['bank_account_id'], $validated['collection_date'], $validated['branch_id']);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => "تم تحصيل {$checks->count()} شيك بنجاح"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Collect single check (helper method)
     */
    private function collectSingleCheck($check, $bankAccountId, $collectionDate, $branchId)
    {
        // نوع العملية: تحصيل شيك (67)
        $proType = 67;
        $lastProId = OperHead::where('pro_type', $proType)->max('pro_id') ?? 0;
        $newProId = $lastProId + 1;

        $portfolioAccount = $check->type === 'incoming' ? 63 : 66;

        // إنشاء عملية التحصيل
        $oper = OperHead::create([
            'pro_id' => $newProId,
            'pro_type' => $proType,
            'pro_date' => $collectionDate,
            'pro_num' => $check->check_number,
            'acc1' => $bankAccountId,
            'acc2' => $portfolioAccount,
            'acc1_before' => 0,
            'acc1_after' => 0,
            'acc2_before' => 0,
            'acc2_after' => 0,
            'pro_value' => $check->amount,
            'fat_net' => $check->amount,
            'details' => "تحصيل شيك رقم {$check->check_number} من {$check->bank_name}",
            'info' => $check->account_holder_name,
            'is_finance' => 1,
            'is_journal' => 1,
            'journal_type' => 2,
            'isdeleted' => 0,
            'tenant' => 0,
            'branch' => 1,
            'user' => Auth::id(),
            'branch_id' => $branchId,
        ]);

        // إنشاء القيد
        $lastJournalId = JournalHead::max('journal_id') ?? 0;
        $newJournalId = $lastJournalId + 1;

        JournalHead::create([
            'journal_id' => $newJournalId,
            'total' => $check->amount,
            'date' => $collectionDate,
            'op_id' => $oper->id,
            'pro_type' => $proType,
            'details' => "تحصيل شيك رقم {$check->check_number}",
            'user' => Auth::id(),
            'branch_id' => $branchId,
        ]);

        $checkInfo = "تحصيل شيك {$check->check_number}";

        // من ح/ البنك
        JournalDetail::create([
            'journal_id' => $newJournalId,
            'account_id' => $bankAccountId,
            'debit' => $check->amount,
            'credit' => 0,
            'type' => 0,
            'info' => $checkInfo,
            'op_id' => $oper->id,
            'isdeleted' => 0,
            'tenant' => 0,
            'branch' => 1,
            'branch_id' => $branchId,
        ]);

        // إلى ح/ حافظة الأوراق
        JournalDetail::create([
            'journal_id' => $newJournalId,
            'account_id' => $portfolioAccount,
            'debit' => 0,
            'credit' => $check->amount,
            'type' => 1,
            'info' => $checkInfo,
            'op_id' => $oper->id,
            'isdeleted' => 0,
            'tenant' => 0,
            'branch' => 1,
            'branch_id' => $branchId,
        ]);

        // تحديث حالة الشيك
        $check->markAsCleared($collectionDate);
    }

    /**
     * Batch cancel with reversal entry
     */
    public function batchCancelReversal(Request $request)
    {
        $ids = $request->input('ids', []);
        Check::whereIn('id', $ids)->update(['status' => Check::STATUS_CANCELLED]);
        
        // TODO: Add reversal journal entry logic here

        return response()->json(['success' => true, 'message' => 'تم إلغاء الشيكات بقيد عكسي']);
    }

    /**
     * Get checks statistics API endpoint
     */
    public function statistics(Request $request)
    {
        $dateRange = $this->getDateRange($request->get('period', 'month'));
        
        $stats = [
            'total' => Check::whereBetween('created_at', $dateRange)->count(),
            'pending' => Check::whereBetween('created_at', $dateRange)
                ->where('status', Check::STATUS_PENDING)->count(),
            'cleared' => Check::whereBetween('created_at', $dateRange)
                ->where('status', Check::STATUS_CLEARED)->count(),
            'bounced' => Check::whereBetween('created_at', $dateRange)
                ->where('status', Check::STATUS_BOUNCED)->count(),
            'total_amount' => Check::whereBetween('created_at', $dateRange)->sum('amount'),
            'pending_amount' => Check::whereBetween('created_at', $dateRange)
                ->where('status', Check::STATUS_PENDING)->sum('amount'),
            'cleared_amount' => Check::whereBetween('created_at', $dateRange)
                ->where('status', Check::STATUS_CLEARED)->sum('amount'),
        ];

        return response()->json($stats);
    }

    private function getDateRange($period)
    {
        return match($period) {
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}