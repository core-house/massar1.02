<?php

declare(strict_types=1);

if (! function_exists('get_font_family')) {
    /**
     * Get the configured font family from settings
     * 
     * @return string
     */
    function get_font_family(): string
    {
        return setting('font_family', 'IBM Plex Sans Arabic');
    }
}

if (! function_exists('get_font_size')) {
    /**
     * Get the configured font size from settings
     * 
     * @return string
     */
    function get_font_size(): string
    {
        return setting('font_size', '16px');
    }
}

if (! function_exists('get_available_fonts')) {
    /**
     * Get list of available fonts
     * 
     * @return array
     */
    function get_available_fonts(): array
    {
        return [
            'IBM Plex Sans Arabic' => [
                'name' => 'IBM Plex Sans Arabic',
                'url' => 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap',
                'family' => "'IBM Plex Sans Arabic', ui-sans-serif, system-ui, sans-serif",
            ],
            'Cairo' => [
                'name' => 'Cairo',
                'url' => 'https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap',
                'family' => "'Cairo', ui-sans-serif, system-ui, sans-serif",
            ],
            'Tajawal' => [
                'name' => 'Tajawal',
                'url' => 'https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap',
                'family' => "'Tajawal', ui-sans-serif, system-ui, sans-serif",
            ],
            'Almarai' => [
                'name' => 'Almarai',
                'url' => 'https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap',
                'family' => "'Almarai', ui-sans-serif, system-ui, sans-serif",
            ],
            'Amiri' => [
                'name' => 'Amiri',
                'url' => 'https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap',
                'family' => "'Amiri', ui-serif, serif",
            ],
            'Noto Sans Arabic' => [
                'name' => 'Noto Sans Arabic',
                'url' => 'https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap',
                'family' => "'Noto Sans Arabic', ui-sans-serif, system-ui, sans-serif",
            ],
            'Noto Kufi Arabic' => [
                'name' => 'Noto Kufi Arabic',
                'url' => 'https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap',
                'family' => "'Noto Kufi Arabic', ui-sans-serif, system-ui, sans-serif",
            ],
            'Changa' => [
                'name' => 'Changa',
                'url' => 'https://fonts.googleapis.com/css2?family=Changa:wght@200;300;400;500;600;700;800&display=swap',
                'family' => "'Changa', ui-sans-serif, system-ui, sans-serif",
            ],
            'Harmattan' => [
                'name' => 'Harmattan',
                'url' => 'https://fonts.googleapis.com/css2?family=Harmattan:wght@400;500;600;700&display=swap',
                'family' => "'Harmattan', ui-sans-serif, system-ui, sans-serif",
            ],
            'Lateef' => [
                'name' => 'Lateef',
                'url' => 'https://fonts.googleapis.com/css2?family=Lateef:wght@200;300;400;500;600;700;800&display=swap',
                'family' => "'Lateef', ui-serif, serif",
            ],
        ];
    }
}

if (! function_exists('get_font_url')) {
    /**
     * Get the Google Fonts URL for the configured font
     * 
     * @return string
     */
    function get_font_url(): string
    {
        $fontFamily = get_font_family();
        $fonts = get_available_fonts();
        
        return $fonts[$fontFamily]['url'] ?? $fonts['IBM Plex Sans Arabic']['url'];
    }
}

if (! function_exists('get_font_css_family')) {
    /**
     * Get the CSS font-family value for the configured font
     * 
     * @return string
     */
    function get_font_css_family(): string
    {
        $fontFamily = get_font_family();
        $fonts = get_available_fonts();
        
        return $fonts[$fontFamily]['family'] ?? $fonts['IBM Plex Sans Arabic']['family'];
    }
}

if (! function_exists('get_available_font_sizes')) {
    /**
     * Get list of available font sizes
     * 
     * @return array
     */
    function get_available_font_sizes(): array
    {
        return [
            '12px' => 'صغير جداً (12px)',
            '14px' => 'صغير (14px)',
            '16px' => 'عادي (16px)',
            '18px' => 'كبير (18px)',
            '20px' => 'كبير جداً (20px)',
            '22px' => 'ضخم (22px)',
        ];
    }
}
