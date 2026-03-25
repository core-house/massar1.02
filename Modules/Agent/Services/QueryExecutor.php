<?php

declare(strict_types=1);

namespace Modules\Agent\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Modules\Agent\DTOs\DomainQueryPlan;
use Modules\Agent\DTOs\QueryResult;
use Modules\Agent\DTOs\TypedFilter;
use Modules\Agent\Exceptions\InvalidOperationException;

class QueryExecutor
{
    public function __construct(
        private QueryLogger $logger,
        private DomainConfigRegistry $configRegistry
    ) {}

    /**
     * تنفيذ query plan بشكل آمن وفعال
     */
    public function execute(DomainQueryPlan $plan, ?int $questionId = null): QueryResult
    {
        $startTime = microtime(true);

        try {
            $query = $this->buildQuery($plan);

            $data = match ($plan->operation) {
                'count' => $query->count(),
                'aggregate' => $this->executeAggregate($query, $plan),
                'select' => $query->get(),
                default => throw new InvalidOperationException("عملية غير مدعومة: {$plan->operation}"),
            };

            $executionTime = (int) ((microtime(true) - $startTime) * 1000);
            $resultCount = is_int($data) ? $data : $data->count();

            // تسجيل تنفيذ الاستعلام
            $this->logger->log($plan, $resultCount, $executionTime, $questionId);

            return new QueryResult(
                data: $data,
                count: $resultCount,
                executionTime: $executionTime,
            );
        } catch (Exception $e) {
            $this->logger->logError($plan, $e);
            throw $e;
        }
    }

    /**
     * بناء Eloquent query من الخطة
     */
    private function buildQuery(DomainQueryPlan $plan): Builder
    {
        $modelClass = $this->getModelClass($plan->domain, $plan->table);
        $query = $modelClass::query();

        // تطبيق filters
        foreach ($plan->typedFilters as $filter) {
            $query = $this->applyFilter($query, $filter);
        }

        // تطبيق sort
        if ($plan->sort) {
            foreach ($plan->sort as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        // تطبيق limit للـ select operations فقط
        if ($plan->operation === 'select') {
            $query->limit($plan->limit);
        }

        // تطبيق eager loading للعلاقات إذا كانت موجودة في التكوين
        $relationships = $this->getRelationships($plan->domain, $plan->table);
        if (! empty($relationships)) {
            $query->with($relationships);
        }

        // اختيار الأعمدة المسموحة فقط
        if ($plan->operation === 'select') {
            $query->select($plan->allowedColumns);
        }

        return $query;
    }

    /**
     * تطبيق TypedFilter على query
     */
    private function applyFilter(Builder $query, TypedFilter $filter): Builder
    {
        return match ($filter->type) {
            'exact' => $query->where($filter->column, $filter->operator, $filter->value),
            'like' => $query->where($filter->column, 'like', "%{$filter->value}%"),
            'range' => $query->whereBetween($filter->column, $filter->value),
            'in' => $query->whereIn($filter->column, $filter->value),
            default => $query,
        };
    }

    /**
     * تنفيذ aggregate operation
     */
    private function executeAggregate(Builder $query, DomainQueryPlan $plan): int
    {
        // يمكن توسيع هذا لدعم aggregate functions مختلفة
        // حالياً نرجع count كـ default
        return $query->count();
    }

    /**
     * الحصول على model class من التكوين
     */
    private function getModelClass(string $domain, string $table): string
    {
        $config = $this->configRegistry->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table]['model'])) {
            throw new InvalidOperationException("Model غير موجود للـ domain: {$domain}, table: {$table}");
        }

        return $config['tables'][$table]['model'];
    }

    /**
     * الحصول على relationships من التكوين
     */
    private function getRelationships(string $domain, string $table): array
    {
        $config = $this->configRegistry->getDomainConfig($domain);

        if (! $config || ! isset($config['tables'][$table]['relationships'])) {
            return [];
        }

        return $config['tables'][$table]['relationships'];
    }
}
