<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpArticle extends Model
{
    protected $fillable = [
        'category_id', 'title', 'title_en', 'content', 'content_en',
        'route_key', 'status', 'views_count', 'sort_order',
    ];

    protected $casts = [
        'views_count' => 'integer',
        'sort_order'  => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'category_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(HelpFeedback::class, 'article_id');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function scopePublished($query): mixed
    {
        return $query->where('status', 'published');
    }

    public function scopeForRoute($query, string $routeKey): mixed
    {
        return $query->where('route_key', $routeKey);
    }
}
