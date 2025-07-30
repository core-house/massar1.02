<?php 
namespace App\Observers;

use App\Models\AccHead;
use App\Models\JournalDetail;
use Illuminate\Support\Facades\Log;

class JournalDetailObserver
{
    public function saved(JournalDetail $journalDetail)
    {
        $this->updateAccountAndParents($journalDetail->account_id);
    }

    public function updated(JournalDetail $journalDetail)
    {
        $this->updateAccountAndParents($journalDetail->account_id);
    }

    public function deleted(JournalDetail $journalDetail)
    {
        $this->updateAccountAndParents($journalDetail->account_id);
    }

    protected function updateAccountAndParents($accountId)
    {
        try {
            // أول حاجة: نحدث الحساب الحالي بناءً على قيوده
            $this->updateLeafAccountBalance($accountId);

            // ثم نحدث الحسابات الأب صعودًا
            $this->updateParentBalancesRecursive($accountId);
        } catch (\Throwable $e) {
            Log::error('Failed to update account and parent balances: ' . $e->getMessage());
        }
    }

    protected function updateLeafAccountBalance($accountId)
    {
        $totalDebit = JournalDetail::where('account_id', $accountId)->sum('debit');
        $totalCredit = JournalDetail::where('account_id', $accountId)->sum('credit');

        $balance = $totalDebit - $totalCredit;

        $accHead = AccHead::find($accountId);
        if ($accHead) {
            $accHead->balance = $balance;
            $accHead->save();
        }
    }

    protected function updateParentBalancesRecursive($accountId)
    {
        $accHead = AccHead::find($accountId);
        if (!$accHead || !$accHead->parent_id) {
            return;
        }

        $parent = AccHead::find($accHead->parent_id);
        if ($parent) {
            // نحسب مجموع أرصدة الحسابات الأبناء المباشرين
            $childTotal = AccHead::where('parent_id', $parent->id)->sum('balance');
            $parent->balance = $childTotal;
            $parent->save();

            // نستدعي الدالة مرة تانية للأب الأعلى
            $this->updateParentBalancesRecursive($parent->id);
        }
    }
}
