<?php

declare(strict_types=1);

namespace Modules\Agent\Services\Domains;

use Modules\Agent\DTOs\ClassificationResult;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\TypedFilter;
use Modules\Agent\Services\DomainQueryService;

/**
 * Invoice Domain Query Service
 *
 * Handles query plan creation for Invoices domain questions.
 * Supports queries about invoices, clients, payments, totals, etc.
 */
class InvoiceQueryService extends DomainQueryService
{
    /**
     * Create a query plan for Invoice domain questions.
     *
     * @param  ClassificationResult  $classification  The classification result
     * @param  string  $questionText  The original question text
     * @return DomainQueryPlan The generated query plan
     */
    public function createQueryPlan(ClassificationResult $classification, string $questionText): DomainQueryPlan
    {
        $table = 'invoices';
        $allowedColumns = $this->configRegistry->getAllowedColumns('invoices', $table);
        $operation = $this->determineOperation($questionText);
        $filters = $this->extractFiltersFromQuestion($questionText, $allowedColumns);

        return new DomainQueryPlan(
            domain: 'invoices',
            table: $table,
            operation: $operation,
            allowedColumns: $allowedColumns,
            typedFilters: $filters,
            sort: ['date' => 'desc'],
            limit: 100,
        );
    }

    /**
     * Determine the operation type from question text (Invoice-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @return string The operation type: 'count', 'aggregate', or 'select'
     */
    protected function determineOperation(string $questionText): string
    {
        // Check for count operations (Invoice-specific patterns)
        if (preg_match('/كم عدد الفواتير|عدد الفواتير|كم فاتورة|احصاء الفواتير/u', $questionText)) {
            return 'count';
        }

        // Check for aggregate operations (Invoice-specific patterns)
        if (preg_match('/مجموع الفواتير|إجمالي الفواتير|متوسط قيمة|معدل الفواتير|أعلى فاتورة|أقل فاتورة/u', $questionText)) {
            return 'aggregate';
        }

        // Use parent implementation for general patterns
        return parent::determineOperation($questionText);
    }

    /**
     * Extract filters from question text (Invoice-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @param  array  $allowedColumns  The columns allowed for filtering
     * @return array Array of TypedFilter objects
     */
    protected function extractFiltersFromQuestion(string $questionText, array $allowedColumns): array
    {
        // Start with parent filters
        $filters = parent::extractFiltersFromQuestion($questionText, $allowedColumns);

        // Add Invoice-specific filters

        // Extract client name filters (للبحث عن عملاء)
        if (preg_match('/عميل|العميل|للعميل|من العميل/u', $questionText)) {
            if (in_array('client_name', $allowedColumns)) {
                // Try to extract client name
                if (preg_match('/عميل\s+["\']?([^"\']+)["\']?|للعميل\s+["\']?([^"\']+)["\']?/u', $questionText, $matches)) {
                    $clientName = trim($matches[1] ?? $matches[2] ?? '');
                    if ($clientName) {
                        $filters[] = new TypedFilter(
                            column: 'client_name',
                            operator: 'like',
                            value: $clientName,
                            type: 'like'
                        );
                    }
                }
            }
        }

        // Extract invoice number filters (للبحث عن رقم فاتورة)
        if (preg_match('/فاتورة رقم|رقم الفاتورة|الفاتورة رقم/u', $questionText)) {
            if (in_array('invoice_number', $allowedColumns)) {
                // Try to extract invoice number
                if (preg_match('/رقم\s+(\d+)|#(\d+)/u', $questionText, $matches)) {
                    $invoiceNumber = $matches[1] ?? $matches[2];
                    $filters[] = new TypedFilter(
                        column: 'invoice_number',
                        operator: '=',
                        value: $invoiceNumber,
                        type: 'exact'
                    );
                }
            }
        }

        // Extract status filters (للبحث عن حالة الفاتورة)
        if (preg_match('/مدفوعة|مدفوع|تم الدفع/u', $questionText) && in_array('status', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'status',
                operator: '=',
                value: 'paid',
                type: 'exact'
            );
        } elseif (preg_match('/غير مدفوعة|غير مدفوع|معلقة|معلق/u', $questionText) && in_array('status', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'status',
                operator: '=',
                value: 'pending',
                type: 'exact'
            );
        } elseif (preg_match('/ملغاة|ملغي|ملغى/u', $questionText) && in_array('status', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'status',
                operator: '=',
                value: 'cancelled',
                type: 'exact'
            );
        }

        // Extract total amount filters (للبحث عن مبالغ)
        if (preg_match('/أكثر من|أكبر من|يزيد عن/u', $questionText) && in_array('total', $allowedColumns)) {
            if (preg_match('/(\d+(?:\.\d+)?)/u', $questionText, $matches)) {
                $amount = (float) $matches[1];
                $filters[] = new TypedFilter(
                    column: 'total',
                    operator: '>',
                    value: $amount,
                    type: 'exact'
                );
            }
        } elseif (preg_match('/أقل من|أصغر من/u', $questionText) && in_array('total', $allowedColumns)) {
            if (preg_match('/(\d+(?:\.\d+)?)/u', $questionText, $matches)) {
                $amount = (float) $matches[1];
                $filters[] = new TypedFilter(
                    column: 'total',
                    operator: '<',
                    value: $amount,
                    type: 'exact'
                );
            }
        }

        return $filters;
    }
}
