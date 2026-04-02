@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.installments')
@endsection

@section('content')
    <style>
        /* Ensure proper RTL spacing for Arabic text */
        [dir="rtl"] label {
            text-align: right;
            display: block;
        }
        [dir="rtl"] .form-label {
            margin-bottom: 0.5rem;
        }
    </style>
    
    @include('components.breadcrumb', [
        'title' => __('installments::installments.installment_plans'),
        'breadcrumb_items' => [
            ['label' => __('installments::installments.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('installments::installments.installment_plans')],
        ],
    ])
    
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            @can('create Installment Plans')
                <div class="text-end mb-3">
                    <a href="{{ route('installments.plans.create') }}" type="button" class="btn btn-primary fw-bold">
                        <i class="fas fa-plus ms-2"></i>
                        {{ __('installments::installments.add_new_installment_plan') }}
                    </a>
                </div>
            @endcan
            <div class="card">
                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="row mb-4" dir="rtl">
                        <div class="col-12">
                            <div class="row g-3">
                                <!-- Client Filter with Live Search -->
                                <div class="col-md-3">
                                    <label for="client_search" class="form-label">{{ __('installments::installments.client') }}</label>
                                    <input 
                                        type="text" 
                                        id="client_search"
                                        class="form-control" 
                                        value="{{ request('client_search') }}"
                                        placeholder="{{ __('installments::installments.search') }}..."
                                        x-data
                                        @input.debounce.500ms="window.location.href = '{{ route('installments.plans.index') }}?client_search=' + $el.value + '&status={{ request('status') }}&date_from={{ request('date_from') }}&date_to={{ request('date_to') }}'">
                                </div>

                                <form method="GET" action="{{ route('installments.plans.index') }}" class="col-md-9">
                                    <input type="hidden" name="client_search" value="{{ request('client_search') }}">
                                    <div class="row g-3">
                                        <!-- Status Filter -->
                                        <div class="col-md-3">
                                            <label for="status" class="form-label">{{ __('installments::installments.status') }}</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="">{{ __('installments::installments.all') }}</option>
                                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('installments::installments.active') }}</option>
                                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('installments::installments.completed') }}</option>
                                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('installments::installments.cancelled') }}</option>
                                            </select>
                                        </div>

                                        <!-- Date From -->
                                        <div class="col-md-3">
                                            <label for="date_from" class="form-label">{{ __('installments::installments.from_date') }}</label>
                                            <input 
                                                type="date" 
                                                name="date_from" 
                                                id="date_from"
                                                class="form-control" 
                                                value="{{ request('date_from') }}">
                                        </div>

                                        <!-- Date To -->
                                        <div class="col-md-3">
                                            <label for="date_to" class="form-label">{{ __('installments::installments.to_date') }}</label>
                                            <input 
                                                type="date" 
                                                name="date_to" 
                                                id="date_to"
                                                class="form-control" 
                                                value="{{ request('date_to') }}">
                                        </div>

                                        <!-- Buttons -->
                                        <div class="col-md-3 d-flex align-items-end gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="las la-search"></i>
                                                {{ __('installments::installments.search') }}
                                            </button>
                                            <a href="{{ route('installments.plans.index') }}" class="btn btn-secondary">
                                                <i class="las la-redo-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('installments::installments.client') }}</th>
                                    <th>{{ __('installments::installments.total_amount') }}</th>
                                    <th>{{ __('installments::installments.number_of_installments') }}</th>
                                    <th>{{ __('installments::installments.start_date') }}</th>
                                    <th>{{ __('installments::installments.status') }}</th>
                                    <th>{{ __('installments::installments.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($installmentPlans as $plan)
                                    <tr class="text-center">
                                        <td> {{ $plan->id }} </td>
                                        <td>{{ $plan->account->aname ?? __('installments::installments.not_applicable') }} ({{ $plan->account->code ?? '' }})</td>
                                        <td>{{ number_format($plan->total_amount, 2) }}</td>
                                        <td>{{ $plan->number_of_installments }}</td>
                                        <td>{{ $plan->start_date->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-success">{{ $plan->status }}</span></td>
                                        <td>
                                            @can('view Installment Plans')
                                                <a class="btn btn-info btn-icon-square-sm"
                                                    href="{{ route('installments.plans.show', $plan->id) }}">
                                                    <i class="las la-eye"></i>
                                                </a>
                                            @endcan

                                            @can('edit Installment Plans')
                                                <a href="{{ route('installments.plans.edit', $plan->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('delete Installment Plans')
                                                <form action="{{ route('installments.plans.destroy', $plan->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('installments::installments.confirm_delete_plan') }} {{ __('installments::installments.all_installments_and_entries_will_be_deleted') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('installments::installments.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $installmentPlans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
