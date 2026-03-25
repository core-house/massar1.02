<?php

declare(strict_types=1);

namespace Modules\HelpCenter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpFeedback extends Model
{
    protected $fillable = ['article_id', 'user_id', 'is_helpful'];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(HelpArticle::class, 'article_id');
    }
}
