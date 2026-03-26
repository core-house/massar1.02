# Kitchen Printer Reliability Upgrade - Rollout Plan

## Overview
هذا الدليل يوضح خطوات ترقية نظام الطباعة للمطبخ لتحقيق reliability-focused pipeline مع:
- ✅ Idempotency (0 duplicate prints)
- ✅ State machine lifecycle
- ✅ Outbox pattern
- ✅ Error classification & retry policies
- ✅ Health monitoring
- ✅ KPIs tracking

## Target KPIs
- Duplicate prints: 0 (verified via idempotency)
- Print success rate: >= 99.5%
- Mean time to detect agent down: < 2 minutes
- Mean dispatch latency: < 1s under normal load

---

## Phase 1: Database Migration (Zero Downtime)

### Step 1.1: Run Migration
```bash
php artisan migrate
```

هذا سيضيف الحقول الجديدة للـ `print_jobs` table:
- `idempotency_key` (unique)
- `payload_hash`
- `sequence`
- `error_type`
- `agent_http_status`
- `agent_response_body`
- `sent_at`
- `last_retry_at`
- `can_auto_retry`
- `retried_by`
- `retried_at`

### Step 1.2: Verify Migration
```bash
php artisan tinker
```

```php
// Check new columns exist
Schema::hasColumn('print_jobs', 'idempotency_key'); // should return true
Schema::hasColumn('print_jobs', 'error_type'); // should return true
```

### Backward Compatibility
✅ الـ migration يضيف حقول جديدة فقط، لا يحذف أو يعدل حقول موجودة
✅ الـ status enum تم توسيعه ليشمل القيم الجديدة مع الحفاظ على القديمة
✅ الـ indexes الجديدة لا تؤثر على الـ queries الموجودة

---

## Phase 2: Update Print Agent (Windows)

### Step 2.1: Add Health Endpoint
راجع `print-agent/HEALTH_ENDPOINT_GUIDE.md` لإضافة `/health` endpoint

### Step 2.2: Ensure Localhost-Only Binding
تأكد أن الـ Agent يعمل على `127.0.0.1:5000` فقط:

```csharp
listener.Prefixes.Add("http://127.0.0.1:5000/");
```

### Step 2.3: Test Health Endpoint
```bash
curl http://127.0.0.1:5000/health
```

Expected response:
```json
{
  "status": "healthy",
  "uptime_seconds": 120,
  "printers": [...],
  "recent_requests": [...],
  "stats": {...}
}
```

### Backward Compatibility
✅ الـ `/print` endpoint يبقى كما هو
✅ الـ `/health` endpoint جديد ولا يؤثر على الوظائف الموجودة

---

## Phase 3: Deploy New Code (Gradual Rollout)

### Step 3.1: Deploy to Staging First
```bash
# On staging server
git pull
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 3.2: Test on Staging
1. إنشاء فاتورة كاشير جديدة
2. التحقق من إنشاء print job مع `idempotency_key`
3. التحقق من الـ state machine (queued -> sending -> printed)
4. اختبار manual retry من Admin UI
5. التحقق من الـ monitoring dashboard

### Step 3.3: Monitor Staging for 24 Hours
راقب:
- Success rate
- Duplicate prints (should be 0)
- Dispatch latency
- Error types distribution

### Step 3.4: Deploy to Production
```bash
# On production server
php artisan down --message="Upgrading print system" --retry=60

git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

### Step 3.5: Restart Queue Workers
```bash
php artisan queue:restart
```

---

## Phase 4: Update Existing Code to Use New Services

### Step 4.1: Update Transaction Listener

**Before (Old Code):**
```php
// In TransactionSaved event listener
foreach ($stations as $station) {
    PrintKitchenOrderJob::dispatch($transaction, $station);
}
```

**After (New Code with Outbox Pattern):**
```php
use Modules\POS\Services\PrintJobIdempotencyService;
use Modules\POS\Services\PrintContentFormatter;

// In TransactionSaved event listener
public function handle(TransactionSaved $event)
{
    $transaction = $event->transaction;
    
    // Determine printer stations
    $stations = app(KitchenPrinterService::class)
        ->determinePrinterStations($transaction);
    
    if ($stations->isEmpty()) {
        return;
    }
    
    // Format content once
    $content = app(PrintContentFormatter::class)
        ->format($transaction, $stations->first());
    
    // Create print jobs inside transaction (Outbox pattern)
    DB::transaction(function () use ($transaction, $stations, $content) {
        $printJobs = app(PrintJobIdempotencyService::class)
            ->createPrintJobsInTransaction($transaction, $stations->toArray(), $content);
        
        // Dispatch jobs from created records
        foreach ($printJobs as $printJob) {
            PrintKitchenOrderJob::dispatch($printJob);
        }
    });
}
```

### Step 4.2: Update Manual Print Trigger

**Before:**
```php
PrintKitchenOrderJob::dispatch($transaction, $station, true, auth()->id());
```

**After:**
```php
use Modules\POS\Services\PrintJobIdempotencyService;

$printJob = app(PrintJobIdempotencyService::class)
    ->createManualRetryJob($originalPrintJob, auth()->id());

PrintKitchenOrderJob::dispatch($printJob);
```

---

## Phase 5: Enable Monitoring & Alerts

### Step 5.1: Add Monitoring Route
في `routes/web.php`:

```php
Route::middleware(['auth', 'permission:view Print Jobs'])
    ->group(function () {
        Route::get('/print-jobs/monitoring', [PrintJobController::class, 'monitoring'])
            ->name('print-jobs.monitoring');
    });
```

### Step 5.2: Add Monitoring Link to Menu
في navigation menu:

```blade
<a href="{{ route('print-jobs.monitoring') }}">
    <i class="las la-chart-line"></i>
    {{ __('pos.print_monitoring') }}
</a>
```

### Step 5.3: Setup Scheduled Health Checks
في `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Check print agent health every minute
    $schedule->call(function () {
        $health = app(PrintJobMonitoringService::class)->checkAgentHealth();
        
        if ($health['status'] !== 'healthy') {
            // Send alert to admins
            Log::critical('Print agent is down', $health);
            // TODO: Send notification to admins
        }
    })->everyMinute();
    
    // Generate daily KPI report
    $schedule->call(function () {
        $kpis = app(PrintJobMonitoringService::class)->getKPIs(24);
        Log::info('Daily print KPIs', $kpis);
        
        // Alert if success rate < 99.5%
        if ($kpis['success_rate'] < 99.5) {
            Log::warning('Print success rate below target', $kpis);
            // TODO: Send notification to admins
        }
    })->daily();
}
```

---

## Phase 6: Testing & Verification

### Test Case 1: Idempotency (No Duplicates)
```php
// Test: Same transaction should not create duplicate print jobs
$transaction = CashierTransaction::factory()->create();
$station = KitchenPrinterStation::factory()->create();
$content = "Test content";

// Create first print job
$job1 = app(PrintJobIdempotencyService::class)
    ->createPrintJobsInTransaction($transaction, [$station], $content);

// Try to create again with same content
$job2 = app(PrintJobIdempotencyService::class)
    ->createPrintJobsInTransaction($transaction, [$station], $content);

// Should return existing job, not create new one
assert($job1[0]->id === $job2[0]->id);
```

### Test Case 2: Error Classification
```php
// Test: PRINTER_NOT_FOUND should not auto-retry
$printJob = PrintJob::factory()->create([
    'status' => 'failed',
    'error_type' => 'PRINTER_NOT_FOUND',
    'can_auto_retry' => false,
]);

assert($printJob->canAutoRetry() === false);

// Test: AGENT_DOWN should auto-retry
$printJob2 = PrintJob::factory()->create([
    'status' => 'failed',
    'error_type' => 'AGENT_DOWN',
    'can_auto_retry' => true,
]);

assert($printJob2->canAutoRetry() === true);
```

### Test Case 3: State Machine
```php
// Test: Job lifecycle
$printJob = PrintJob::factory()->create(['status' => 'queued']);

// Should transition: queued -> sending
$printJob->markAsSending();
assert($printJob->status === 'sending');
assert($printJob->sent_at !== null);

// Should transition: sending -> printed
$printJob->markAsPrinted(200, 'OK');
assert($printJob->status === 'printed');
assert($printJob->printed_at !== null);
```

### Test Case 4: Manual Retry Audit
```php
// Test: Manual retry creates audit trail
$originalJob = PrintJob::factory()->create(['status' => 'failed']);
$userId = 1;

$retryJob = app(PrintJobIdempotencyService::class)
    ->createManualRetryJob($originalJob, $userId);

assert($retryJob->retried_by === $userId);
assert($retryJob->retried_at !== null);
assert($retryJob->sequence > $originalJob->sequence);
```

---

## Phase 7: Monitoring & Optimization

### Week 1: Monitor KPIs Daily
```bash
# Check KPIs via artisan command
php artisan tinker
```

```php
$kpis = app(\Modules\POS\Services\PrintJobMonitoringService::class)->getKPIs(24);
print_r($kpis);
```

Expected output:
```
[
    'success_rate' => 99.8,  // Target: >= 99.5%
    'failure_rate' => 0.2,
    'average_dispatch_latency' => 0.5,  // Target: < 1s
    'queue_backlog_length' => 2,
    'duplicate_prints' => 0,  // Target: 0
    ...
]
```

### Week 2-4: Fine-tune Retry Policies
بناءً على الـ error_type distribution، قد تحتاج لتعديل:
- Backoff intervals
- Max retries
- Timeout values

---

## Rollback Plan (If Needed)

### Step 1: Revert Code
```bash
git revert <commit-hash>
php artisan config:cache
php artisan route:cache
php artisan queue:restart
```

### Step 2: Keep Database Changes
⚠️ لا تعمل rollback للـ migration لأن الحقول الجديدة لا تؤثر على الكود القديم

### Step 3: Monitor
راقب النظام للتأكد من عودته للعمل الطبيعي

---

## Success Criteria

✅ **Idempotency:** 0 duplicate prints detected in monitoring
✅ **Success Rate:** >= 99.5% over 7 days
✅ **Agent Detection:** < 2 minutes mean time to detect agent down
✅ **Dispatch Latency:** < 1s average under normal load
✅ **Error Handling:** Logical errors (PRINTER_NOT_FOUND, INVALID_PAYLOAD) don't auto-retry
✅ **Audit Trail:** All manual retries logged with user info
✅ **Monitoring:** Dashboard shows real-time KPIs and alerts

---

## Support & Troubleshooting

### Issue: High Dispatch Latency
**Solution:** Check queue worker count and database performance

```bash
# Increase queue workers
php artisan queue:work --queue=kitchen-printing --tries=3 --timeout=10 &
```

### Issue: Agent Down Not Detected
**Solution:** Check scheduled task is running

```bash
php artisan schedule:list
php artisan schedule:run
```

### Issue: Duplicate Prints Detected
**Solution:** Check idempotency_key uniqueness

```sql
SELECT transaction_id, printer_station_id, payload_hash, COUNT(*) as count
FROM print_jobs
GROUP BY transaction_id, printer_station_id, payload_hash
HAVING count > 1;
```

---

## Next Steps After Rollout

1. **Week 1:** Monitor KPIs daily, fix any issues
2. **Week 2:** Optimize retry policies based on error distribution
3. **Week 3:** Add admin notifications for critical alerts
4. **Week 4:** Generate monthly reliability report
5. **Month 2:** Consider adding print job archiving for old records

---

## Contact

للدعم الفني، راجع:
- `KITCHEN_PRINTER_SETUP.md` - دليل الإعداد الأساسي
- `print-agent/HEALTH_ENDPOINT_GUIDE.md` - دليل الـ health endpoint
- Laravel logs: `storage/logs/laravel.log`
- Print agent logs: Check Windows Event Viewer
