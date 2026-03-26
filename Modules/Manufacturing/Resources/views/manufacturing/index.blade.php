@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection
@section('content')
<div class="container-fluid">
    <!-- الإحصائيات السريعة -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('manufacturing::manufacturing.total invoices') }}</h6>
                            <h3 class="mb-0">{{ number_format($statistics['total']) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('manufacturing::manufacturing.this month') }}</h6>
                            <h3 class="mb-0">{{ number_format($statistics['thisMonth']) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('manufacturing::manufacturing.total value') }}</h6>
                            <h3 class="mb-0">{{ number_format($statistics['totalValue'], 2) }}</h3>
                            <small>{{ __('manufacturing::manufacturing.egp') }}</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('manufacturing::manufacturing.average cost') }}</h6>
                            <h3 class="mb-0">{{ number_format($statistics['avgValue'], 2) }}</h3>
                            <small>{{ __('manufacturing::manufacturing.egp') }}</small>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الفلاتر والبحث -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    {{ __('manufacturing::manufacturing.manufacturing invoices') }}
                </h5>
                @can('create Manufacturing Invoices')
                    <a href="{{ route('manufacturing.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        {{ __('manufacturing::manufacturing.new invoice') }}
                    </a>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('manufacturing.index') }}" id="filter-form">
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="{{ __('manufacturing::manufacturing.search by invoice number or description...') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="dateFrom" value="{{ request('dateFrom') }}" class="form-control"
                            placeholder="{{ __('manufacturing::manufacturing.from date') }}">
                    </div>

                    <div class="col-md-2">
                        <input type="date" name="dateTo" value="{{ request('dateTo') }}" class="form-control"
                            placeholder="{{ __('manufacturing::manufacturing.to date') }}">
                    </div>

                    <div class="col-md-2">
                        <select name="branchFilter" class="form-select">
                            <option value="">{{ __('manufacturing::manufacturing.all branches') }}</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branchFilter') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="perPage" class="form-select">
                            <option value="15" {{ request('perPage', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <input type="hidden" name="sortField" value="{{ request('sortField', 'pro_date') }}">
                <input type="hidden" name="sortDirection" value="{{ request('sortDirection', 'desc') }}">
            </form>

            <!-- الجدول -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 5%">#</th>
                            <th style="width: 10%" class="sortable" data-field="pro_id">
                                {{ __('manufacturing::manufacturing.invoice number') }}
                                @if (request('sortField') === 'pro_id')
                                    <i class="fas fa-sort-{{ request('sortDirection') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th style="width: 10%" class="sortable" data-field="pro_date">
                                {{ __('manufacturing::manufacturing.date') }}
                                @if (request('sortField') === 'pro_date')
                                    <i class="fas fa-sort-{{ request('sortDirection') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th style="width: 15%">{{ __('manufacturing::manufacturing.product') }}</th>
                            <th style="width: 15%">{{ __('manufacturing::manufacturing.raw materials') }}</th>
                            <th style="width: 10%">{{ __('manufacturing::manufacturing.employee') }}</th>
                            <th style="width: 10%">{{ __('manufacturing::manufacturing.branch') }}</th>
                            <th style="width: 10%" class="sortable" data-field="pro_value">
                                {{ __('manufacturing::manufacturing.value') }}
                                @if (request('sortField') === 'pro_value')
                                    <i class="fas fa-sort-{{ request('sortDirection') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th style="width: 15%">{{ __('manufacturing::manufacturing.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="text-center">
                                    {{ $loop->iteration + ($invoices->currentPage() - 1) * $invoices->perPage() }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $invoice->pro_id }}</span>
                                </td>
                                <td class="text-center">{{ $invoice->pro_date }}</td>
                                <td>{{ $invoice->acc1Head->aname ?? '-' }}</td>
                                <td>{{ $invoice->acc2Head->aname ?? '-' }}</td>
                                <td>{{ $invoice->employee->aname ?? '-' }}</td>
                                <td>{{ $invoice->branch->name ?? '-' }}</td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($invoice->pro_value, 2) }}</strong>
                                    <small class="text-muted d-block">{{ __('manufacturing::manufacturing.egp') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('view Manufacturing Invoices')
                                            <a href="{{ route('manufacturing.show', $invoice->id) }}"
                                                class="btn btn-info" title="{{ __('manufacturing::manufacturing.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('edit Manufacturing Invoices')
                                            <a href="{{ route('manufacturing.edit', $invoice->id) }}"
                                                class="btn btn-warning" title="{{ __('manufacturing::manufacturing.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                        @can('delete Manufacturing Invoices')
                                            <button onclick="confirmDelete({{ $invoice->id }})"
                                                class="btn btn-danger" title="{{ __('manufacturing::manufacturing.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">{{ __('manufacturing::manufacturing.no manufacturing invoices') }}</h5>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    {{ __('manufacturing::manufacturing.showing') }} {{ $invoices->firstItem() ?? 0 }} {{ __('manufacturing::manufacturing.to') }}
                    {{ $invoices->lastItem() ?? 0 }}
                    {{ __('manufacturing::manufacturing.of') }} {{ $invoices->total() }} {{ __('manufacturing::manufacturing.invoices') }}
                </div>
                <div>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Sorting functionality
document.querySelectorAll('.sortable').forEach(header => {
    header.style.cursor = 'pointer';
    header.style.userSelect = 'none';
    
    header.addEventListener('click', function() {
        const field = this.dataset.field;
        const currentField = document.querySelector('input[name="sortField"]').value;
        const currentDirection = document.querySelector('input[name="sortDirection"]').value;
        
        // Toggle direction if same field, otherwise default to desc
        let newDirection = 'desc';
        if (field === currentField) {
            newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
        }
        
        document.querySelector('input[name="sortField"]').value = field;
        document.querySelector('input[name="sortDirection"]').value = newDirection;
        document.getElementById('filter-form').submit();
    });
    
    header.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f8f9fa';
    });
    
    header.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
    });
});

// Auto-submit on filter change
document.querySelectorAll('#filter-form select, #filter-form input[type="date"]').forEach(element => {
    element.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
});

// Search with debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filter-form').submit();
    }, 500);
});

// Delete confirmation
function confirmDelete(invoiceId) {
    Swal.fire({
        title: '{{ __("manufacturing::manufacturing.are you sure?") }}',
        text: '{{ __("manufacturing::manufacturing.the manufacturing invoice and all related data will be deleted") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("manufacturing::manufacturing.yes, delete it") }}',
        cancelButtonText: '{{ __("manufacturing::manufacturing.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/manufacturing/${invoiceId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Success message
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: '{{ __("manufacturing::manufacturing.success") }}',
        text: '{{ session("success") }}',
        timer: 3000,
        showConfirmButton: false
    });
@endif

// Error message
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: '{{ __("manufacturing::manufacturing.error") }}',
        text: '{{ session("error") }}',
        confirmButtonText: '{{ __("manufacturing::manufacturing.ok") }}'
    });
@endif
</script>
@endpush

<style>
.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
