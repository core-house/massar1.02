@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('reports::reports.item_sales_report'),
        'breadcrumb_items' => [['label' => __('reports::reports.home'), 'url' => route('admin.dashboard')], ['label' => __('reports::reports.item_sales_report')]],
    ])

    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>{{ __('reports::reports.item_sales_report') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('reports::reports.report_under_development') }}
                </div>
                
                {{-- Placeholder for future implementation --}}
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-muted">
                            {{ __('reports::reports.report_item_sales_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

