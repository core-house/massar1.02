<?php

namespace App\Services\AnomalyDetection\Rules;

use App\Models\OperHead;
use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;

class OperHeadInvalidCurrencyRule implements AnomalyRule
{
    public function code(): string
    {
        return 'operhead.invalid_currency';
    }

    public function supports(Model $model): bool
    {
        return $model instanceof OperHead;
    }

    public function evaluate(Model $model): array
    {
        /** @var OperHead $model */
        $anomalies = [];

        if ($model->currency_id && ((float) $model->currency_rate) <= 0) {
            $anomalies[] = [
                'severity' => 'critical',
                'title' => 'Invalid currency rate',
                'description' => 'currency_id is set but currency_rate <= 0',
                'meta' => [
                    'currency_id' => $model->currency_id,
                    'currency_rate' => (float) $model->currency_rate,
                ],
            ];
        }

        if (!$model->currency_id && $model->currency_rate && ((float) $model->currency_rate) !== 1.0) {
            $anomalies[] = [
                'severity' => 'warning',
                'title' => 'Currency rate without currency',
                'description' => 'currency_rate is set but currency_id is null',
                'meta' => [
                    'currency_rate' => (float) $model->currency_rate,
                ],
            ];
        }

        return $anomalies;
    }
}

