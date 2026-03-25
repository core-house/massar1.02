<?php

declare(strict_types=1);

namespace Modules\Agent\Services\Domains;

use Modules\Agent\DTOs\ClassificationResult;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\Services\DomainQueryService;

/**
 * HR Domain Query Service
 *
 * Handles query plan creation for Human Resources domain questions.
 * Supports queries about employees, departments, positions, etc.
 */
class HRQueryService extends DomainQueryService
{
    /**
     * Create a query plan for HR domain questions.
     *
     * @param  ClassificationResult  $classification  The classification result
     * @param  string  $questionText  The original question text
     * @return DomainQueryPlan The generated query plan
     */
    public function createQueryPlan(ClassificationResult $classification, string $questionText): DomainQueryPlan
    {
        $table = 'employees';
        $allowedColumns = $this->configRegistry->getAllowedColumns('hr', $table);
        $operation = $this->determineOperation($questionText);
        $filters = $this->extractFiltersFromQuestion($questionText, $allowedColumns);

        return new DomainQueryPlan(
            domain: 'hr',
            table: $table,
            operation: $operation,
            allowedColumns: $allowedColumns,
            typedFilters: $filters,
            sort: ['created_at' => 'desc'],
            limit: 100,
        );
    }

    /**
     * Determine the operation type from question text (HR-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @return string The operation type: 'count', 'aggregate', or 'select'
     */
    protected function determineOperation(string $questionText): string
    {
        // Check for count operations (HR-specific patterns)
        if (preg_match('/كم عدد الموظفين|عدد الموظفين|كم موظف|احصاء الموظفين/u', $questionText)) {
            return 'count';
        }

        // Check for aggregate operations (HR-specific patterns)
        if (preg_match('/متوسط الرواتب|معدل الرواتب|مجموع الرواتب|إجمالي الموظفين/u', $questionText)) {
            return 'aggregate';
        }

        // Use parent implementation for general patterns
        return parent::determineOperation($questionText);
    }

    /**
     * Extract filters from question text (HR-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @param  array  $allowedColumns  The columns allowed for filtering
     * @return array Array of TypedFilter objects
     */
    protected function extractFiltersFromQuestion(string $questionText, array $allowedColumns): array
    {
        // Start with parent filters
        $filters = parent::extractFiltersFromQuestion($questionText, $allowedColumns);

        // Add HR-specific filters

        // Extract position filters (للبحث عن مناصب)
        if (preg_match('/منصب|المنصب|وظيفة|الوظيفة|مسمى/u', $questionText, $matches)) {
            if (in_array('position', $allowedColumns)) {
                // Try to extract position name
                if (preg_match('/منصب\s+([^\s]+)|وظيفة\s+([^\s]+)/u', $questionText, $posMatches)) {
                    $position = trim($posMatches[1] ?? $posMatches[2] ?? '');
                    if ($position) {
                        $filters[] = new \Modules\Agent\DTOs\TypedFilter(
                            column: 'position',
                            operator: 'like',
                            value: $position,
                            type: 'like'
                        );
                    }
                }
            }
        }

        // Extract hire date filters (للبحث عن تاريخ التوظيف)
        if (preg_match('/تم توظيفهم|تم تعيينهم|تعيين|توظيف/u', $questionText)) {
            if (in_array('hire_date', $allowedColumns)) {
                // Check for specific year
                if (preg_match('/في\s+(\d{4})|عام\s+(\d{4})/u', $questionText, $yearMatches)) {
                    $year = $yearMatches[1] ?? $yearMatches[2];
                    $filters[] = new \Modules\Agent\DTOs\TypedFilter(
                        column: 'hire_date',
                        operator: '>=',
                        value: "{$year}-01-01",
                        type: 'exact'
                    );
                    $filters[] = new \Modules\Agent\DTOs\TypedFilter(
                        column: 'hire_date',
                        operator: '<=',
                        value: "{$year}-12-31",
                        type: 'exact'
                    );
                }
            }
        }

        return $filters;
    }
}
