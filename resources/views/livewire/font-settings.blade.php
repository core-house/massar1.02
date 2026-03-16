<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-font me-2"></i>
                        إعدادات الخطوط
                    </h4>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <!-- Action Buttons - Top Right -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="submit" 
                                            class="btn btn-primary"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            <i class="fas fa-save me-2"></i>
                                            حفظ التغييرات
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin me-2"></i>
                                            جاري الحفظ...
                                        </span>
                                    </button>
                                    <button type="button" 
                                            wire:click="resetToDefault" 
                                            class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-2"></i>
                                        استعادة الافتراضي
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Font Family Selection -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-text-height me-2"></i>
                                    نوع الخط
                                </label>
                                <select wire:model.live="font_family" 
                                        class="form-select form-select-lg"
                                        wire:change="preview">
                                    @foreach($availableFonts as $key => $font)
                                        <option value="{{ $key }}" style="font-family: {{ $font['family'] }}">
                                            {{ $font['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    اختر نوع الخط المناسب للنظام
                                </small>
                            </div>

                            <!-- Font Size Selection -->
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-text-width me-2"></i>
                                    حجم الخط
                                </label>
                                <select wire:model.live="font_size" 
                                        class="form-select form-select-lg"
                                        wire:change="preview">
                                    @foreach($availableSizes as $size => $label)
                                        <option value="{{ $size }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    اختر حجم الخط المناسب للقراءة
                                </small>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-2 border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-eye me-2"></i>
                                            معاينة الخط
                                        </h5>
                                    </div>
                                    <div class="card-body" id="font-preview">
                                        <h1 class="mb-3">عنوان رئيسي - Heading 1</h1>
                                        <h2 class="mb-3">عنوان فرعي - Heading 2</h2>
                                        <h3 class="mb-3">عنوان ثانوي - Heading 3</h3>
                                        <p class="mb-3">
                                            هذا نص تجريبي لمعاينة الخط المختار. يمكنك رؤية كيف سيظهر النص في النظام.
                                            This is a sample text to preview the selected font. You can see how the text will appear in the system.
                                        </p>
                                        <p class="mb-3">
                                            <strong>نص عريض - Bold Text</strong> | 
                                            <em>نص مائل - Italic Text</em> | 
                                            <u>نص مسطر - Underlined Text</u>
                                        </p>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>العمود 1</th>
                                                        <th>العمود 2</th>
                                                        <th>العمود 3</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>بيانات 1</td>
                                                        <td>بيانات 2</td>
                                                        <td>بيانات 3</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary">زر تجريبي</button>
                                            <button type="button" class="btn btn-secondary">زر ثانوي</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Font Information Card -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الخطوط
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">الخطوط المتاحة:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>IBM Plex Sans Arabic - خط احترافي حديث</li>
                                <li><i class="fas fa-check text-success me-2"></i>Cairo - خط عربي أنيق</li>
                                <li><i class="fas fa-check text-success me-2"></i>Tajawal - خط واضح وسهل القراءة</li>
                                <li><i class="fas fa-check text-success me-2"></i>Almarai - خط عصري بسيط</li>
                                <li><i class="fas fa-check text-success me-2"></i>Amiri - خط تقليدي جميل</li>
                                <li><i class="fas fa-check text-success me-2"></i>Noto Sans Arabic - خط Google الشهير</li>
                                <li><i class="fas fa-check text-success me-2"></i>Noto Kufi Arabic - خط كوفي حديث</li>
                                <li><i class="fas fa-check text-success me-2"></i>Changa - خط ديناميكي</li>
                                <li><i class="fas fa-check text-success me-2"></i>Harmattan - خط أفريقي عربي</li>
                                <li><i class="fas fa-check text-success me-2"></i>Lateef - خط نسخي تقليدي</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">ملاحظات:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>سيتم تطبيق الخط على كامل النظام</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>قد تحتاج لتحديث الصفحة لرؤية التغييرات</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>الخطوط محملة من Google Fonts</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>اختر حجم خط مناسب للقراءة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview font changes in real-time
    document.addEventListener('livewire:init', () => {
        Livewire.on('preview-font', (event) => {
            const data = event[0] || event;
            const fontFamily = data.fontFamily;
            const fontSize = data.fontSize;
            
            // Get font URL from available fonts
            const fonts = @json($availableFonts);
            const fontUrl = fonts[fontFamily]?.url;
            
            if (fontUrl) {
                // Load font dynamically
                const link = document.createElement('link');
                link.href = fontUrl;
                link.rel = 'stylesheet';
                document.head.appendChild(link);
            }
            
            // Apply to preview section
            const preview = document.getElementById('font-preview');
            if (preview) {
                const fontCssFamily = fonts[fontFamily]?.family || fontFamily;
                preview.style.fontFamily = fontCssFamily;
                preview.style.fontSize = fontSize;
            }
        });

        Livewire.on('reload-page', () => {
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        });
    });
</script>
@endpush
