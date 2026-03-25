<?php

declare(strict_types=1);

namespace Modules\Decumintations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    protected $fillable = ['name', 'description', 'color', 'icon'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }
}
