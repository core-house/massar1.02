<?php

namespace App\Services\AnomalyDetection\Rules;

use App\Models\OperationItems;
use App\Services\AnomalyDetection\Contracts\AnomalyRule;
use Illuminate\Database\Eloquent\Model;

class OperationItemInvalidQuantitiesRule implements AnomalyRule
{
    public function code(): string
    {
        return 'operation_items.invalid_quantities';
    }

    public function supports(Model $model): bool
    {
        return $model instanceof OperationItems;
    }

    public function evaluate(Model $model): array
    {
        /** @var OperationItems $model */
        $qtyIn = (float) ($model->qty_in ?? 0);
        $qtyOut = (float) ($model->qty_out ?? 0);
        $fatQty = (float) ($model->fat_quantity ?? 0);

        $anomalies = [];

        if ($qtyIn > 0 && $qtyOut > 0) {
            $anomalies[] = [
                'severity' => 'warning',
                'title' => 'Both qty_in and qty_out are set',
                'description' => 'An item line has both incoming and outgoing quantities.',
                'meta' => [
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                ],
            ];
        }

        if ($qtyIn < 0 || $qtyOut < 0 || $fatQty < 0) {
            $anomalies[] = [
                'severity' => 'critical',
                'title' => 'Negative quantity',
                'description' => 'One or more quantity fields are negative.',
                'meta' => [
                    'qty_in' => $qtyIn,
                    'qty_out' => $qtyOut,
                    'fat_quantity' => $fatQty,
                ],
            ];
        }

        if ($model->item_id && $fatQty === 0.0 && $qtyIn === 0.0 && $qtyOut === 0.0) {
            $anomalies[] = [
                'severity' => 'info',
                'title' => 'Zero quantity line',
                'description' => 'Item line has an item_id but all quantities are zero.',
                'meta' => [
                    'item_id' => (int) $model->item_id,
                ],
            ];
        }

        return $anomalies;
    }
}

