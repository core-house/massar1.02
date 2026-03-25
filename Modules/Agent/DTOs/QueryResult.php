<?php

declare(strict_types=1);

namespace Modules\Agent\DTOs;

use Illuminate\Support\Collection;

/**
 * Query Result DTO
 *
 * يمثل نتيجة تنفيذ استعلام مع metadata
 */
class QueryResult
{
    /**
     * @param  Collection|int  $data  البيانات المسترجعة (Collection للـ select، int للـ count/aggregate)
     * @param  int  $count  عدد النتائج
     * @param  int  $executionTime  وقت التنفيذ بالميلي ثانية
     */
    public function __construct(
        public Collection|int $data,
        public int $count,
        public int $executionTime,
    ) {}
}
