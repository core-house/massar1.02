{{-- IndexedDB Helper --}}
<script src="{{ asset('modules/pos/js/pos-indexeddb.js') }}"></script>

{{-- Dark Mode Toggle Script --}}
<script>
    (function() {
        // دالة لتطبيق الوضع الداكن
        function applyDarkMode(isDark) {
            const body = document.body;
            if (isDark) {
                body.classList.add('dark-mode');
            } else {
                body.classList.remove('dark-mode');
            }
        }

        // تهيئة الوضع الداكن عند تحميل الصفحة
        const savedTheme = localStorage.getItem('pos-dark-mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = savedTheme === 'enabled' || (!savedTheme && prefersDark);
        
        applyDarkMode(isDark);

        // إضافة event listener لجميع أزرار toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('#darkModeToggle');
            
            toggles.forEach(function(toggle) {
                // تعيين الحالة الأولية
                toggle.checked = isDark;
                
                // إضافة event listener للتبديل
                toggle.addEventListener('change', function() {
                    const newDarkState = this.checked;
                    
                    applyDarkMode(newDarkState);
                    
                    // تحديث جميع الـ toggles الأخرى
                    toggles.forEach(function(otherToggle) {
                        if (otherToggle !== toggle) {
                            otherToggle.checked = newDarkState;
                        }
                    });
                    
                    // حفظ التفضيل
                    localStorage.setItem('pos-dark-mode', newDarkState ? 'enabled' : 'disabled');
                });
            });
        });
    })();
</script>

{{-- POS Main Scripts - يتم تنفيذها بعد تحميل jQuery و Bootstrap --}}
@include('pos::partials.scripts.main')
