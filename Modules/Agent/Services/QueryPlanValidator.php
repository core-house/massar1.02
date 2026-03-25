<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\ValidationResult;

class QueryPlanValidator
{
    public function __construct(
        private DomainConfigRegistry $configRegistry
    ) {}

    /**
     * التحقق من صحة DomainQueryPlan قبل التنفيذ
     */
    public function validate(DomainQueryPlan $plan): ValidationResult
    {
        $errors = [];
        $forbiddenColumnsDetected = [];

        // التحقق من الأعمدة المسموحة
        if (! $this->validateColumns($plan)) {
            $errors[] = 'بعض الأعمدة المطلوبة غير مسموحة في هذا الـ domain';
        }

        // التحقق من الـ filters
        if (! $this->validateFilters($plan)) {
            $errors[] = 'بعض الـ filters تستهدف أعمدة غير مسموحة';
        }

        // التحقق من searchable columns للـ LIKE filters
        if (! $this->validateSearchableColumns($plan)) {
            $errors[] = 'بعض الـ LIKE filters تستهدف أعمدة غير قابلة للبحث';
        }

        // التحقق من عدم وجود أعمدة محظورة
        $forbiddenColumnsDetected = $this->checkForbiddenColumns($plan);
        if (! empty($forbiddenColumnsDetected)) {
            $errors[] = 'تم اكتشاف أعمدة محظورة في الخطة: '.implode(', ', $forbiddenColumnsDetected);
        }

        // التحقق من limit
        if ($plan->limit > 100) {
            $errors[] = 'الحد الأقصى للنتائج يجب ألا يتجاوز 100';
        }

        return new ValidationResult(
            isValid: empty($errors),
            errors: $errors,
            forbiddenColumnsDetected: $forbiddenColumnsDetected,
        );
    }

    /**
     * التحقق من أن جميع الأعمدة في allowedColumns مسموحة
     */
    private function validateColumns(DomainQueryPlan $plan): bool
    {
        $allowedColumns = $this->configRegistry->getAllowedColumns($plan->domain, $plan->table);

        foreach ($plan->allowedColumns as $column) {
            if (! in_array($column, $allowedColumns, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * التحقق من أن جميع الـ filters تستهدف أعمدة مسموحة
     */
    private function validateFilters(DomainQueryPlan $plan): bool
    {
        $allowedColumns = $this->configRegistry->getAllowedColumns($plan->domain, $plan->table);

        foreach ($plan->typedFilters as $filter) {
            if (! in_array($filter->column, $allowedColumns, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * التحقق من أن LIKE filters تستهدف فقط searchable columns
     */
    private function validateSearchableColumns(DomainQueryPlan $plan): bool
    {
        $searchableColumns = $this->configRegistry->getSearchableColumns($plan->domain, $plan->table);

        foreach ($plan->typedFilters as $filter) {
            if ($filter->type === 'like' && ! in_array($filter->column, $searchableColumns, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * الكشف عن أعمدة محظورة في الخطة
     */
    private function checkForbiddenColumns(DomainQueryPlan $plan): array
    {
        $forbiddenColumns = $this->configRegistry->getForbiddenColumns($plan->domain, $plan->table);
        $detectedForbidden = [];

        // التحقق من allowedColumns
        foreach ($plan->allowedColumns as $column) {
            if (in_array($column, $forbiddenColumns, true)) {
                $detectedForbidden[] = $column;
            }
        }

        // التحقق من filters
        foreach ($plan->typedFilters as $filter) {
            if (in_array($filter->column, $forbiddenColumns, true) && ! in_array($filter->column, $detectedForbidden, true)) {
                $detectedForbidden[] = $filter->column;
            }
        }

        return $detectedForbidden;
    }
}
