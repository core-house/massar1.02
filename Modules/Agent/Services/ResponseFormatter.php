<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use Illuminate\Support\Collection;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\QueryResult;

/**
 * Response Formatter Service
 *
 * Formats query results into formal Arabic responses for display to users.
 * Ensures forbidden columns are excluded and output is properly escaped.
 */
class ResponseFormatter
{
    public function __construct(
        private DomainConfigRegistry $configRegistry
    ) {}

    /**
     * Format a query result into a formal Arabic response.
     *
     * @param  QueryResult  $result  The query execution result
     * @param  DomainQueryPlan  $plan  The query plan that was executed
     * @param  string  $questionText  The original question text
     * @return string The formatted response in Arabic
     */
    public function format(QueryResult $result, DomainQueryPlan $plan, string $questionText): string
    {
        // Handle different operation types
        return match ($plan->operation) {
            'count' => $this->formatCount($result->count),
            'aggregate' => $this->formatAggregate($result->data, $questionText),
            'select' => $this->formatList($result->data, $plan->allowedColumns, $plan->domain, $plan->table),
            default => $this->formatNoResults(),
        };
    }

    /**
     * Format a list of records.
     *
     * @param  Collection  $items  The collection of records
     * @param  array  $allowedColumns  The columns allowed for display
     * @param  string  $domain  The domain name
     * @param  string  $table  The table name
     * @return string The formatted list in Arabic
     */
    private function formatList(Collection $items, array $allowedColumns, string $domain, string $table): string
    {
        if ($items->isEmpty()) {
            return $this->formatNoResults();
        }

        $count = $items->count();
        $response = "تم العثور على {$count} نتيجة:\n\n";

        foreach ($items as $index => $item) {
            $response .= ($index + 1).'. ';

            // Filter to only allowed columns and exclude forbidden ones
            $filteredData = $this->filterForbiddenColumns(
                $item->toArray(),
                $allowedColumns,
                $domain,
                $table
            );

            $response .= $this->formatItemDetails($filteredData);
            $response .= "\n";
        }

        return $response;
    }

    /**
     * Format item details into a readable string.
     *
     * @param  array  $itemData  The filtered item data
     * @return string The formatted item details
     */
    private function formatItemDetails(array $itemData): string
    {
        $details = [];

        foreach ($itemData as $key => $value) {
            // Skip null values and internal fields
            if ($value === null || in_array($key, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            // Format the key name (convert snake_case to readable Arabic)
            $label = $this->formatColumnLabel($key);

            // Format the value
            $formattedValue = $this->formatValue($value);

            $details[] = "{$label}: {$formattedValue}";
        }

        return implode(' | ', $details);
    }

    /**
     * Format a column label for display.
     *
     * @param  string  $column  The column name
     * @return string The formatted label
     */
    private function formatColumnLabel(string $column): string
    {
        // Map common column names to Arabic labels
        $labels = [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'phone' => 'الهاتف',
            'status' => 'الحالة',
            'department' => 'القسم',
            'position' => 'المنصب',
            'hire_date' => 'تاريخ التوظيف',
            'invoice_number' => 'رقم الفاتورة',
            'client_name' => 'اسم العميل',
            'total' => 'المجموع',
            'date' => 'التاريخ',
            'category' => 'الفئة',
            'price' => 'السعر',
            'stock_quantity' => 'الكمية',
            'sku' => 'رمز المنتج',
            'customer_type' => 'نوع العميل',
            'city' => 'المدينة',
            'lead_source' => 'المصدر',
        ];

        return $labels[$column] ?? ucfirst(str_replace('_', ' ', $column));
    }

    /**
     * Format a value for display.
     *
     * @param  mixed  $value  The value to format
     * @return string The formatted value
     */
    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        if (is_numeric($value)) {
            // Format numbers with Arabic separators
            return number_format((float) $value, 2, '.', ',');
        }

        if ($value instanceof \DateTime || $value instanceof \DateTimeInterface) {
            // Format dates in Arabic style
            return $value->format('Y-m-d');
        }

        // Check if value is a date string
        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            try {
                $date = new \DateTime($value);

                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                // Not a valid date, return as is
            }
        }

        // Escape HTML to prevent XSS
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format a count result.
     *
     * @param  int  $count  The count value
     * @return string The formatted count in Arabic
     */
    private function formatCount(int $count): string
    {
        if ($count === 0) {
            return $this->formatNoResults();
        }

        return "العدد الإجمالي: {$count}";
    }

    /**
     * Format an aggregate result.
     *
     * @param  mixed  $data  The aggregate data
     * @param  string  $questionText  The original question text
     * @return string The formatted aggregate in Arabic
     */
    private function formatAggregate(mixed $data, string $questionText): string
    {
        if (is_array($data)) {
            $response = "نتائج التجميع:\n\n";

            foreach ($data as $key => $value) {
                $label = $this->formatAggregateLabel($key, $questionText);
                $formattedValue = $this->formatValue($value);
                $response .= "{$label}: {$formattedValue}\n";
            }

            return $response;
        }

        // Single aggregate value
        $label = $this->detectAggregateType($questionText);
        $formattedValue = $this->formatValue($data);

        return "{$label}: {$formattedValue}";
    }

    /**
     * Format an aggregate label based on the key and question.
     *
     * @param  string  $key  The aggregate key
     * @param  string  $questionText  The question text
     * @return string The formatted label
     */
    private function formatAggregateLabel(string $key, string $questionText): string
    {
        $labels = [
            'avg' => 'المتوسط',
            'sum' => 'المجموع',
            'max' => 'الأعلى',
            'min' => 'الأدنى',
            'count' => 'العدد',
        ];

        foreach ($labels as $type => $label) {
            if (str_contains(strtolower($key), $type)) {
                return $label;
            }
        }

        return $this->formatColumnLabel($key);
    }

    /**
     * Detect the aggregate type from question text.
     *
     * @param  string  $questionText  The question text
     * @return string The aggregate type label
     */
    private function detectAggregateType(string $questionText): string
    {
        if (preg_match('/متوسط|معدل/u', $questionText)) {
            return 'المتوسط';
        }

        if (preg_match('/مجموع|إجمالي|اجمالي/u', $questionText)) {
            return 'المجموع';
        }

        if (preg_match('/أعلى|أكبر/u', $questionText)) {
            return 'الأعلى';
        }

        if (preg_match('/أقل|أصغر|أدنى/u', $questionText)) {
            return 'الأدنى';
        }

        return 'النتيجة';
    }

    /**
     * Format a no results message.
     *
     * @return string The no results message in Arabic
     */
    private function formatNoResults(): string
    {
        return __('agent.responses.no_results');
    }

    /**
     * Filter forbidden columns from item data.
     *
     * @param  array  $itemData  The raw item data
     * @param  array  $allowedColumns  The columns allowed for display
     * @param  string  $domain  The domain name
     * @param  string  $table  The table name
     * @return array The filtered item data
     */
    private function filterForbiddenColumns(array $itemData, array $allowedColumns, string $domain, string $table): array
    {
        // Get forbidden columns from config
        $forbiddenColumns = $this->configRegistry->getForbiddenColumns($domain, $table);

        // Filter to only allowed columns
        $filtered = collect($itemData)
            ->only($allowedColumns)
            ->toArray();

        // Remove any forbidden columns that might have slipped through
        foreach ($forbiddenColumns as $forbidden) {
            unset($filtered[$forbidden]);
        }

        return $filtered;
    }
}
