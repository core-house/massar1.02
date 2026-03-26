<?php

namespace App\Observers;

use App\Models\OperHead;
use App\Services\AnomalyDetection\AnomalyDetectionService;

class OperHeadObserver
{
    public function saved(OperHead $operHead): void
    {
        app(AnomalyDetectionService::class)->detectAndStore($operHead);
    }
}

