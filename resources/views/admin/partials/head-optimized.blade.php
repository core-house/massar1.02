<head>
    <meta charset="utf-8" />
    <title>MASAR مـسار</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    {{-- Google Fonts - Dynamic Font Loading --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ get_font_url() }}" rel="stylesheet">
    
    {{-- Font Awesome 6 (CDN - Single Source) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    
    {{-- Legacy CSS (Bootstrap, Icons, MetisMenu) --}}
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app-rtl.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/cake.css') }}" rel="stylesheet" type="text/css" />
    
    {{-- Masar Theme System --}}
    <link href="{{ asset('css/themes/masar-themes.css') }}" rel="stylesheet" type="text/css" />
    
    {{-- Early Theme Initialization (Prevents Flash) --}}
    <script>
        // Inline to prevent FOUC (Flash of Unstyled Content)
        (function() {
            const STORAGE_KEY = 'masar_theme';
            const VALID_THEMES = ['classic', 'mint-green', 'dark', 'monokai'];
            const DEFAULT_THEME = 'classic';
            
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                const theme = (stored && VALID_THEMES.indexOf(stored) !== -1) ? stored : DEFAULT_THEME;
                document.documentElement.classList.add('theme-' + theme);
            } catch (e) {
                document.documentElement.classList.add('theme-' + DEFAULT_THEME);
            }
        })();
    </script>

    {{-- Vite CSS (Core + Components) --}}
    @vite([
        'resources/css/core/variables.css',
        'resources/css/core/typography.css',
        'resources/css/components/loader.css',
        'resources/css/components/forms.css',
        'resources/css/components/tables.css',
        'resources/css/app.css'
    ])

    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    @livewireStyles

    {{-- Conditional Plugin Styles (Loaded only when needed) --}}
    @stack('plugin-styles')
    
    {{-- Custom Styles from Components --}}
    @stack('styles')
</head>

{{-- Page Loader Component --}}
@include('components.idintity.loader')
