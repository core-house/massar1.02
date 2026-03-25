@extends('pos::layouts.master')

@section('content')

@push('styles')
<style>
    .setup-tabs .nav-link {
        color: rgba(255,255,255,0.8) !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }
    .setup-tabs .nav-link:hover {
        color: #fff !important;
        background: rgba(255,255,255,0.1);
    }
    .setup-tabs .nav-link.active {
        color: #fff !important;
        background: transparent !important;
        border-bottom: 3px solid #fff !important;
    }
    .setup-card-header {
        background: linear-gradient(135deg, #059669, #10B981) !important;
        border-bottom: none;
        padding-bottom: 0;
    }
</style>
@endpush

<div class="container py-4">
    <div class="mb-4">
        <div class="header-navigation d-flex justify-content-between align-items-center">
            <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-right me-2"></i>
                {{ __('pos.back_to_home', [], 'ar') ?? 'العودة للصفحة الرئيسية' }}
            </a>
            <h3 class="mb-0 fw-bold text-success">
                <i class="fas fa-sliders-h me-2"></i>
                {{ __('pos.setup', [], 'ar') ?? 'الإعدادات الأساسية للنقاط' }}
            </h3>
            <div></div>
        </div>
    </div>

    <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header setup-card-header text-white pt-3">
            <ul class="nav nav-tabs card-header-tabs setup-tabs" id="setupTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="drivers-tab" data-bs-toggle="tab" data-bs-target="#drivers" type="button" role="tab" aria-controls="drivers" aria-selected="true">
                        <i class="fas fa-motorcycle me-2"></i>{{ __('pos.drivers', [], 'ar') ?? 'عمال التوصيل' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="areas-tab" data-bs-toggle="tab" data-bs-target="#areas" type="button" role="tab" aria-controls="areas" aria-selected="false">
                        <i class="fas fa-map-marked-alt me-2"></i>{{ __('pos.delivery_areas', [], 'ar') ?? 'مناطق التوصيل' }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="tables-tab" data-bs-toggle="tab" data-bs-target="#tables" type="button" role="tab" aria-controls="tables" aria-selected="false">
                        <i class="fas fa-chair me-2"></i>{{ __('pos.tables', [], 'ar') ?? 'الطاولات' }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="setupTabsContent">
                <div class="tab-pane fade show active" id="drivers" role="tabpanel" aria-labelledby="drivers-tab">
                    @include('pos::setup.partials.drivers_tab')
                </div>
                <div class="tab-pane fade" id="areas" role="tabpanel" aria-labelledby="areas-tab">
                    @include('pos::setup.partials.areas_tab')
                </div>
                <div class="tab-pane fade" id="tables" role="tabpanel" aria-labelledby="tables-tab">
                    @include('pos::setup.partials.tables_tab')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
