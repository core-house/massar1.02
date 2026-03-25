<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpCategory extends Model
{
    protected $fillable = ['name', 'name_en', 'icon', 'slug', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(HelpArticle::class, 'category_id')->orderBy('sort_order');
    }

    public function activeArticles(): HasMany
    {
        return $this->hasMany(HelpArticle::class, 'category_id')
            ->where('status', 'published')
            ->orderBy('sort_order');
    }
}
