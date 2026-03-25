<?php

namespace App\Services\AnomalyDetection\Rules;

use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\Models\AccHead;

class AccHeadSuspiciousBalanceRule implements AnomalyRule
{
    public function code(): string
    {
        return 'acc_head.suspicious_balance';
    }

    public function supports(Model $model): bool
    {
        return $model instanceof AccHead;
    }

    public function evaluate(Model $model): array
    {
        /** @var AccHead $model */
        $balance = (float) ($model->balance ?? 0);
        $debitLimit = (float) ($model->debit_limit ?? 0);

        $anomalies = [];

        if ($debitLimit > 0 && abs($balance) > ($debitLimit * 1.20)) {
            $anomalies[] = [
                'severity' => 'warning',
                'title' => 'Balance exceeds debit limit',
                'description' => 'Account balance is significantly above debit_limit.',
                'meta' => [
                    'balance' => $balance,
                    'debit_limit' => $debitLimit,
                    'threshold' => $debitLimit * 1.20,
                ],
            ];
        }

        if (($model->isdeleted ?? false) && abs($balance) > 0) {
            $anomalies[] = [
                'severity' => 'info',
                'title' => 'Deleted account has balance',
                'description' => 'Account is marked deleted but balance is not zero.',
                'meta' => [
                    'balance' => $balance,
                    'isdeleted' => (bool) $model->isdeleted,
                ],
            ];
        }

        return $anomalies;
    }
}

