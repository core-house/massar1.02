# Kitchen Printer Monitoring API Documentation

## Overview
هذا الدليل يوضح كيفية استخدام الـ monitoring endpoints للحصول على KPIs و health metrics.

---

## Endpoints

### 1. Get KPIs Dashboard
**Route:** `GET /print-jobs/monitoring`

**Permission:** `view Print Jobs`

**Description:** عرض dashboard شامل للـ KPIs والتنبيهات

**Response (HTML):** صفحة dashboard تحتوي على:
- KPIs للساعة الأخيرة
- KPIs لآخر 24 ساعة
- KPIs لآخر أسبوع
- التنبيهات الحالية
- حالة وكيل الطباعة
- Mean time to detect agent down

---

### 2. Get KPIs (Programmatic)
**Method:** استخدام Service مباشرة

```php
use Modules\POS\Services\PrintJobMonitoringService;

$service = app(PrintJobMonitoringService::class);

// Get KPIs for last 24 hours
$kpis = $service->getKPIs(24);
```

**Response:**
```php
[
    'success_rate' => 99.8,              // نسبة النجاح (%)
    'failure_rate' => 0.2,               // نسبة الفشل (%)
    'average_dispatch_latency' => 0.5,   // متوسط زمن الإرسال (ثانية)
    'queue_backlog_length' => 2,         // عدد المهام في قائمة الانتظار
    'per_station_failures' => [          // الفشل لكل محطة
        [
            'station_id' => 1,
            'station_name' => 'Kitchen',
            'failure_count' => 3
        ]
    ],
    'duplicate_prints' => 0,             // عدد الطباعات المكررة
    'error_type_distribution' => [       // توزيع أنواع الأخطاء
        'AGENT_DOWN' => 5,
        'TIMEOUT' => 2,
        'PRINTER_NOT_FOUND' => 1
    ],
    'total_jobs' => 1500,                // إجمالي المهام
    'period_hours' => 24,                // الفترة الزمنية
    'generated_at' => '2026-02-25T10:30:00Z'
]
```

---

### 3. Check Agent Health
**Method:** استخدام Service مباشرة

```php
use Modules\POS\Services\PrintJobMonitoringService;

$service = app(PrintJobMonitoringService::class);
$health = $service->checkAgentHealth();
```

**Response (Healthy):**
```php
[
    'status' => 'healthy',
    'data' => [
        'status' => 'healthy',
        'uptime_seconds' => 3600,
        'uptime_formatted' => '1h 0m 0s',
        'printers' => [
            [
                'name' => 'Kitchen Printer 1',
                'status' => 'ready',
                'is_default' => false
            ]
        ],
        'recent_requests' => [...],
        'stats' => [
            'total_requests' => 1250,
            'successful_requests' => 1245,
            'failed_requests' => 5,
            'success_rate' => 99.6
        ]
    ],
    'checked_at' => '2026-02-25T10:30:00Z'
]
```

**Response (Down):**
```php
[
    'status' => 'down',
    'error' => 'Connection refused',
    'checked_at' => '2026-02-25T10:30:00Z'
]
```

---

### 4. Get Alerts
**Method:** استخدام Service مباشرة

```php
use Modules\POS\Services\PrintJobMonitoringService;

$service = app(PrintJobMonitoringService::class);
$alerts = $service->getAlerts();
```

**Response:**
```php
[
    [
        'severity' => 'high',
        'type' => 'low_success_rate',
        'message' => 'معدل النجاح منخفض: 98.5% (الهدف: >= 99.5%)',
        'value' => 98.5
    ],
    [
        'severity' => 'critical',
        'type' => 'agent_down',
        'message' => 'وكيل الطباعة غير متاح: down',
        'value' => [...]
    ]
]
```

**Alert Severities:**
- `critical` - يتطلب تدخل فوري
- `high` - مشكلة خطيرة
- `medium` - تحذير
- `low` - معلومة

**Alert Types:**
- `low_success_rate` - معدل النجاح أقل من 99.5%
- `high_latency` - زمن الإرسال أكبر من 1 ثانية
- `high_backlog` - تراكم في قائمة الانتظار
- `duplicate_prints` - طباعات مكررة
- `agent_down` - وكيل الطباعة غير متاح

---

### 5. Get Print Job Details
**Route:** `GET /print-jobs/{id}`

**Permission:** `view Print Jobs`

**Description:** عرض تفاصيل مهمة طباعة محددة

**Response (HTML):** صفحة تفاصيل تحتوي على:
- معلومات المهمة الأساسية
- حالة المهمة (State Machine)
- تفاصيل الخطأ (إن وجد)
- معلومات الـ agent response
- المهام المرتبطة (نفس المعاملة والمحطة)
- Audit trail (من قام بإعادة المحاولة)

---

### 6. Manual Retry
**Route:** `POST /print-jobs/{id}/retry`

**Permission:** `retry Print Jobs`

**Description:** إعادة محاولة مهمة طباعة يدوياً

**Process:**
1. إنشاء print job جديد مع sequence أعلى
2. تسجيل audit trail (user_id, timestamp)
3. إضافة المهمة لقائمة الانتظار
4. تسجيل في logs

**Response:** Redirect back مع رسالة نجاح/فشل

---

### 7. Batch Retry
**Route:** `POST /print-jobs/batch-retry`

**Permission:** `retry Print Jobs`

**Request Body:**
```json
{
    "job_ids": [1, 2, 3, 4, 5]
}
```

**Description:** إعادة محاولة عدة مهام دفعة واحدة

**Response:** Redirect back مع عدد المهام الناجحة/الفاشلة

---

## Usage Examples

### Example 1: Check System Health
```php
use Modules\POS\Services\PrintJobMonitoringService;

$service = app(PrintJobMonitoringService::class);

// Check if system is healthy
$kpis = $service->getKPIs(1); // Last hour
$alerts = $service->getAlerts();

if ($kpis['success_rate'] >= 99.5 && empty($alerts)) {
    echo "System is healthy ✅";
} else {
    echo "System has issues ⚠️";
    print_r($alerts);
}
```

### Example 2: Daily Report
```php
use Modules\POS\Services\PrintJobMonitoringService;

$service = app(PrintJobMonitoringService::class);
$kpis = $service->getKPIs(24);

$report = "
📊 Daily Print Report
━━━━━━━━━━━━━━━━━━━━
✅ Success Rate: {$kpis['success_rate']}%
❌ Failure Rate: {$kpis['failure_rate']}%
⏱️ Avg Latency: {$kpis['average_dispatch_latency']}s
📦 Queue Backlog: {$kpis['queue_backlog_length']}
🔄 Duplicate Prints: {$kpis['duplicate_prints']}
📈 Total Jobs: {$kpis['total_jobs']}
";

// Send to admins via email/notification
```

### Example 3: Auto-Retry Failed Jobs
```php
use Modules\POS\Models\PrintJob;
use Modules\POS\Jobs\PrintKitchenOrderJob;

// Get auto-retryable failed jobs
$failedJobs = PrintJob::autoRetryable()
    ->where('attempts', '<', 3)
    ->get();

foreach ($failedJobs as $job) {
    $job->markAsQueued();
    PrintKitchenOrderJob::dispatch($job);
}
```

### Example 4: Monitor Agent Health
```php
use Modules\POS\Services\PrintJobMonitoringService;
use Illuminate\Support\Facades\Log;

$service = app(PrintJobMonitoringService::class);
$health = $service->checkAgentHealth();

if ($health['status'] !== 'healthy') {
    Log::critical('Print agent is down', $health);
    
    // Send notification to admins
    // Notification::send($admins, new PrintAgentDownNotification($health));
}
```

---

## Scheduled Tasks

### Health Check (Every Minute)
```php
// In app/Console/Kernel.php
$schedule->call(function () {
    $health = app(PrintJobMonitoringService::class)->checkAgentHealth();
    
    if ($health['status'] !== 'healthy') {
        Log::critical('Print agent is down', $health);
    }
})->everyMinute();
```

### Daily KPI Report
```php
$schedule->call(function () {
    $kpis = app(PrintJobMonitoringService::class)->getKPIs(24);
    Log::info('Daily print KPIs', $kpis);
    
    if ($kpis['success_rate'] < 99.5) {
        Log::warning('Print success rate below target', $kpis);
    }
})->daily();
```

### Weekly Summary
```php
$schedule->call(function () {
    $kpis = app(PrintJobMonitoringService::class)->getKPIs(168); // 7 days
    
    // Generate and send weekly report
})->weekly();
```

---

## Metrics Interpretation

### Success Rate
- **>= 99.5%** ✅ Excellent
- **95-99.5%** ⚠️ Needs attention
- **< 95%** ❌ Critical issue

### Dispatch Latency
- **< 1s** ✅ Excellent
- **1-3s** ⚠️ Acceptable
- **> 3s** ❌ Performance issue

### Queue Backlog
- **0-5** ✅ Normal
- **5-10** ⚠️ Monitor closely
- **> 10** ❌ Queue congestion

### Duplicate Prints
- **0** ✅ Perfect (idempotency working)
- **> 0** ❌ Critical bug (investigate immediately)

---

## Troubleshooting

### High Failure Rate
1. Check agent health: `$service->checkAgentHealth()`
2. Check error distribution: `$kpis['error_type_distribution']`
3. Check per-station failures: `$kpis['per_station_failures']`
4. Review Laravel logs: `storage/logs/laravel.log`

### High Latency
1. Check queue worker count
2. Check database performance
3. Check network latency to agent
4. Consider increasing queue workers

### Agent Down
1. Check if agent is running: `http://127.0.0.1:5000/health`
2. Check Windows Event Viewer
3. Restart agent: `start-admin.bat`
4. Check firewall settings

### Duplicate Prints
1. Check idempotency_key uniqueness:
   ```sql
   SELECT idempotency_key, COUNT(*) 
   FROM print_jobs 
   GROUP BY idempotency_key 
   HAVING COUNT(*) > 1;
   ```
2. Review transaction commit logic
3. Check for race conditions

---

## Best Practices

1. **Monitor Daily:** Check KPIs dashboard daily
2. **Set Up Alerts:** Configure notifications for critical issues
3. **Review Logs:** Check Laravel logs weekly
4. **Test Agent:** Test health endpoint regularly
5. **Archive Old Jobs:** Archive print jobs older than 30 days
6. **Optimize Queries:** Add indexes if monitoring queries are slow
7. **Document Issues:** Keep track of recurring issues and solutions

---

## API Rate Limits

- Health checks: Cached for 1 minute
- KPI queries: No limit (but consider caching for frequent access)
- Manual retries: No limit (but logged for audit)

---

## Security Considerations

1. **Permissions:** All endpoints require authentication and permissions
2. **Audit Logging:** All manual retries are logged with user info
3. **Agent Security:** Agent binds to 127.0.0.1 only
4. **Data Privacy:** Print content may contain sensitive data, handle carefully

---

## Support

للمزيد من المعلومات:
- `RELIABILITY_UPGRADE_SUMMARY.md` - ملخص التحسينات
- `RELIABILITY_UPGRADE_ROLLOUT_PLAN.md` - خطة النشر
- `KITCHEN_PRINTER_SETUP.md` - دليل الإعداد
- `print-agent/HEALTH_ENDPOINT_GUIDE.md` - دليل الـ health endpoint
