{{-- Audio element for form submission sound --}}
<audio id="submit-sound" src="{{ asset('assets/wav/paper_sound.wav') }}"></audio>

{{-- Livewire Scripts - MUST be loaded FIRST to ensure Alpine.js is available --}}
{{-- Livewire 3 includes Alpine.js internally, no need to load separately --}}
@livewireScripts

{{-- Vite JS - MUST be loaded AFTER Livewire/Alpine.js --}}
@vite(['resources/js/app.js'])

{{-- Core JavaScript Libraries - Loaded after Livewire/Alpine.js --}}
{{-- jQuery is loaded in head.blade.php (jq.js) to ensure it's available early --}}
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/metismenu.min.js') }}" defer></script>
<script src="{{ asset('assets/js/waves.js') }}" defer></script>
<script src="{{ asset('assets/js/feather.min.js') }}" defer></script>
<script src="{{ asset('assets/js/simplebar.min.js') }}" defer></script>
<script src="{{ asset('assets/js/moment.js') }}" defer></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}" defer></script>
{{-- Dashboard scripts moved to @stack('dashboard-scripts') to avoid loading on non-dashboard pages --}}
@stack('dashboard-scripts')
<script src="{{ asset('assets/js/jq.js') }}"></script>

{{-- Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
@if (app()->getLocale() === 'ar')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js" defer></script>
@elseif(app()->getLocale() === 'tr')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/tr.js" defer></script>
@else
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/en.js" defer></script>
@endif

{{-- Tom Select JS --}}
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js" defer></script>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

{{-- Masar theme switcher --}}
<script src="{{ asset('js/theme-switcher.js') }}"></script>
<script>
(function(){ if (typeof MasarThemeSwitcher !== 'undefined') { MasarThemeSwitcher.bindDropdown('[data-masar-theme-dropdown]'); } })();
</script>

{{-- Stack for additional scripts from components --}}
@stack('scripts')

{{-- Unified initialization script - runs after all libraries are loaded --}}
<script>
    (function() {
        'use strict';

        // Wait for Alpine.js and all libraries to be ready
        function initApp() {
            // Check if Alpine.js is loaded (optional, only if needed)
            // Alpine.js is loaded with Livewire, but we don't need to wait for it for MetisMenu

            // Check if jQuery and MetisMenu are loaded (required for sidebar)
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.metisMenu === 'undefined') {
                // Retry after a short delay if jQuery or MetisMenu not ready
                setTimeout(initApp, 100);
                return;
            }

            // Initialize MetisMenu
            try {
                jQuery('.metismenu').metisMenu();
                console.log('MetisMenu initialized successfully');
            } catch (error) {
                console.error('MetisMenu initialization failed:', error);
                // Retry once more
                setTimeout(function() {
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.metisMenu !== 'undefined') {
                        jQuery('.metismenu').metisMenu();
                    }
                }, 200);
            }

            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Initialize Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Form submission sound
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    const audio = document.getElementById('submit-sound');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play().catch(function(err) {
                            console.warn('Audio play failed:', err);
                        });
                    }
                });
            });

            // Auto focus on first input with class "frst"
            initAutoFocus();

            // Keyboard shortcuts for focus navigation (also called after Livewire init)
            initFocusShortcuts();

            // Sidebar effects
            initSidebarEffects();

            // Navbar effects
            initNavbarEffects();

            // Sidebar state initialization
            initSidebarState();

            // Initialize global event listeners
            initGlobalEventListeners();

            // Initialize number input formatting
            initNumberInputs();
        }

        // Flag to prevent multiple event listener registrations
        let focusShortcutsInitialized = false;
        let globalEventListenersInitialized = false;

        // Auto focus on first input with class "frst"
        function initAutoFocus() {
            // Find first input with class "frst"
            const firstInput = document.querySelector('input.frst, textarea.frst, select.frst');
            if (firstInput) {
                // Add small delay to ensure page is fully loaded
                setTimeout(function() {
                    firstInput.focus();
                    // If it's an input or textarea, select the text if it has value
                    if ((firstInput.tagName === 'INPUT' || firstInput.tagName === 'TEXTAREA') && firstInput.select) {
                        firstInput.select();
                    }
                }, 100);
            }
        }

        // Keyboard shortcuts for focus navigation (F1 -> .frst, F2 -> .scnd)
        function initFocusShortcuts() {
            // Prevent multiple registrations
            if (focusShortcutsInitialized) {
                return;
            }
            
            document.addEventListener('keydown', function(event) {
                // F1 key - Focus on input with class "frst"
                if (event.key === 'F1') {
                    event.preventDefault();
                    const firstInput = document.querySelector('input.frst, textarea.frst, select.frst');
                    if (firstInput) {
                        firstInput.focus();
                        if ((firstInput.tagName === 'INPUT' || firstInput.tagName === 'TEXTAREA') && firstInput.select) {
                            firstInput.select();
                        }
                    }
                }
                
                // F2 key - Focus on input with class "scnd"
                if (event.key === 'F2') {
                    event.preventDefault();
                    const secondInput = document.querySelector('input.scnd, textarea.scnd, select.scnd');
                    if (secondInput) {
                        secondInput.focus();
                        if ((secondInput.tagName === 'INPUT' || secondInput.tagName === 'TEXTAREA') && secondInput.select) {
                            secondInput.select();
                        }
                    }
                }
            });
            
            focusShortcutsInitialized = true;
        }

        // Global event listeners (F12, Enter navigation, Print buttons)
        function initGlobalEventListeners() {
            if (globalEventListenersInitialized) {
                return;
            }

            document.addEventListener('keydown', function(e) {
                // F12 key - Submit form
                if (e.key === "F12") {
                    e.preventDefault();
                    const activeForm = document.querySelector('form');
                    if (activeForm) {
                        activeForm.submit();
                    }
                }

                // Enter key - Navigate between fields
                if (e.key === 'Enter' && !e.shiftKey) {
                    // Skip if we are in a textarea or on a button or special components that handle Enter
                    if (e.target.tagName === 'TEXTAREA' || 
                        e.target.tagName === 'BUTTON' || 
                        (e.target.tagName === 'INPUT' && (e.target.type === 'submit' || e.target.type === 'button')) ||
                        e.target.closest('.ts-control') || // TomSelect
                        e.target.closest('.select2-container') // Select2
                    ) {
                        return;
                    }

                    e.preventDefault();
                    const formElements = Array.from(document.querySelectorAll('input, select, textarea, button'))
                        .filter(el =>
                            !el.disabled &&
                            el.type !== 'hidden' &&
                            el.type !== 'checkbox' &&
                            el.type !== 'radio' &&
                            el.offsetParent !== null
                        );

                    const currentIndex = formElements.indexOf(e.target);
                    if (currentIndex > -1 && currentIndex < formElements.length - 1) {
                        formElements[currentIndex + 1].focus();
                    }
                }
            });

            // Print button functionality
            document.addEventListener('click', function(e) {
                const printBtn = e.target.closest('.printbtn');
                if (printBtn) {
                    let targetId = printBtn.getAttribute('data-target');
                    let content = document.getElementById(targetId)?.innerHTML;
                    if (content) {
                        let printWindow = window.open('', '', 'width=800,height=600');
                        printWindow.document.write(content);
                        printWindow.document.close();
                        printWindow.print();
                    }
                }
            });

            globalEventListenersInitialized = true;
        }

        // Number input auto-select and formatting
        function initNumberInputs() {
            // Using delegation for dynamic elements (like in Livewire)
            document.addEventListener('focusin', function(e) {
                if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
                    e.target.select();
                }
            });

            document.addEventListener('focusout', function(e) {
                if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
                    let val = parseFloat(e.target.value);
                    if (!isNaN(val)) {
                        // Format to 2 decimal places as per original behavior
                        e.target.value = val.toFixed(2);
                    }
                }
            });
        }

        // Legacy function for old forms
        window.disableButton = function() {
            const submitBtn = document.getElementById("submitBtn");
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            return true; // Allows form submission to continue
        };

        // ========================================
        // Global Submit Button Disabling System
        // ========================================
        // This system prevents double-submission across the entire project
        // Works with: Traditional Forms, Livewire Forms, and Livewire Volt
        
        (function initGlobalSubmitDisabling() {
            // Track submitted forms to prevent re-enabling
            const submittedForms = new WeakSet();
            
            // Disable submit button helper
            function disableSubmitButton(button) {
                if (!button || button.disabled) return;
                
                button.disabled = true;
                button.setAttribute('data-original-text', button.innerHTML);
                
                // Add loading indicator
                const loadingText = button.getAttribute('data-loading-text') || '{{ __("common.loading") }}';
                
                // Check if button has icon
                const hasIcon = button.querySelector('i, svg');
                if (hasIcon) {
                    button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
                } else {
                    button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`;
                }
                
                button.classList.add('disabled', 'btn-loading');
            }
            
            // Re-enable submit button helper (for error cases)
            function enableSubmitButton(button) {
                if (!button) return;
                
                button.disabled = false;
                const originalText = button.getAttribute('data-original-text');
                if (originalText) {
                    button.innerHTML = originalText;
                    button.removeAttribute('data-original-text');
                }
                button.classList.remove('disabled', 'btn-loading');
            }
            
            // Find all submit buttons in a form
            function findSubmitButtons(form) {
                const buttons = [];
                
                // Find button[type="submit"]
                buttons.push(...form.querySelectorAll('button[type="submit"]'));
                
                // Find input[type="submit"]
                buttons.push(...form.querySelectorAll('input[type="submit"]'));
                
                // Find buttons without type (default is submit)
                const buttonsWithoutType = Array.from(form.querySelectorAll('button:not([type])'));
                buttons.push(...buttonsWithoutType);
                
                return buttons;
            }
            
            // ========================================
            // 1. Traditional Forms (Non-Livewire)
            // ========================================
            document.addEventListener('submit', function(e) {
                const form = e.target;
                
                // Skip Livewire forms (they have wire:submit)
                if (form.hasAttribute('wire:submit') || 
                    form.hasAttribute('wire:submit.prevent') ||
                    form.closest('[wire\\:submit]') ||
                    form.closest('[wire\\:submit\\.prevent]')) {
                    return;
                }
                
                // Skip if already submitted
                if (submittedForms.has(form)) {
                    e.preventDefault();
                    return;
                }
                
                // Mark as submitted
                submittedForms.add(form);
                
                // Disable all submit buttons
                const submitButtons = findSubmitButtons(form);
                submitButtons.forEach(disableSubmitButton);
                
                // Re-enable after 5 seconds as fallback (in case of network error)
                setTimeout(function() {
                    if (submittedForms.has(form)) {
                        submitButtons.forEach(enableSubmitButton);
                        submittedForms.delete(form);
                    }
                }, 5000);
            });
            
            // ========================================
            // 2. Livewire Forms
            // ========================================
            document.addEventListener('livewire:init', function() {
                if (typeof Livewire === 'undefined') return;
                
                // Track Livewire requests
                const livewireSubmittedForms = new WeakMap();
                
                // Before Livewire request
                Livewire.hook('commit', ({ component, commit, respond }) => {
                    // Find the component's element
                    const el = component.el;
                    if (!el) return;
                    
                    // Find form within component
                    const form = el.querySelector('form[wire\\:submit], form[wire\\:submit\\.prevent]');
                    if (!form) return;
                    
                    // Find submit buttons
                    const submitButtons = findSubmitButtons(form);
                    if (submitButtons.length === 0) return;
                    
                    // Store buttons for this component
                    livewireSubmittedForms.set(component, submitButtons);
                    
                    // Disable buttons
                    submitButtons.forEach(disableSubmitButton);
                });
                
                // After Livewire request completes
                Livewire.hook('commit.finish', ({ component }) => {
                    const submitButtons = livewireSubmittedForms.get(component);
                    if (!submitButtons) return;
                    
                    // Re-enable buttons after a short delay
                    setTimeout(function() {
                        submitButtons.forEach(enableSubmitButton);
                        livewireSubmittedForms.delete(component);
                    }, 500);
                });
                
                // On Livewire error
                Livewire.hook('commit.error', ({ component }) => {
                    const submitButtons = livewireSubmittedForms.get(component);
                    if (!submitButtons) return;
                    
                    // Re-enable buttons immediately on error
                    submitButtons.forEach(enableSubmitButton);
                    livewireSubmittedForms.delete(component);
                });
            });
            
            // ========================================
            // 3. Additional Protection: Click Handler
            // ========================================
            // This catches any submit button clicks directly
            document.addEventListener('click', function(e) {
                const button = e.target.closest('button[type="submit"], input[type="submit"]');
                if (!button) return;
                
                // Skip if already disabled
                if (button.disabled) {
                    e.preventDefault();
                    return;
                }
                
                // Find parent form
                const form = button.closest('form');
                if (!form) return;
                
                // For non-Livewire forms, the submit event will handle it
                // For Livewire forms, disable immediately
                if (form.hasAttribute('wire:submit') || form.hasAttribute('wire:submit.prevent')) {
                    // Small delay to allow Livewire to process
                    setTimeout(function() {
                        disableSubmitButton(button);
                    }, 50);
                }
            });
            
            // ========================================
            // 4. Global Function for Manual Control
            // ========================================
            window.disableFormSubmit = function(formOrButton) {
                if (formOrButton.tagName === 'FORM') {
                    const buttons = findSubmitButtons(formOrButton);
                    buttons.forEach(disableSubmitButton);
                } else if (formOrButton.tagName === 'BUTTON' || formOrButton.tagName === 'INPUT') {
                    disableSubmitButton(formOrButton);
                }
            };
            
            window.enableFormSubmit = function(formOrButton) {
                if (formOrButton.tagName === 'FORM') {
                    const buttons = findSubmitButtons(formOrButton);
                    buttons.forEach(enableSubmitButton);
                } else if (formOrButton.tagName === 'BUTTON' || formOrButton.tagName === 'INPUT') {
                    enableSubmitButton(formOrButton);
                }
            };
        })();

        // Sidebar effects initialization
        function initSidebarEffects() {
            const sidebarLinks = document.querySelectorAll('.left-sidenav-menu li > a');

            // Ripple effect on links
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;

                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.right = (rect.width - x - size) + 'px';
                    ripple.style.top = y + 'px';

                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Particle effect on icons
            const menuIcons = document.querySelectorAll('.menu-icon');
            menuIcons.forEach(icon => {
                icon.parentElement.addEventListener('mouseenter', function(e) {
                    const rect = icon.getBoundingClientRect();
                    createMiniParticles(rect.left + rect.width / 2, rect.top + rect.height / 2, 3);
                });
            });

            function createMiniParticles(x, y, count) {
                for (let i = 0; i < count; i++) {
                    const particle = document.createElement('div');
                    particle.style.cssText = `
                    position: fixed;
                    width: 4px;
                    height: 4px;
                    background: #667eea;
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 9999;
                    animation: particleFly 0.8s ease-out forwards;
                `;

                    const angle = (Math.PI * 2 * i) / count;
                    const velocity = 20 + Math.random() * 20;

                    particle.style.left = x + 'px';
                    particle.style.top = y + 'px';
                    particle.style.setProperty('--tx', Math.cos(angle) * velocity + 'px');
                    particle.style.setProperty('--ty', Math.sin(angle) * velocity + 'px');

                    document.body.appendChild(particle);
                    setTimeout(() => particle.remove(), 800);
                }
            }

            // Add animation styles if not already added
            if (!document.getElementById('sidebar-animations')) {
                const style = document.createElement('style');
                style.id = 'sidebar-animations';
                style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                @keyframes particleFly {
                    0% {
                        transform: translate(0, 0) scale(1);
                        opacity: 1;
                    }
                    100% {
                        transform: translate(var(--tx), var(--ty)) scale(0);
                        opacity: 0;
                    }
                }
            `;
                document.head.appendChild(style);
            }

            // Re-initialize icons after a short delay
            setTimeout(function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        }

        // Navbar effects initialization
        function initNavbarEffects() {
            const navLinks = document.querySelectorAll('.topbar .nav-link');

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(114, 114, 255, 0.4);
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;

                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';

                    this.style.position = 'relative';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Particles on menu button
            const menuBtn = document.querySelector('.button-menu-mobile');
            if (menuBtn) {
                menuBtn.addEventListener('click', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = rect.left + rect.width / 2;
                    const y = rect.top + rect.height / 2;

                    for (let i = 0; i < 8; i++) {
                        const particle = document.createElement('div');
                        particle.style.cssText = `
                        position: fixed;
                        width: 5px;
                        height: 5px;
                        background: #7272ff;
                        border-radius: 50%;
                        pointer-events: none;
                        z-index: 9999;
                        left: ${x}px;
                        top: ${y}px;
                    `;

                        const angle = (Math.PI * 2 * i) / 8;
                        const velocity = 20 + Math.random() * 25;
                        const tx = Math.cos(angle) * velocity;
                        const ty = Math.sin(angle) * velocity;

                        particle.animate([{
                                transform: 'translate(0, 0) scale(1)',
                                opacity: 1
                            },
                            {
                                transform: `translate(${tx}px, ${ty}px) scale(0)`,
                                opacity: 0
                            }
                        ], {
                            duration: 800,
                            easing: 'ease-out'
                        });

                        document.body.appendChild(particle);
                        setTimeout(() => particle.remove(), 800);
                    }
                });
            }
        }

        // Sidebar Hide/Show Toggle Functionality
        window.toggleSidebar = function() {
            const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
            const sidebar = document.querySelector('.left-sidenav');
            const pageWrapper = document.querySelector('.page-wrapper');

            if (sidebar && pageWrapper) {
                if (sidebarHidden) {
                    sidebar.style.display = 'none';
                    pageWrapper.style.marginLeft = '0';
                    pageWrapper.style.marginRight = '0';
                } else {
                    sidebar.style.display = '';
                    pageWrapper.style.marginLeft = '';
                    pageWrapper.style.marginRight = '';
                }
            }
        };

        // Initialize sidebar state on page load
        function initSidebarState() {
            const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
            if (sidebarHidden) {
                setTimeout(toggleSidebar, 100);
            }
        }

        // Initialize when all scripts are loaded
        // Since we use 'defer' attribute, scripts load after DOM is ready
        // So we use window.load event to ensure all deferred scripts are loaded
        function startInitialization() {
            // Check if page is already loaded
            if (document.readyState === 'complete') {
                // Page already loaded, try to initialize immediately
                // But still check if scripts are ready (they might not be)
                setTimeout(initApp, 100);
            } else {
                // Wait for window load event (fires after all deferred scripts)
                window.addEventListener('load', function() {
                    // Give a small delay to ensure all scripts are fully initialized
                    setTimeout(initApp, 50);
                });
            }
        }

        // Start initialization
        startInitialization();

        // Also re-initialize icons on window load as fallback
        window.addEventListener('load', function() {
            setTimeout(function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }, 100);
        });
    })();
</script>

{{-- Icons early initialization - independent of jQuery/MetisMenu --}}
<script>
    (function() {
        function initIcons() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Run on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initIcons);
        } else {
            initIcons();
        }

        // Run again on window load (for deferred scripts)
        window.addEventListener('load', function() {
            setTimeout(initIcons, 50);
        });

        // Re-run after Livewire updates
        document.addEventListener('livewire:init', function() {
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('morph.updated', function() {
                    setTimeout(initIcons, 50);
                });
            }
        });

        // Expose globally for manual calls
        window.reinitIcons = initIcons;
    })();
</script>

{{-- YouTube-style Progress Bar Loader Script --}}
<script>
    (function() {
        'use strict';

        const loader = document.getElementById('page-loader');
        const loaderBar = loader?.querySelector('.loader-bar');

        if (!loader || !loaderBar) {
            return;
        }

        let progressTimer = null;
        let completeTimer = null;

        // Show loader and start progress
        function startLoader() {
            if (completeTimer) {
                clearTimeout(completeTimer);
                completeTimer = null;
            }

            loader.classList.add('active');
            loaderBar.style.width = '0%';

            // Simulate progress
            let progress = 0;
            progressTimer = setInterval(function() {
                progress += Math.random() * 15;
                if (progress > 90) {
                    progress = 90;
                }
                loaderBar.style.width = progress + '%';
            }, 200);
        }

        // Complete loader
        function completeLoader() {
            if (progressTimer) {
                clearInterval(progressTimer);
                progressTimer = null;
            }

            loader.classList.add('completing');
            loaderBar.style.width = '100%';

            // Hide loader after animation
            completeTimer = setTimeout(function() {
                loader.classList.remove('active', 'completing');
                loaderBar.style.width = '0%';
            }, 400);
        }

        // Start loader on page load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                startLoader();
                // Complete when page is fully loaded
                window.addEventListener('load', function() {
                    setTimeout(completeLoader, 300);
                });
            });
        } else {
            // Page already loading
            startLoader();
            if (document.readyState === 'complete') {
                setTimeout(completeLoader, 300);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(completeLoader, 300);
                });
            }
        }

        // Show loader on form submissions
        document.addEventListener('submit', function(e) {
            const form = e.target;
            // Skip if form has data-no-loader attribute
            if (form.hasAttribute('data-no-loader')) {
                return;
            }
            startLoader();
        });

        // Show loader on link clicks (navigation)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            // Skip if link has data-no-loader attribute
            if (link.hasAttribute('data-no-loader')) {
                return;
            }

            // Skip if it's a hash link or javascript link
            if (link.href &&
                (link.href.includes('#') ||
                    link.href.startsWith('javascript:') ||
                    link.getAttribute('target') === '_blank')) {
                return;
            }

            // Skip if it's a Livewire wire:navigate link (Livewire handles it)
            if (link.hasAttribute('wire:navigate')) {
                return;
            }

            // Show loader for regular navigation
            const href = link.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                startLoader();
            }
        });

        // Show loader on Livewire navigation
        if (typeof window.Livewire !== 'undefined') {
            document.addEventListener('livewire:init', function() {
                // Focus shortcuts and auto focus are handled in the main initialization script
                
                // التحقق من وجود Livewire قبل استخدامه
                if (typeof Livewire !== 'undefined' && typeof Livewire.hook === 'function') {
                    Livewire.hook('morph.updating', function() {
                        startLoader();
                    });

                    Livewire.hook('morph.updated', function() {
                        setTimeout(completeLoader, 200);
                    });

                    Livewire.hook('morph.failed', function() {
                        completeLoader();
                    });
                }
            });
        }

        // Show loader on AJAX requests
        let activeAjaxRequests = 0;
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            activeAjaxRequests++;
            if (activeAjaxRequests === 1) {
                startLoader();
            }

            return originalFetch.apply(this, args)
                .then(function(response) {
                    activeAjaxRequests--;
                    if (activeAjaxRequests === 0) {
                        setTimeout(completeLoader, 200);
                    }
                    return response;
                })
                .catch(function(error) {
                    activeAjaxRequests--;
                    if (activeAjaxRequests === 0) {
                        completeLoader();
                    }
                    throw error;
                });
        };

        // Expose functions globally for manual control
        window.showPageLoader = startLoader;
        window.hidePageLoader = completeLoader;
    })();
</script>
