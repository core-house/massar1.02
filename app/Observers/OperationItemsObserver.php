<?php

namespace App\Observers;

use App\Models\OperationItems;
use App\Services\AnomalyDetection\AnomalyDetectionService;

class OperationItemsObserver
{
    public function saved(OperationItems $operationItems): void
    {
        app(AnomalyDetectionService::class)->detectAndStore($operationItems);
    }
}

