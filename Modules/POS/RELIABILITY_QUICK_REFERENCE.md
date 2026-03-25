# Kitchen Printer Reliability - Quick Reference

## 🎯 Target KPIs

| Metric | Target | Current Status |
|--------|--------|----------------|
| Duplicate prints | 0 | Check: `$kpis['duplicate_prints']` |
| Success rate | >= 99.5% | Check: `$kpis['success_rate']` |
| Agent detection | < 2 min | Check: `$service->getMeanTimeToDetectAgentDown()` |
| Dispatch latency | < 1s | Check: `$kpis['average_dispatch_latency']` |

---

## 🚀 Quick Commands

### Check System Health
```bash
php artisan tinker
```
```php
$service = app(\Modules\POS\Services\PrintJobMonitoringService::class);
$kpis = $service->getKPIs(24);
print_r($kpis);
```

### Check Agent Health
```bash
curl http://127.0.0.1:5000/health
```

### View Recent Failed Jobs
```bash
php artisan tinker
```
```php
\Modules\POS\Models\PrintJob::where('status', 'failed')
    ->recent(24)
    ->with('printerStation')
    ->get(['id', 'error_type', 'error_message', 'created_at']);
```

### Retry Failed Jobs
```bash
php artisan tinker
```
```php
$jobs = \Modules\POS\Models\PrintJob::autoRetryable()->get();
foreach ($jobs as $job) {
    $job->markAsQueued();
    \Modules\POS\Jobs\PrintKitchenOrderJob::dispatch($job);
}
```

### Check Queue Status
```bash
php artisan queue:work --once --queue=kitchen-printing
```

---

## 🔍 Error Types & Actions

| Error Type | Auto-Retry? | Action |
|------------|-------------|--------|
| `AGENT_DOWN` | ✅ Yes | Check if agent is running |
| `TIMEOUT` | ✅ Yes | Check network/agent performance |
| `PRINTER_NOT_FOUND` | ❌ No | Verify printer name in settings |
| `INVALID_PAYLOAD` | ❌ No | Check print content format |
| `UNKNOWN` | ✅ Yes | Check logs for details |

---

## 📊 Monitoring Dashboard

**URL:** `/print-jobs/monitoring`

**Shows:**
- Success rate (last hour, 24h, week)
- Failure rate
- Dispatch latency
- Queue backlog
- Per-station failures
- Duplicate prints
- Error distribution
- Agent health
- Active alerts

---

## 🔧 Common Issues & Solutions

### Issue: Agent Down
```bash
# Check agent
curl http://127.0.0.1:5000/health

# Restart agent (Windows)
cd print-agent
start-admin.bat
```

### Issue: High Failure Rate
```php
// Check error distribution
$kpis = app(\Modules\POS\Services\PrintJobMonitoringService::class)->getKPIs(1);
print_r($kpis['error_type_distribution']);

// Check per-station failures
print_r($kpis['per_station_failures']);
```

### Issue: High Latency
```bash
# Check queue workers
ps aux | grep "queue:work"

# Add more workers
php artisan queue:work --queue=kitchen-printing --tries=3 &
```

### Issue: Duplicate Prints
```sql
-- Check for duplicates
SELECT transaction_id, printer_station_id, payload_hash, COUNT(*) as count
FROM print_jobs
GROUP BY transaction_id, printer_station_id, payload_hash
HAVING count > 1;
```

---

## 📝 State Machine

```
queued → sending → printed ✅
   ↓         ↓
   └─────→ failed ❌
```

**States:**
- `queued` - Waiting in queue
- `sending` - Being sent to agent
- `printed` - Successfully printed
- `failed` - Failed (check error_type)

---

## 🔐 Permissions

| Action | Permission |
|--------|-----------|
| View print jobs | `view Print Jobs` |
| View monitoring | `view Print Jobs` |
| Manual retry | `retry Print Jobs` |
| Batch retry | `retry Print Jobs` |

---

## 📁 Key Files

| File | Purpose |
|------|---------|
| `PrintJob.php` | Model with state machine |
| `PrintKitchenOrderJob.php` | Job with error classification |
| `PrintJobIdempotencyService.php` | Prevent duplicates |
| `PrintJobMonitoringService.php` | KPIs & health checks |
| `PrintJobController.php` | Admin UI |

---

## 🔄 Retry Logic

### Auto-Retry (Exponential Backoff)
- 1st attempt: immediate
- 2nd attempt: +5s
- 3rd attempt: +15s
- 4th attempt: +45s

### Manual Retry
```php
// Via UI: /print-jobs/{id}/retry
// Via code:
$retryJob = app(\Modules\POS\Services\PrintJobIdempotencyService::class)
    ->createManualRetryJob($originalJob, auth()->id());
\Modules\POS\Jobs\PrintKitchenOrderJob::dispatch($retryJob);
```

---

## 📈 Monitoring Queries

### Success Rate (Last 24h)
```php
$total = \Modules\POS\Models\PrintJob::recent(24)->count();
$success = \Modules\POS\Models\PrintJob::recent(24)->byStatus('printed')->count();
$rate = ($success / $total) * 100;
```

### Failed Jobs by Error Type
```php
\Modules\POS\Models\PrintJob::recent(24)
    ->byStatus('failed')
    ->select('error_type', DB::raw('COUNT(*) as count'))
    ->groupBy('error_type')
    ->get();
```

### Queue Backlog
```php
\Modules\POS\Models\PrintJob::whereIn('status', ['queued', 'sending'])->count();
```

### Average Latency
```php
\Modules\POS\Models\PrintJob::recent(24)
    ->whereNotNull('sent_at')
    ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, sent_at)) as avg')
    ->value('avg');
```

---

## 🚨 Alerts

### Critical Alerts
- Agent down
- Duplicate prints detected
- Success rate < 95%

### High Priority Alerts
- Success rate < 99.5%
- Queue backlog > 20

### Medium Priority Alerts
- Dispatch latency > 1s
- Queue backlog > 10

---

## 🔗 Related Documentation

- `RELIABILITY_UPGRADE_SUMMARY.md` - Full summary
- `RELIABILITY_UPGRADE_ROLLOUT_PLAN.md` - Deployment guide
- `MONITORING_API.md` - API documentation
- `KITCHEN_PRINTER_SETUP.md` - Setup guide
- `print-agent/HEALTH_ENDPOINT_GUIDE.md` - Agent health endpoint

---

## 💡 Tips

1. **Check dashboard daily** - `/print-jobs/monitoring`
2. **Set up scheduled tasks** - Health checks every minute
3. **Monitor logs** - `storage/logs/laravel.log`
4. **Test agent regularly** - `curl http://127.0.0.1:5000/health`
5. **Archive old jobs** - Keep last 30 days only
6. **Document issues** - Keep a troubleshooting log

---

## 📞 Emergency Contacts

### Agent Down
1. Check: `http://127.0.0.1:5000/health`
2. Restart: `print-agent/start-admin.bat`
3. Check logs: Windows Event Viewer

### Database Issues
1. Check connections: `php artisan tinker` → `DB::connection()->getPdo()`
2. Check migrations: `php artisan migrate:status`
3. Check indexes: `SHOW INDEX FROM print_jobs`

### Queue Issues
1. Check workers: `ps aux | grep queue:work`
2. Restart workers: `php artisan queue:restart`
3. Check failed jobs: `php artisan queue:failed`

---

## ✅ Health Check Checklist

- [ ] Agent responding: `curl http://127.0.0.1:5000/health`
- [ ] Success rate >= 99.5%
- [ ] No duplicate prints
- [ ] Dispatch latency < 1s
- [ ] Queue backlog < 10
- [ ] No critical alerts
- [ ] Queue workers running
- [ ] Logs clean (no errors)

---

## 🎓 Learning Resources

### Understanding Idempotency
```php
// Same transaction + station + content = Same idempotency_key
$key = PrintJob::generateIdempotencyKey(
    $transactionId,
    $stationId,
    $payloadHash,
    $sequence
);
// Unique constraint prevents duplicates
```

### Understanding State Machine
```php
$job->markAsSending();   // queued → sending
$job->markAsPrinted();   // sending → printed
$job->markAsFailed(...); // sending → failed
$job->markAsQueued();    // failed → queued (retry)
```

### Understanding Error Classification
```php
// Temporary errors (auto-retry)
AGENT_DOWN, TIMEOUT, UNKNOWN

// Logical errors (no auto-retry)
PRINTER_NOT_FOUND, INVALID_PAYLOAD
```

---

**Last Updated:** 2026-02-25
**Version:** 1.0.0
