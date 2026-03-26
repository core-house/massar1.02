{{-- resources/views/components/layouts/settings-no-sidebar.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? __('Settings') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap" rel="stylesheet">
    <style>
        /* Alpine.js Utilities */
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Cairo', sans-serif;
        }
        
        /* RTL Support */
        [dir="rtl"] .rtl-flip {
            transform: scaleX(-1);
        }
        
        [dir="rtl"] .text-start {
            text-align: right !important;
        }
        
        [dir="rtl"] .text-end {
            text-align: left !important;
        }
        
        [dir="rtl"] .ms-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        
        [dir="rtl"] .me-auto {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        [dir="rtl"] .ps-3 {
            padding-left: 0 !important;
            padding-right: 1rem !important;
        }
        
        [dir="rtl"] .pe-3 {
            padding-right: 0 !important;
            padding-left: 1rem !important;
        }
        
        /* Submit Button Loading State Styles */
        button.btn-loading,
        input.btn-loading {
            cursor: not-allowed;
            opacity: 0.7;
            position: relative;
        }
        
        button.btn-loading:hover,
        input.btn-loading:hover {
            opacity: 0.7;
        }
        
        /* Spinner animation for loading state */
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        
        .spinner-border {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            vertical-align: text-bottom;
            border: 0.15em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }
        
        .spinner-border-sm {
            width: 0.875rem;
            height: 0.875rem;
            border-width: 0.125em;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <script>
        // Global Submit Button Disabling for Settings Pages
        document.addEventListener('DOMContentLoaded', function() {
            const submittedForms = new WeakSet();
            
            function disableSubmitButton(button) {
                if (!button || button.disabled) return;
                
                button.disabled = true;
                button.setAttribute('data-original-text', button.innerHTML);
                
                const loadingText = button.getAttribute('data-loading-text') || 'جاري الحفظ...';
                button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`;
                button.classList.add('disabled', 'btn-loading');
            }
            
            function findSubmitButtons(form) {
                const buttons = [];
                buttons.push(...form.querySelectorAll('button[type="submit"]'));
                buttons.push(...form.querySelectorAll('input[type="submit"]'));
                buttons.push(...Array.from(form.querySelectorAll('button:not([type])')));
                return buttons;
            }
            
            // Handle traditional form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
                    return;
                }
                
                if (submittedForms.has(form)) {
                    e.preventDefault();
                    return;
                }
                
                submittedForms.add(form);
                const submitButtons = findSubmitButtons(form);
                submitButtons.forEach(disableSubmitButton);
            });
            
            // Handle Livewire forms
            document.addEventListener('livewire:init', function() {
                if (typeof Livewire === 'undefined') return;
                
                const livewireSubmittedForms = new WeakMap();
                
                Livewire.hook('commit', ({ component }) => {
                    const el = component.el;
                    if (!el) return;
                    
                    const form = el.querySelector('form[wire\\:submit], form[wire\\:submit\\.prevent]');
                    if (!form) return;
                    
                    const submitButtons = findSubmitButtons(form);
                    if (submitButtons.length === 0) return;
                    
                    livewireSubmittedForms.set(component, submitButtons);
                    submitButtons.forEach(disableSubmitButton);
                });
                
                Livewire.hook('commit.finish', ({ component }) => {
                    const submitButtons = livewireSubmittedForms.get(component);
                    if (!submitButtons) return;
                    
                    setTimeout(function() {
                        submitButtons.forEach(button => {
                            button.disabled = false;
                            const originalText = button.getAttribute('data-original-text');
                            if (originalText) {
                                button.innerHTML = originalText;
                                button.removeAttribute('data-original-text');
                            }
                            button.classList.remove('disabled', 'btn-loading');
                        });
                        livewireSubmittedForms.delete(component);
                    }, 500);
                });
            });
        });
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        {{ $slot }}
    </div>
    
    @livewireScripts
</body>
</html>