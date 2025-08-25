@props([
    'tableId',
    'filename' => 'export',
    'excelLabel' => 'Export Excel',
    'pdfLabel' => 'Export PDF',
    'printLabel' => 'Print',
])

{{-- data-skip-last="true"  --> الكومبونتنت بيعمل ignore لعمود العمليات تلقائي  --}}

<div class="d-flex align-items-center justify-content-end mb-3">
    <div class="btn-group" role="group" aria-label="Export actions" data-export-actions data-table-id="{{ $tableId }}" data-filename="{{ $filename }}" data-skip-last="true">
        <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center" data-action="export-excel">
            <i class="las la-file-excel me-1"></i>
            <span>{{ $excelLabel }}</span>
        </button>
        <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center" data-action="export-pdf">
            <i class="las la-file-pdf me-1"></i>
            <span>{{ $pdfLabel }}</span>
        </button>
        <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center" data-action="print">
            <i class="las la-print me-1"></i>
            <span>{{ $printLabel }}</span>
        </button>
    </div>
</div>


