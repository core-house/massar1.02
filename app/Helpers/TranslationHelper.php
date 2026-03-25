<?php

if (! function_exists('translateDynamicValue')) {
    /**
     * Translate dynamic database values like price types, unit names, and note types
     *
     * @param  string  $value
     * @return string
     */
    function translateDynamicValue($value)
    {
        // Common translations for price types
        $priceTranslations = [
            'قطاع' => __('items.sector_price'),
            'قطاعى' => __('items.sectoral_price'),
            'جملة' => __('items.wholesale_price'),
            'السوق' => __('items.market_price'),
            'تجزئة' => __('items.retail_price'),
            'خاص' => __('items.special_price'),
            'قطعه' => __('items.piece_price'),
            'قطعة' => __('items.piece_price'),
        ];

        // Common translations for note types  
        $noteTranslations = [
            'المجموعات' => __('items.groups'),
            'التصنيفات' => __('items.categories'),
            'الاماكن' => __('items.locations'),
            'الألوان' => __('items.colors'),
            'الأحجام' => __('items.sizes'),
            'العلامات التجارية' => __('items.brands'),
            // Common note detail values
            'المجموعات 1' => __('items.groups') . ' 1',
            'التصنيفات 1' => __('items.categories') . ' 1',
            'الاماكن 1' => __('items.locations') . ' 1',
        ];

        // Common translations for unit names
        $unitTranslations = [
            'قطعه' => __('items.piece_unit'),
            'قطعة' => __('items.piece_unit'),
            'كرتونة' => __('items.carton_unit'),
            'كرتون' => __('items.carton_unit'),
            'a-test' => __('items.test_unit'),
        ];

        // Common translations for varibal names
        $varibalTranslations = [
            'المقاس' => __('items.size'),
            'اللون' => __('items.color'),
            'الحجم' => __('items.size'),
            'النوع' => __('items.type'),
            'الشكل' => __('items.shape'),
            'الطول' => __('items.length'),
            'العرض' => __('items.width'),
            'الارتفاع' => __('items.height'),
        ];

        // Check if translation exists
        if (isset($priceTranslations[$value])) {
            return $priceTranslations[$value];
        }
        
        if (isset($noteTranslations[$value])) {
            return $noteTranslations[$value];
        }

        if (isset($unitTranslations[$value])) {
            return $unitTranslations[$value];
        }

        if (isset($varibalTranslations[$value])) {
            return $varibalTranslations[$value];
        }

        // Return original value if no translation found
        return $value;
    }
}