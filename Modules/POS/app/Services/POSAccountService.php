<?php

declare(strict_types=1);

namespace Modules\POS\app\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Accounts\Models\AccHead;

/**
 * خدمة جلب الحسابات المستخدمة في POS
 * تُجمّع الاستعلامات المتكررة في مكان واحد لتجنب التكرار
 */
class POSAccountService
{
    /** @return Collection<int, AccHead> */
    public function getClientsAccounts(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1103%')
            ->select('id', 'aname')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getStores(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1104%')
            ->select('id', 'aname')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getEmployees(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '2102%')
            ->select('id', 'aname')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getCashAccounts(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('is_fund', 1)
            ->select('id', 'aname')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getBankAccounts(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where('code', 'like', '1102%')
            ->select('id', 'aname')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getExpenseAccounts(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->where(function ($q) {
                $q->where('code', 'like', '5101%')
                    ->orWhere('code', 'like', '5102%')
                    ->orWhere('code', 'like', '5103%')
                    ->orWhere('code', 'like', '5104%');
            })
            ->select('id', 'aname')
            ->orderBy('code')
            ->get();
    }

    /** @return Collection<int, AccHead> */
    public function getAllAccounts(): Collection
    {
        return AccHead::where('isdeleted', 0)
            ->where('is_basic', 0)
            ->select('id', 'aname', 'code')
            ->orderBy('code')
            ->get();
    }

    /**
     * جلب كل الحسابات المطلوبة دفعة واحدة
     *
     * @return array<string, Collection<int, AccHead>>
     */
    public function getAllForPOS(): array
    {
        return [
            'clientsAccounts' => $this->getClientsAccounts(),
            'stores'          => $this->getStores(),
            'employees'       => $this->getEmployees(),
            'cashAccounts'    => $this->getCashAccounts(),
            'bankAccounts'    => $this->getBankAccounts(),
            'expenseAccounts' => $this->getExpenseAccounts(),
        ];
    }
}
