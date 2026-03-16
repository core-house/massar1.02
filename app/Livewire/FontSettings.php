<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class FontSettings extends Component
{
    public $font_family;
    public $font_size;
    public $availableFonts;
    public $availableSizes;

    public function mount(): void
    {
        $setting = Setting::first();
        
        $this->font_family = $setting->font_family ?? 'IBM Plex Sans Arabic';
        $this->font_size = $setting->font_size ?? '16px';
        $this->availableFonts = get_available_fonts();
        $this->availableSizes = get_available_font_sizes();
    }

    public function save(): void
    {
        $this->validate([
            'font_family' => 'required|string|max:100',
            'font_size' => 'required|string|max:20',
        ]);

        $setting = Setting::first();
        
        if ($setting) {
            $setting->update([
                'font_family' => $this->font_family,
                'font_size' => $this->font_size,
            ]);
        } else {
            Setting::create([
                'font_family' => $this->font_family,
                'font_size' => $this->font_size,
            ]);
        }

        // Clear cache
        Cache::forget('public_settings');

        $this->dispatch('success-swal', [
            'title' => 'تم الحفظ!',
            'text' => 'تم حفظ إعدادات الخط بنجاح. سيتم تطبيق التغييرات عند تحديث الصفحة.',
            'icon' => 'success',
        ]);

        // Reload page to apply new font
        $this->dispatch('reload-page');
    }

    public function preview(): void
    {
        $this->dispatch('preview-font', [
            'fontFamily' => $this->font_family,
            'fontSize' => $this->font_size,
        ]);
    }

    public function resetToDefault(): void
    {
        $this->font_family = 'IBM Plex Sans Arabic';
        $this->font_size = '16px';
        
        $this->dispatch('preview-font', [
            'fontFamily' => $this->font_family,
            'fontSize' => $this->font_size,
        ]);
    }

    public function render()
    {
        return view('livewire.font-settings');
    }
}
