<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use App\Models\User;
use Modules\Agent\DTOs\ClassificationResult;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\QueryResult;
use Modules\Agent\DTOs\TypedFilter;
use Modules\Agent\Exceptions\InvalidQueryPlanException;

/**
 * Abstract base class for domain-specific query services.
 *
 * This class provides the foundation for creating and executing query plans
 * for specific domains (HR, Invoices, Inventory, CRM).
 */
abstract class DomainQueryService
{
    public function __construct(
        protected DomainConfigRegistry $configRegistry,
        protected QueryPlanValidator $validator,
        protected PermissionScoper $scoper,
        protected QueryExecutor $executor,
    ) {}

    /**
     * Create a domain-specific query plan from classification result and question text.
     *
     * This method must be implemented by each domain service to define
     * how questions are translated into query plans for that domain.
     *
     * @param  ClassificationResult  $classification  The classification result
     * @param  string  $questionText  The original question text
     * @return DomainQueryPlan The generated query plan
     */
    abstract public function createQueryPlan(ClassificationResult $classification, string $questionText): DomainQueryPlan;

    /**
     * Execute a query plan with validation and row-level authorization.
     *
     * @param  DomainQueryPlan  $plan  The query plan to execute
     * @param  User  $user  The authenticated user
     * @return QueryResult The query execution result
     *
     * @throws InvalidQueryPlanException If the plan is invalid
     */
    public function execute(DomainQueryPlan $plan, User $user): QueryResult
    {
        // Validate plan
        $validation = $this->validator->validate($plan);
        if (! $validation->isValid) {
            throw new InvalidQueryPlanException(
                __('agent.errors.invalid_query_plan', [
                    'errors' => implode(', ', $validation->errors),
                ])
            );
        }

        // Apply row-level authorization
        $scopedPlan = $this->scoper->applyScopes($plan, $user);

        // Execute query
        return $this->executor->execute($scopedPlan);
    }

    /**
     * Extract filters from question text based on allowed columns.
     *
     * @param  string  $questionText  The question text to analyze
     * @param  array  $allowedColumns  The columns allowed for filtering
     * @return array Array of TypedFilter objects
     */
    protected function extractFiltersFromQuestion(string $questionText, array $allowedColumns): array
    {
        $filters = [];

        // Extract name filters (للبحث عن أسماء)
        if (preg_match('/اسم[ه]?\s+["\']?([^"\']+)["\']?/u', $questionText, $matches)) {
            if (in_array('name', $allowedColumns)) {
                $filters[] = new TypedFilter(
                    column: 'name',
                    operator: 'like',
                    value: trim($matches[1]),
                    type: 'like'
                );
            }
        }

        // Extract status filters (للبحث عن حالات)
        if (preg_match('/حالة|حاله|الحالة|الحاله/u', $questionText)) {
            if (preg_match('/نشط|فعال|مفعل/u', $questionText) && in_array('status', $allowedColumns)) {
                $filters[] = new TypedFilter(
                    column: 'status',
                    operator: '=',
                    value: 'active',
                    type: 'exact'
                );
            } elseif (preg_match('/غير نشط|معطل|موقوف/u', $questionText) && in_array('status', $allowedColumns)) {
                $filters[] = new TypedFilter(
                    column: 'status',
                    operator: '=',
                    value: 'inactive',
                    type: 'exact'
                );
            }
        }

        // Extract department filters (للبحث عن أقسام)
        if (preg_match('/قسم|القسم|في\s+([^\s]+)/u', $questionText, $matches)) {
            if (in_array('department', $allowedColumns) || in_array('department_id', $allowedColumns)) {
                $filters[] = new TypedFilter(
                    column: in_array('department', $allowedColumns) ? 'department' : 'department_id',
                    operator: 'like',
                    value: trim($matches[1]),
                    type: 'like'
                );
            }
        }

        // Extract date range filters (للبحث عن تواريخ)
        if (preg_match('/خلال\s+(\d+)\s+(يوم|أيام|شهر|أشهر|سنة|سنوات)/u', $questionText, $matches)) {
            $value = (int) $matches[1];
            $unit = $matches[2];

            $dateColumn = null;
            if (in_array('created_at', $allowedColumns)) {
                $dateColumn = 'created_at';
            } elseif (in_array('date', $allowedColumns)) {
                $dateColumn = 'date';
            } elseif (in_array('hire_date', $allowedColumns)) {
                $dateColumn = 'hire_date';
            }

            if ($dateColumn) {
                $startDate = match ($unit) {
                    'يوم', 'أيام' => now()->subDays($value),
                    'شهر', 'أشهر' => now()->subMonths($value),
                    'سنة', 'سنوات' => now()->subYears($value),
                    default => now()->subDays($value),
                };

                $filters[] = new TypedFilter(
                    column: $dateColumn,
                    operator: '>=',
                    value: $startDate->format('Y-m-d'),
                    type: 'exact'
                );
            }
        }

        return $filters;
    }

    /**
     * Determine the operation type from question text.
     *
     * @param  string  $questionText  The question text to analyze
     * @return string The operation type: 'count', 'aggregate', or 'select'
     */
    protected function determineOperation(string $questionText): string
    {
        // Check for count operations
        if (preg_match('/كم عدد|عدد|كم|احصاء|إحصاء/u', $questionText)) {
            return 'count';
        }

        // Check for aggregate operations
        if (preg_match('/متوسط|معدل|مجموع|إجمالي|اجمالي|أعلى|أقل|أكبر|أصغر/u', $questionText)) {
            return 'aggregate';
        }

        // Default to select
        return 'select';
    }
}
