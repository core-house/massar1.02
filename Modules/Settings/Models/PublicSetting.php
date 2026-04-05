<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class PublicSetting extends Model
{
    protected $fillable = ['label', 'key', 'input_type', 'category_id', 'value'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Return translated label if a translation key exists, otherwise fall back to stored label.
     */
    public function getLabelAttribute(string $value): string
    {
        $translationKey = 'settings::settings.setting_label_' . $this->key;
        $translated = __($translationKey);

        return $translated !== $translationKey ? $translated : $value;
    }
}
