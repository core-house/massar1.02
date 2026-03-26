<?php

declare(strict_types=1);

namespace Modules\Agent\Services\Domains;

use Modules\Agent\DTOs\ClassificationResult;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\TypedFilter;
use Modules\Agent\Services\DomainQueryService;

/**
 * CRM Domain Query Service
 *
 * Handles query plan creation for CRM (Customer Relationship Management) domain questions.
 * Supports queries about customers, contacts, leads, opportunities, etc.
 */
class CRMQueryService extends DomainQueryService
{
    /**
     * Create a query plan for CRM domain questions.
     *
     * @param  ClassificationResult  $classification  The classification result
     * @param  string  $questionText  The original question text
     * @return DomainQueryPlan The generated query plan
     */
    public function createQueryPlan(ClassificationResult $classification, string $questionText): DomainQueryPlan
    {
        $table = 'customers';
        $allowedColumns = $this->configRegistry->getAllowedColumns('crm', $table);
        $operation = $this->determineOperation($questionText);
        $filters = $this->extractFiltersFromQuestion($questionText, $allowedColumns);

        return new DomainQueryPlan(
            domain: 'crm',
            table: $table,
            operation: $operation,
            allowedColumns: $allowedColumns,
            typedFilters: $filters,
            sort: ['created_at' => 'desc'],
            limit: 100,
        );
    }

    /**
     * Determine the operation type from question text (CRM-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @return string The operation type: 'count', 'aggregate', or 'select'
     */
    protected function determineOperation(string $questionText): string
    {
        // Check for count operations (CRM-specific patterns)
        if (preg_match('/كم عدد العملاء|عدد العملاء|كم عميل|احصاء العملاء|كم زبون/u', $questionText)) {
            return 'count';
        }

        // Check for aggregate operations (CRM-specific patterns)
        if (preg_match('/مجموع العملاء|إجمالي المبيعات|متوسط قيمة العميل|معدل الشراء/u', $questionText)) {
            return 'aggregate';
        }

        // Use parent implementation for general patterns
        return parent::determineOperation($questionText);
    }

    /**
     * Extract filters from question text (CRM-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @param  array  $allowedColumns  The columns allowed for filtering
     * @return array Array of TypedFilter objects
     */
    protected function extractFiltersFromQuestion(string $questionText, array $allowedColumns): array
    {
        // Start with parent filters
        $filters = parent::extractFiltersFromQuestion($questionText, $allowedColumns);

        // Add CRM-specific filters

        // Extract customer type filters (للبحث عن نوع العميل)
        if (preg_match('/عميل جديد|عملاء جدد|جديد/u', $questionText) && in_array('customer_type', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'customer_type',
                operator: '=',
                value: 'new',
                type: 'exact'
            );
        } elseif (preg_match('/عميل دائم|عملاء دائمين|دائم|مستمر/u', $questionText) && in_array('customer_type', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'customer_type',
                operator: '=',
                value: 'regular',
                type: 'exact'
            );
        } elseif (preg_match('/عميل VIP|عملاء VIP|مميز|مميزين/u', $questionText) && in_array('customer_type', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'customer_type',
                operator: '=',
                value: 'vip',
                type: 'exact'
            );
        }

        // Extract email filters (للبحث عن بريد إلكتروني)
        if (preg_match('/بريد|البريد|إيميل|الإيميل|email/ui', $questionText) && in_array('email', $allowedColumns)) {
            if (preg_match('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/u', $questionText, $matches)) {
                $email = $matches[1];
                $filters[] = new TypedFilter(
                    column: 'email',
                    operator: '=',
                    value: $email,
                    type: 'exact'
                );
            }
        }

        // Extract phone filters (للبحث عن رقم هاتف)
        if (preg_match('/هاتف|الهاتف|جوال|الجوال|رقم/u', $questionText) && in_array('phone', $allowedColumns)) {
            if (preg_match('/(\d{10,})/u', $questionText, $matches)) {
                $phone = $matches[1];
                $filters[] = new TypedFilter(
                    column: 'phone',
                    operator: 'like',
                    value: $phone,
                    type: 'like'
                );
            }
        }

        // Extract city/location filters (للبحث عن مدينة)
        if (preg_match('/مدينة|المدينة|في\s+([^\s]+)|من\s+([^\s]+)/u', $questionText)) {
            if (in_array('city', $allowedColumns)) {
                if (preg_match('/مدينة\s+([^\s]+)|في\s+([^\s]+)|من\s+([^\s]+)/u', $questionText, $matches)) {
                    $city = trim($matches[1] ?? $matches[2] ?? $matches[3] ?? '');
                    if ($city && ! in_array($city, ['قسم', 'فرع', 'شركة'])) {
                        $filters[] = new TypedFilter(
                            column: 'city',
                            operator: 'like',
                            value: $city,
                            type: 'like'
                        );
                    }
                }
            }
        }

        // Extract lead source filters (للبحث عن مصدر العميل)
        if (preg_match('/مصدر|المصدر|جاء من|أتى من/u', $questionText) && in_array('lead_source', $allowedColumns)) {
            if (preg_match('/موقع|الموقع|website/ui', $questionText)) {
                $filters[] = new TypedFilter(
                    column: 'lead_source',
                    operator: '=',
                    value: 'website',
                    type: 'exact'
                );
            } elseif (preg_match('/إعلان|إعلانات|ads/ui', $questionText)) {
                $filters[] = new TypedFilter(
                    column: 'lead_source',
                    operator: '=',
                    value: 'ads',
                    type: 'exact'
                );
            } elseif (preg_match('/توصية|ترشيح|referral/ui', $questionText)) {
                $filters[] = new TypedFilter(
                    column: 'lead_source',
                    operator: '=',
                    value: 'referral',
                    type: 'exact'
                );
            }
        }

        // Extract status filters (للبحث عن حالة العميل)
        if (preg_match('/نشط|فعال|active/ui', $questionText) && in_array('status', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'status',
                operator: '=',
                value: 'active',
                type: 'exact'
            );
        } elseif (preg_match('/غير نشط|معطل|inactive/ui', $questionText) && in_array('status', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'status',
                operator: '=',
                value: 'inactive',
                type: 'exact'
            );
        }

        return $filters;
    }
}
