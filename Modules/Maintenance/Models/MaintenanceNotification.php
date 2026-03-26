<?php

namespace Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceNotification extends Model
{
    protected $fillable = [
        'periodic_schedule_id',
        'notification_date',
        'status',
        'message',
    ];

    protected $casts = [
        'notification_date' => 'date',
    ];

    public function periodicSchedule()
    {
        return $this->belongsTo(PeriodicMaintenanceSchedule::class, 'periodic_schedule_id');
    }

    public function markAsSent()
    {
        $this->update(['status' => 'sent']);
    }

    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }
}
