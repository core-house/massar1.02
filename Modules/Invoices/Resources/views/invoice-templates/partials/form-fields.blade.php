<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('invoices::templates.template_name') }} <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $template->name ?? '') }}" required>
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            <label for="code">{{ __('invoices::templates.template_code') }} <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror"
                value="{{ old('code', $template->code ?? '') }}" required>
            @error('code')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>


<div class="form-group">
    <label for="description">{{ __('invoices::invoices.description') }}</label>
    <textarea name="description" id="description" rows="2" class="form-control">{{ old('description', $template->description ?? '') }}</textarea>
</div>


<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="sort_order">{{ __('invoices::templates.display_order') }}</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control"
                value="{{ old('sort_order', $template->sort_order ?? 0) }}" step="1" min="0"
                pattern="[0-9]*">
        </div>
    </div>


    <div class="col-md-6">
        <div class="form-group">
            <div class="custom-control custom-switch mt-4">
                <input type="checkbox" name="is_active" value="1" class="custom-control-input" id="is_active"
                    {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_active">{{ __('invoices::templates.template_active') }}</label>
            </div>
        </div>
    </div>
</div>


<hr>


<div class="form-group">
    <label>{{ __('invoices::invoices.invoice_types') }} <span class="text-danger">*</span></label>
    <div class="row">
        @foreach ($invoiceTypes as $typeId => $typeName)
            <div class="col-md-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="invoice_types[]" value="{{ $typeId }}"
                        class="custom-control-input invoice-type-checkbox" id="type_{{ $typeId }}"
                        {{ in_array($typeId,old('invoice_types',collect(optional($template)->invoiceTypes ?? [])->pluck('invoice_type')->toArray()))? 'checked': '' }}>
                    <label class="custom-control-label" for="type_{{ $typeId }}">
                        {{ $typeName }} ({{ $typeId }})
                    </label>


                    <div class="ml-4 default-option" id="default_{{ $typeId }}" style="display: none;">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_default[]" value="{{ $typeId }}"
                                class="custom-control-input" id="default_switch_{{ $typeId }}"
                                {{ collect(optional($template)->invoiceTypes ?? [])->where('invoice_type', $typeId)->first()?->is_default? 'checked': '' }}>
                            <label class="custom-control-label" for="default_switch_{{ $typeId }}">
                                <small class="text-muted">{{ __('invoices::templates.default_for_this_type') }}</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @error('invoice_types')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>


<hr>


<div class="form-group">
    <label>{{ __('invoices::templates.visible_columns_and_order') }} <span class="text-danger">*</span></label>
    <p class="text-muted">{{ __('invoices::templates.drag_to_reorder') }}</p>
    <div class="alert alert-info mb-3" role="alert">
        <i class="las la-info-circle"></i>
        <strong>{{ __('invoices::templates.column_width_warning_title') }}:</strong> {!! __('invoices::templates.column_width_warning_text') !!}
    </div>


    <div id="columns-container" class="border rounded p-3 bg-light">
        <div id="sortable-columns" class="list-group">
            @php
                $orderedColumns = old(
                    'column_order',
                    optional($template)->column_order ?? array_keys($availableColumns),
                );
                $selectedColumns = old('visible_columns', optional($template)->visible_columns ?? []);
                $columnWidths = old('column_widths', optional($template)->column_widths ?? []);
            @endphp


            @foreach ($orderedColumns as $columnKey)
                @if (isset($availableColumns[$columnKey]))
                    <div class="list-group-item column-item" data-column="{{ $columnKey }}">
                        <div class="row align-items-center">
                            <div class="col-1 text-center">
                                <i class="las la-grip-vertical text-muted" style="cursor: move;"></i>
                            </div>


                            <div class="col-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="visible_columns[]" value="{{ $columnKey }}"
                                        class="custom-control-input column-checkbox" id="column_{{ $columnKey }}"
                                        {{ in_array($columnKey, $selectedColumns) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="column_{{ $columnKey }}">
                                        {{ $availableColumns[$columnKey] }}
                                    </label>
                                </div>
                            </div>


                            <div class="col-4">
                                <div class="form-group mb-0">
                                    <label class="small">{{ __('invoices::templates.column_width') }} (px) <span class="text-muted">(5-500)</span></label>
                                    <input type="text" name="column_widths[{{ $columnKey }}]"
                                        class="form-control form-control-sm column-width-input" 
                                        value="{{ $columnWidths[$columnKey] ?? 100 }}"
                                        data-column="{{ $columnKey }}" 
                                        style="width: 100px;"
                                        placeholder="100"
                                        pattern="[0-9]*"
                                        inputmode="numeric"
                                        title="الحد الأدنى: 5 بكسل، الحد الأقصى: 500 بكسل">
                                </div>
                            </div>


                            <div class="col-2 text-center">
                                <span class="badge badge-primary width-display" id="width_{{ $columnKey }}">
                                    {{ $columnWidths[$columnKey] ?? 100 }}px
                                </span>
                            </div>
                        </div>


                        <input type="hidden" name="column_order[]" value="{{ $columnKey }}"
                            class="column-order-input">
                    </div>
                @endif
            @endforeach


            @foreach ($availableColumns as $columnKey => $columnName)
                @if (!in_array($columnKey, $orderedColumns))
                    <div class="list-group-item column-item" data-column="{{ $columnKey }}">
                        <div class="row align-items-center">
                            <div class="col-1 text-center">
                                <i class="las la-grip-vertical text-muted" style="cursor: move;"></i>
                            </div>


                            <div class="col-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="visible_columns[]" value="{{ $columnKey }}"
                                        class="custom-control-input column-checkbox" id="column_{{ $columnKey }}">
                                    <label class="custom-control-label" for="column_{{ $columnKey }}">
                                        {{ $columnName }}
                                    </label>
                                </div>
                            </div>


                            <div class="col-4">
                                <div class="form-group mb-0">
                                    <label class="small">{{ __('invoices::templates.column_width') }} (px) <span class="text-muted">(5-500)</span></label>
                                    <input type="text" name="column_widths[{{ $columnKey }}]"
                                        class="form-control form-control-sm column-width-input" 
                                        value="100" 
                                        data-column="{{ $columnKey }}" 
                                        style="width: 100px;"
                                        placeholder="100"
                                        pattern="[0-9]*"
                                        inputmode="numeric"
                                        title="الحد الأدنى: 5 بكسل، الحد الأقصى: 500 بكسل">
                                </div>
                            </div>


                            <div class="col-2 text-center">
                                <span class="badge badge-primary width-display"
                                    id="width_{{ $columnKey }}">100px</span>
                            </div>
                        </div>


                        <input type="hidden" name="column_order[]" value="{{ $columnKey }}"
                            class="column-order-input">
                    </div>
                @endif
            @endforeach
        </div>
    </div>


    @error('visible_columns')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<hr>

<div class="form-group">
    <label class="h5 mb-3">
        <i class="las la-print text-primary"></i>
        {{ __('invoices::templates.printable_sections_in_invoice') }}
    </label>
    <p class="text-muted mb-4">
        <i class="las la-info-circle"></i>
        {{ __('invoices::templates.select_sections_to_print') }}
    </p>

    @php
        $selectedSections = old(
            'printable_sections',
            optional($template)->printable_sections ??
                \Modules\Invoices\Models\InvoiceTemplate::defaultPrintableSections(),
        );

        $groupLabels = [
            'header' => __('invoices::templates.header_sections'),
            'parties' => __('invoices::templates.parties_sections'),
            'invoice_details' => __('invoices::templates.invoice_details_sections'),
            'content' => __('invoices::templates.content_sections'),
            'totals' => __('invoices::templates.totals_sections'),
            'footer' => __('invoices::templates.footer_sections'),
        ];

        $groupIcons = [
            'header' => 'las la-heading',
            'parties' => 'las la-users',
            'invoice_details' => 'las la-file-invoice',
            'content' => 'las la-list',
            'totals' => 'las la-calculator',
            'footer' => 'las la-align-center',
        ];
    @endphp

    <div class="row g-3">
        @foreach ($availableSections as $groupKey => $groupSections)
            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="{{ $groupIcons[$groupKey] ?? 'las la-folder' }} text-primary me-2"></i>
                            {{ $groupLabels[$groupKey] ?? $groupKey }}
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="toggleGroupSections('{{ $groupKey }}')">
                            <i class="las la-check-double"></i>
                            {{ __('invoices::templates.toggle_all') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- // عرض الأقسام المتاحة ضمن هذا المجموعة في الطباعه --}}

                            @foreach ($groupSections as $sectionKey => $sectionLabel)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="printable_sections[{{ $sectionKey }}]"
                                            value="1" class="form-check-input section-checkbox"
                                            id="section_{{ $sectionKey }}" data-group="{{ $groupKey }}"
                                            {{ $selectedSections[$sectionKey] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="section_{{ $sectionKey }}">
                                            {{ $sectionLabel }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<hr>

<div class="form-group">
    <label for="preamble_text" class="h6">
        <i class="las la-file-alt text-primary"></i>
        {{ __('invoices::templates.preamble_text') }}
    </label>
    <p class="text-muted small">
        <i class="las la-info-circle"></i>
        {{ __('invoices::templates.preamble_text_hint') }}
    </p>

    <!-- Toolbar for formatting -->
    <div class="btn-toolbar mb-2" role="toolbar" id="preamble-toolbar">
        <div class="btn-group btn-group-sm me-2" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('bold')" title="{{ __('invoices::invoices.bold') }}">
                <i class="las la-bold"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('italic')" title="{{ __('invoices::invoices.italic') }}">
                <i class="las la-italic"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('underline')"
                title="{{ __('invoices::invoices.underline') }}">
                <i class="las la-underline"></i>
            </button>
        </div>
        <div class="btn-group btn-group-sm me-2" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('justifyRight')"
                title="{{ __('invoices::invoices.align_right') }}">
                <i class="las la-align-right"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('justifyCenter')"
                title="{{ __('invoices::invoices.align_center') }}">
                <i class="las la-align-center"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('justifyLeft')"
                title="{{ __('invoices::invoices.align_left') }}">
                <i class="las la-align-left"></i>
            </button>
        </div>
        <div class="btn-group btn-group-sm me-2" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('insertUnorderedList')"
                title="{{ __('invoices::invoices.bullet_list') }}">
                <i class="las la-list-ul"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="formatText('insertOrderedList')"
                title="{{ __('invoices::invoices.numbered_list') }}">
                <i class="las la-list-ol"></i>
            </button>
        </div>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="insertHeading('h3')" title="{{ __('invoices::invoices.heading') }}">
                <i class="las la-heading"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="insertParagraph()" title="{{ __('invoices::invoices.paragraph') }}">
                <i class="las la-paragraph"></i>
            </button>
        </div>
    </div>

    <!-- Editable div instead of textarea -->
    <div id="preamble_editor" class="form-control" contenteditable="true"
        style="min-height: 200px; max-height: 400px; overflow-y: auto; direction: rtl; text-align: right; background: white;"
        dir="rtl">{!! old('preamble_text', $template->preamble_text ?? '') !!}</div>

    <!-- Hidden input to store the HTML -->
    <input type="hidden" name="preamble_text" id="preamble_text"
        value="{{ old('preamble_text', $template->preamble_text ?? '') }}">

    <small class="text-muted">{{ __('invoices::templates.preamble_supports_html') }}</small>
</div>

<script>
    // Format text in contenteditable div
    function formatText(command) {
        document.execCommand(command, false, null);
        updateHiddenInput();
    }

    // Insert heading
    function insertHeading(tag) {
        document.execCommand('formatBlock', false, tag);
        updateHiddenInput();
    }

    // Insert paragraph
    function insertParagraph() {
        document.execCommand('formatBlock', false, 'p');
        updateHiddenInput();
    }

    // Update hidden input when content changes
    function updateHiddenInput() {
        const editor = document.getElementById('preamble_editor');
        const hiddenInput = document.getElementById('preamble_text');
        if (editor && hiddenInput) {
            hiddenInput.value = editor.innerHTML;
        }
    }

    // Listen for changes in the editor
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.getElementById('preamble_editor');
        if (editor) {
            // Update hidden input on any change
            editor.addEventListener('input', updateHiddenInput);
            editor.addEventListener('blur', updateHiddenInput);

            // Handle paste - allow formatted content
            editor.addEventListener('paste', function(e) {
                setTimeout(updateHiddenInput, 10);
            });

            // Initialize hidden input value
            updateHiddenInput();
        }
    });
</script>

<script>
    function toggleGroupSections(groupKey) {
        const checkboxes = document.querySelectorAll(`input.section-checkbox[data-group="${groupKey}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
</script>
