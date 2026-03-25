<?php

namespace App\Services\AnomalyDetection\Rules;

use App\Models\OperationItems;
use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;

class OperationItemNegativeValuesRule implements AnomalyRule
{
    public function code(): string
    {
        return 'operation_items.negative_values';
    }

    public function supports(Model $model): bool
    {
        return $model instanceof OperationItems;
    }

    public function evaluate(Model $model): array
    {
        /** @var OperationItems $model */
        $fields = [
            'fat_price' => (float) ($model->fat_price ?? 0),
            'item_price' => (float) ($model->item_price ?? 0),
            'cost_price' => (float) ($model->cost_price ?? 0),
            'detail_value' => (float) ($model->detail_value ?? 0),
            'item_discount' => (float) ($model->item_discount ?? 0),
            'additional' => (float) ($model->additional ?? 0),
        ];

        $negative = [];
        foreach ($fields as $k => $v) {
            if ($v < 0) {
                $negative[$k] = $v;
            }
        }

        if (empty($negative)) {
            return [];
        }

        return [[
            'severity' => 'warning',
            'title' => 'Negative value fields',
            'description' => 'Some monetary fields are negative.',
            'meta' => [
                'negative_fields' => $negative,
            ],
        ]];
    }
}

