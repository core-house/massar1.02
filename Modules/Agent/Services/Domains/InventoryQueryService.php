<?php

declare(strict_types=1);

namespace Modules\Agent\Services\Domains;

use Modules\Agent\DTOs\ClassificationResult;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\TypedFilter;
use Modules\Agent\Services\DomainQueryService;

/**
 * Inventory Domain Query Service
 *
 * Handles query plan creation for Inventory domain questions.
 * Supports queries about products, stock levels, categories, etc.
 */
class InventoryQueryService extends DomainQueryService
{
    /**
     * Create a query plan for Inventory domain questions.
     *
     * @param  ClassificationResult  $classification  The classification result
     * @param  string  $questionText  The original question text
     * @return DomainQueryPlan The generated query plan
     */
    public function createQueryPlan(ClassificationResult $classification, string $questionText): DomainQueryPlan
    {
        $table = 'products';
        $allowedColumns = $this->configRegistry->getAllowedColumns('inventory', $table);
        $operation = $this->determineOperation($questionText);
        $filters = $this->extractFiltersFromQuestion($questionText, $allowedColumns);

        return new DomainQueryPlan(
            domain: 'inventory',
            table: $table,
            operation: $operation,
            allowedColumns: $allowedColumns,
            typedFilters: $filters,
            sort: ['created_at' => 'desc'],
            limit: 100,
        );
    }

    /**
     * Determine the operation type from question text (Inventory-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @return string The operation type: 'count', 'aggregate', or 'select'
     */
    protected function determineOperation(string $questionText): string
    {
        // Check for count operations (Inventory-specific patterns)
        if (preg_match('/كم عدد المنتجات|عدد المنتجات|كم منتج|احصاء المنتجات|كم صنف/u', $questionText)) {
            return 'count';
        }

        // Check for aggregate operations (Inventory-specific patterns)
        if (preg_match('/مجموع المخزون|إجمالي الكمية|متوسط السعر|معدل الأسعار/u', $questionText)) {
            return 'aggregate';
        }

        // Use parent implementation for general patterns
        return parent::determineOperation($questionText);
    }

    /**
     * Extract filters from question text (Inventory-specific).
     *
     * @param  string  $questionText  The question text to analyze
     * @param  array  $allowedColumns  The columns allowed for filtering
     * @return array Array of TypedFilter objects
     */
    protected function extractFiltersFromQuestion(string $questionText, array $allowedColumns): array
    {
        // Start with parent filters
        $filters = parent::extractFiltersFromQuestion($questionText, $allowedColumns);

        // Add Inventory-specific filters

        // Extract category filters (للبحث عن فئات)
        if (preg_match('/فئة|الفئة|تصنيف|التصنيف|نوع|النوع/u', $questionText)) {
            if (in_array('category', $allowedColumns)) {
                // Try to extract category name
                if (preg_match('/فئة\s+([^\s]+)|تصنيف\s+([^\s]+)|نوع\s+([^\s]+)/u', $questionText, $matches)) {
                    $category = trim($matches[1] ?? $matches[2] ?? $matches[3] ?? '');
                    if ($category) {
                        $filters[] = new TypedFilter(
                            column: 'category',
                            operator: 'like',
                            value: $category,
                            type: 'like'
                        );
                    }
                }
            }
        }

        // Extract stock level filters (للبحث عن مستويات المخزون)
        if (preg_match('/نفذ|نفدت|نفذت|انتهى|انتهت|المخزون منخفض/u', $questionText) && in_array('stock_quantity', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'stock_quantity',
                operator: '<=',
                value: 0,
                type: 'exact'
            );
        } elseif (preg_match('/مخزون قليل|كمية قليلة|أقل من/u', $questionText) && in_array('stock_quantity', $allowedColumns)) {
            if (preg_match('/(\d+)/u', $questionText, $matches)) {
                $quantity = (int) $matches[1];
                $filters[] = new TypedFilter(
                    column: 'stock_quantity',
                    operator: '<',
                    value: $quantity,
                    type: 'exact'
                );
            } else {
                // Default low stock threshold
                $filters[] = new TypedFilter(
                    column: 'stock_quantity',
                    operator: '<',
                    value: 10,
                    type: 'exact'
                );
            }
        } elseif (preg_match('/متوفر|في المخزون|موجود/u', $questionText) && in_array('stock_quantity', $allowedColumns)) {
            $filters[] = new TypedFilter(
                column: 'stock_quantity',
                operator: '>',
                value: 0,
                type: 'exact'
            );
        }

        // Extract price filters (للبحث عن أسعار)
        if (preg_match('/سعره|سعرها|بسعر|السعر/u', $questionText) && in_array('price', $allowedColumns)) {
            if (preg_match('/أكثر من|أكبر من|يزيد عن/u', $questionText)) {
                if (preg_match('/(\d+(?:\.\d+)?)/u', $questionText, $matches)) {
                    $price = (float) $matches[1];
                    $filters[] = new TypedFilter(
                        column: 'price',
                        operator: '>',
                        value: $price,
                        type: 'exact'
                    );
                }
            } elseif (preg_match('/أقل من|أصغر من/u', $questionText)) {
                if (preg_match('/(\d+(?:\.\d+)?)/u', $questionText, $matches)) {
                    $price = (float) $matches[1];
                    $filters[] = new TypedFilter(
                        column: 'price',
                        operator: '<',
                        value: $price,
                        type: 'exact'
                    );
                }
            }
        }

        // Extract SKU filters (للبحث عن رمز المنتج)
        if (preg_match('/رمز|الرمز|SKU|كود|الكود/u', $questionText) && in_array('sku', $allowedColumns)) {
            if (preg_match('/رمز\s+([A-Z0-9-]+)|SKU\s+([A-Z0-9-]+)|كود\s+([A-Z0-9-]+)/ui', $questionText, $matches)) {
                $sku = trim($matches[1] ?? $matches[2] ?? $matches[3] ?? '');
                if ($sku) {
                    $filters[] = new TypedFilter(
                        column: 'sku',
                        operator: '=',
                        value: $sku,
                        type: 'exact'
                    );
                }
            }
        }

        return $filters;
    }
}
