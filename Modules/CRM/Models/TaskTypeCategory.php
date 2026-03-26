<?php

declare(strict_types=1);

namespace Modules\CRM\Models;

use Database\Factories\TaskTypeCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskTypeCategory extends Model
{
    use HasFactory;

      protected $guarded = ['id'];

    protected static function newFactory(): TaskTypeCategoryFactory
    {
        return TaskTypeCategoryFactory::new();
    }

    public function taskTypes(): HasMany
    {
        return $this->hasMany(TaskType::class, 'category_id');
    }
}
