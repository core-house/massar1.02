<?php

namespace App\Observers;

use App\Services\AnomalyDetection\AnomalyDetectionService;
use Modules\Accounts\Models\AccHead;

class AccHeadObserver
{
    public function saved(AccHead $accHead): void
    {
        app(AnomalyDetectionService::class)->detectAndStore($accHead);
    }
}

