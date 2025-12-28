<?php

use Modules\Settings\Models\{PublicSetting, Currency};

if (!function_exists('isMultiCurrencyEnabled')) {
    function isMultiCurrencyEnabled()
    {
        $setting = PublicSetting::where('key', 'multi_currency_enabled')->first();
        return $setting && $setting->value == '1';
    }
}

if (!function_exists('getDefaultCurrency')) {
    /**
     * الحصول على العملة الافتراضية للنظام
     */
    function getDefaultCurrency()
    {
        return Currency::where('is_default', true)->first();
    }
}

if (!function_exists('getExchangeRate')) {
    /**
     * الحصول على سعر الصرف لعملة معينة
     */
    function getExchangeRate($currencyId)
    {
        if (!$currencyId) return 1;

        $currency = Currency::with('latestRate')->find($currencyId);

        // لو العملة مش موجودة أو هي الافتراضية، السعر 1
        if (!$currency || $currency->is_default) {
            return 1;
        }

        return $currency->latestRate->rate ?? 1;
    }
}
