<?php

namespace Modules\MyResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceType extends Model
{
    protected $fillable = [
        'resource_category_id',
        'name',
        'name_ar',
        'description',
        'specifications',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specifications' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory($query, int $categoryId)
    {
        return $query->where('resource_category_id', $categoryId);
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn () => app()->getLocale() === 'ar' ? ($this->name_ar ?: $this->name) : ($this->name ?: $this->name_ar),
        );
    }
}

