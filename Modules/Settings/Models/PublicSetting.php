<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Settings\Database\Factories\PublicSettingFactory;

class PublicSetting extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'key', 'input_type', 'category_id', 'value'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
