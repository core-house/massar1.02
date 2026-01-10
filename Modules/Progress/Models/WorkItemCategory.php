<?php

namespace Modules\Progress\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkItemCategory extends Model
{
    protected $fillable = ['name'];

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class, 'category_id');
    }
}
