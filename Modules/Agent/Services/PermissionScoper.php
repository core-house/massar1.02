<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use App\Models\User;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\TypedFilter;

class PermissionScoper
{
    public function __construct(
        private DomainConfigRegistry $configRegistry
    ) {}

    /**
     * تطبيق row-level authorization على query plan
     */
    public function applyScopes(DomainQueryPlan $plan, User $user): DomainQueryPlan
    {
        $requiredScopes = $this->configRegistry->getRequiredScopes($plan->domain, $plan->table);

        $additionalFilters = [];

        foreach ($requiredScopes as $scope) {
            $filter = $this->createScopeFilter($scope, $user);
            if ($filter) {
                $additionalFilters[] = $filter;
            }
        }

        // إنشاء خطة جديدة مع filters إضافية
        return new DomainQueryPlan(
            domain: $plan->domain,
            table: $plan->table,
            operation: $plan->operation,
            allowedColumns: $plan->allowedColumns,
            typedFilters: array_merge($plan->typedFilters, $additionalFilters),
            sort: $plan->sort,
            limit: $plan->limit,
        );
    }

    /**
     * إنشاء filter للـ scope بناءً على بيانات المستخدم
     */
    private function createScopeFilter(string $scope, User $user): ?TypedFilter
    {
        return match ($scope) {
            'tenant' => $user->tenant_id ? new TypedFilter('tenant_id', '=', $user->tenant_id, 'exact') : null,
            'company' => $user->company_id ? new TypedFilter('company_id', '=', $user->company_id, 'exact') : null,
            'branch' => $user->branch_id ? new TypedFilter('branch_id', '=', $user->branch_id, 'exact') : null,
            'department' => $user->department_id ? new TypedFilter('department_id', '=', $user->department_id, 'exact') : null,
            default => null,
        };
    }
}
