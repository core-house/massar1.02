<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    public $availableLocales = [
        'ar' => 'العربية',
        'en' => 'English',
        'tr' => 'Türkçe'
    ];

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        if (array_key_exists($locale, $this->availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
            $this->currentLocale = $locale;
            
            // إعادة تحميل الصفحة لتطبيق التغييرات
            $this->redirect(request()->header('Referer'));
        }
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
