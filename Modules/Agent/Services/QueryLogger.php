<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\Models\AgentQueryLog;

class QueryLogger
{
    /**
     * تسجيل metadata الاستعلام بطريقة آمنة للخصوصية
     */
    public function log(DomainQueryPlan $plan, int $resultCount, int $executionTime, ?int $questionId = null): void
    {
        // استخراج الـ scopes المطبقة فقط (tenant_id, company_id, branch_id, department_id)
        $scopesApplied = array_filter(
            $plan->typedFilters,
            fn ($filter) => in_array($filter->column, ['tenant_id', 'company_id', 'branch_id', 'department_id'], true)
        );

        AgentQueryLog::create([
            'question_id' => $questionId,
            'user_id' => auth()->id(),
            'domain' => $plan->domain,
            'table_name' => $plan->table,
            'operation_type' => $plan->operation,
            'column_count' => count($plan->allowedColumns),
            'filter_count' => count($plan->typedFilters),
            'result_count' => $resultCount,
            'execution_time_ms' => $executionTime,
            'scopes_applied' => array_map(fn ($f) => $f->column, $scopesApplied),
        ]);
    }

    /**
     * تسجيل خطأ في تنفيذ الاستعلام
     *
     * ملاحظة: لا يتم تسجيل raw SQL أو bindings أو PII
     */
    public function logError(DomainQueryPlan $plan, Exception $e): void
    {
        Log::error('Agent query execution failed', [
            'domain' => $plan->domain,
            'table' => $plan->table,
            'operation' => $plan->operation,
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            // NO raw SQL, NO bindings, NO PII
        ]);
    }
}
