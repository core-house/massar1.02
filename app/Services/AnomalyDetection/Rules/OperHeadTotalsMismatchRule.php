<?php

namespace App\Services\AnomalyDetection\Rules;

use App\Models\OperHead;
use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;

class OperHeadTotalsMismatchRule implements AnomalyRule
{
    public function code(): string
    {
        return 'operhead.totals_mismatch';
    }

    public function supports(Model $model): bool
    {
        return $model instanceof OperHead;
    }

    public function evaluate(Model $model): array
    {
        /** @var OperHead $model */
        $items = $model->operationItems()->get(['id', 'detail_value', 'isdeleted']);
        if ($items->isEmpty()) {
            return [];
        }

        $sum = (float) $items->where('isdeleted', 0)->sum('detail_value');
        $headTotal = (float) ($model->fat_total ?? 0);
        $headNet = (float) ($model->fat_net ?? 0);

        // If totals are not used, skip
        if ($headTotal <= 0 && $headNet <= 0) {
            return [];
        }

        // Allow small rounding variance
        $tolerance = 0.05;

        $anomalies = [];
        if ($headTotal > 0 && abs($sum - $headTotal) > $tolerance) {
            $anomalies[] = [
                'severity' => 'warning',
                'title' => 'Items total mismatch',
                'description' => 'Sum(detail_value) does not match operhead.fat_total',
                'meta' => [
                    'items_sum' => $sum,
                    'fat_total' => $headTotal,
                    'tolerance' => $tolerance,
                ],
            ];
        }

        if ($headNet > 0 && $headTotal > 0 && $headNet > $headTotal) {
            $anomalies[] = [
                'severity' => 'warning',
                'title' => 'Net greater than total',
                'description' => 'operhead.fat_net > operhead.fat_total',
                'meta' => [
                    'fat_net' => $headNet,
                    'fat_total' => $headTotal,
                ],
            ];
        }

        return $anomalies;
    }
}

