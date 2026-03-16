@extends('admin.dashboard')
@section('sidebar')
    @include('components.sidebar.items')
@endsection

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, currentColor, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 0.6;
        }

        .stats-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            transition: all 0.3s ease;
        }

        .stats-card:hover .stats-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .gradient-info {
            background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        }

        .gradient-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .gradient-warning {
            background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);
        }

        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card-modern:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .progress-modern {
            height: 12px;
            border-radius: 10px;
            background-color: #f0f0f0;
            overflow: hidden;
        }

        .progress-bar-modern {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .badge-modern {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .table-modern {
            border-radius: 12px;
            overflow: hidden;
        }

        .table-modern thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-modern tbody tr {
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid;
            border-image: linear-gradient(90deg, currentColor, transparent) 1;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
            <div>
                <h2 class="mb-1 font-hold fw-bold text-dark">
                    <i class="las la-chart-bar text-primary me-2"></i>إحصائيات الأصناف
                </h2>
                <p class="text-muted mb-0">نظرة شاملة على إحصائيات الأصناف في النظام</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('items.index') }}" class="btn btn-light font-hold fw-bold shadow-sm">
                    <i class="las la-arrow-right me-1"></i> رجوع
                </a>
                <a href="{{ route('items.statistics.refresh') }}" class="btn btn-primary font-hold fw-bold shadow-sm">
                    <i class="las la-sync me-1"></i> تحديث
                </a>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show font-hold fw-bold shadow-sm border-0 mb-4" role="alert">
                <i class="las la-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Cards Row 1: Main Statistics --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stats-card card shadow-sm h-100 border-start border-primary border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="stat-label text-primary">إجمالي الأصناف</div>
                                <h2 class="stat-number text-primary mb-0">{{ number_format($totalItems) }}</h2>
                            </div>
                            <div class="stats-icon-wrapper gradient-primary text-white">
                                <i class="las la-box"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stats-card card shadow-sm h-100 border-start border-success border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="stat-label text-success">إجمالي الوحدات</div>
                                <h2 class="stat-number text-success mb-0">{{ number_format($totalUnits) }}</h2>
                            </div>
                            <div class="stats-icon-wrapper gradient-success text-white">
                                <i class="las la-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stats-card card shadow-sm h-100 border-start border-info border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="stat-label text-info">إجمالي الباركود</div>
                                <h2 class="stat-number text-info mb-0">{{ number_format($totalBarcodes) }}</h2>
                            </div>
                            <div class="stats-icon-wrapper gradient-info text-white">
                                <i class="las la-barcode"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stats-card card shadow-sm h-100 border-start border-danger border-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="stat-label text-danger">إجمالي المتغيرات</div>
                                <h2 class="stat-number text-danger mb-0">{{ number_format($totalVaribals) }}</h2>
                            </div>
                            <div class="stats-icon-wrapper gradient-danger text-white">
                                <i class="las la-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cards Row 2: Averages --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="stats-icon-wrapper gradient-primary text-white mx-auto mb-3">
                            <i class="las la-calculator"></i>
                        </div>
                        <h6 class="stat-label text-muted mb-2">متوسط الوحدات/صنف</h6>
                        <h3 class="stat-number text-primary mb-0">{{ $avgUnitsPerItem }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="stats-icon-wrapper gradient-success text-white mx-auto mb-3">
                            <i class="las la-qrcode"></i>
                        </div>
                        <h6 class="stat-label text-muted mb-2">متوسط الباركود/صنف</h6>
                        <h3 class="stat-number text-success mb-0">{{ $avgBarcodesPerItem }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="stats-icon-wrapper gradient-info text-white mx-auto mb-3">
                            <i class="las la-sticky-note"></i>
                        </div>
                        <h6 class="stat-label text-muted mb-2">إجمالي الملاحظات</h6>
                        <h3 class="stat-number text-info mb-0">{{ number_format($totalNotes) }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="stats-icon-wrapper gradient-warning text-white mx-auto mb-3">
                            <i class="las la-code-branch"></i>
                        </div>
                        <h6 class="stat-label text-muted mb-2">إجمالي المتغيرات</h6>
                        <h3 class="stat-number text-warning mb-0">{{ number_format($totalVaribals) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts and Lists Row --}}
        <div class="row g-4 mb-4">
            {{-- Items by Type --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                            <div class="stats-icon-wrapper gradient-primary text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="las la-chart-pie"></i>
                            </div>
                            الأصناف حسب النوع
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($itemsByType as $type => $count)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="font-hold fw-bold">{{ $type }}</span>
                                    <span class="badge-modern badge bg-primary">{{ number_format($count) }}</span>
                                </div>
                                <div class="progress-modern">
                                    <div class="progress-bar-modern bg-primary" role="progressbar"
                                        style="width: {{ $totalItems > 0 ? ($count / $totalItems) * 100 : 0 }}%"
                                        aria-valuenow="{{ $count }}" aria-valuemin="0"
                                        aria-valuemax="{{ $totalItems }}">
                                    </div>
                                </div>
                                <small class="text-muted font-hold d-block mt-1">
                                    {{ $totalItems > 0 ? number_format(($count / $totalItems) * 100, 1) : 0 }}%
                                </small>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="las la-inbox text-muted"></i>
                                <p class="text-muted font-hold fw-bold mb-0">لا توجد بيانات</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Most Used Units --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                            <div class="stats-icon-wrapper gradient-success text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="las la-trophy"></i>
                            </div>
                            أكثر الوحدات استخداماً
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($mostUsedUnits as $index => $unit)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded {{ $index == 0 ? 'bg-light border border-success' : 'bg-light' }}"
                                 style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <span class="badge-modern badge {{ $index == 0 ? 'bg-warning' : 'bg-secondary' }} me-3" style="min-width: 35px;">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-hold fw-bold">{{ $unit->name }}</span>
                                </div>
                                <span class="badge-modern badge bg-success">
                                    {{ number_format($unit->usage_count) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="las la-inbox text-muted"></i>
                                <p class="text-muted font-hold fw-bold mb-0">لا توجد بيانات</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Price Ranges --}}
            <div class="col-xl-4 col-lg-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                            <div class="stats-icon-wrapper gradient-info text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="las la-tag"></i>
                            </div>
                            نطاقات الأسعار
                        </h5>
                    </div>
                    <div class="card-body">
                        @forelse($priceRanges as $range => $count)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border-bottom border-light">
                                <span class="font-hold fw-bold d-flex align-items-center">
                                    <i class="las la-money-bill text-success me-2"></i>
                                    {{ $range }}
                                </span>
                                <span class="badge-modern badge bg-info">
                                    {{ number_format($count) }}
                                </span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="las la-inbox text-muted"></i>
                                <p class="text-muted font-hold fw-bold mb-0">لا توجد بيانات</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Tables Row --}}
        <div class="row g-4 mb-4">
            {{-- Items with Most Units --}}
            <div class="col-xl-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                            <div class="stats-icon-wrapper gradient-purple text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="las la-layer-group"></i>
                            </div>
                            الأصناف ذات أكثر عدد وحدات
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="font-hold fw-bold border-0">#</th>
                                        <th class="font-hold fw-bold border-0">اسم الصنف</th>
                                        <th class="font-hold fw-bold text-center border-0">عدد الوحدات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($itemsWithMostUnits as $index => $item)
                                        <tr>
                                            <td class="font-hold fw-bold">{{ $index + 1 }}</td>
                                            <td class="font-hold fw-bold">{{ $item['name'] }}</td>
                                            <td class="font-hold fw-bold text-center">
                                                <span class="badge-modern badge bg-primary">{{ $item['units_count'] }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="las la-inbox text-muted"></i>
                                                    <p class="text-muted font-hold fw-bold mb-0">لا توجد بيانات</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Items --}}
            <div class="col-xl-6">
                <div class="card card-modern shadow-sm h-100">
                    <div class="card-header bg-white border-bottom pb-3">
                        <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                            <div class="stats-icon-wrapper gradient-success text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="las la-clock"></i>
                            </div>
                            أحدث الأصناف المضافة
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-modern table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="font-hold fw-bold border-0">الكود</th>
                                        <th class="font-hold fw-bold border-0">الاسم</th>
                                        <th class="font-hold fw-bold border-0">النوع</th>
                                        <th class="font-hold fw-bold border-0">التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentItems as $item)
                                        <tr>
                                            <td class="font-hold fw-bold">{{ $item['code'] }}</td>
                                            <td class="font-hold fw-bold">{{ $item['name'] }}</td>
                                            <td class="font-hold fw-bold">
                                                <span class="badge-modern badge bg-secondary">{{ $item['type'] }}</span>
                                            </td>
                                            <td class="font-hold fw-bold">
                                                <small>{{ $item['created_at'] }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="las la-inbox text-muted"></i>
                                                    <p class="text-muted font-hold fw-bold mb-0">لا توجد بيانات</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cost Statistics --}}
        @if ($costStats && $costStats->min_cost !== null)
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card card-modern shadow-sm">
                        <div class="card-header bg-white border-bottom pb-3">
                            <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                                <div class="stats-icon-wrapper gradient-danger text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                    <i class="las la-dollar-sign"></i>
                                </div>
                                إحصائيات التكلفة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 text-center">
                                <div class="col-md-4">
                                    <div class="p-4 rounded bg-light h-100">
                                        <div class="stats-icon-wrapper gradient-success text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                                            <i class="las la-arrow-down"></i>
                                        </div>
                                        <h6 class="stat-label text-muted mb-2">أقل تكلفة</h6>
                                        <h3 class="stat-number text-success mb-0">
                                            {{ number_format($costStats->min_cost ?? 0, 2) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded bg-light h-100">
                                        <div class="stats-icon-wrapper gradient-primary text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                                            <i class="las la-chart-line"></i>
                                        </div>
                                        <h6 class="stat-label text-muted mb-2">متوسط التكلفة</h6>
                                        <h3 class="stat-number text-primary mb-0">
                                            {{ number_format($costStats->avg_cost ?? 0, 2) }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded bg-light h-100">
                                        <div class="stats-icon-wrapper gradient-danger text-white mx-auto mb-3" style="width: 60px; height: 60px; font-size: 24px;">
                                            <i class="las la-arrow-up"></i>
                                        </div>
                                        <h6 class="stat-label text-muted mb-2">أعلى تكلفة</h6>
                                        <h3 class="stat-number text-danger mb-0">
                                            {{ number_format($costStats->max_cost ?? 0, 2) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Price Comparison Table --}}
        @if (isset($priceComparison) && $priceComparison->isNotEmpty())
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card card-modern shadow-sm">
                        <div class="card-header bg-white border-bottom pb-3">
                            <h5 class="font-hold fw-bold mb-0 d-flex align-items-center">
                                <div class="stats-icon-wrapper gradient-warning text-white me-2" style="width: 40px; height: 40px; font-size: 20px;">
                                    <i class="las la-exchange-alt"></i>
                                </div>
                                مقارنة أسعار الشراء والبيع
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="font-hold fw-bold border-0">الكود</th>
                                            <th class="font-hold fw-bold border-0">اسم الصنف</th>
                                            <th class="font-hold fw-bold text-center border-0">سعر الشراء</th>
                                            <th class="font-hold fw-bold text-center border-0">سعر البيع</th>
                                            <th class="font-hold fw-bold text-center border-0">الاتجاه</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($priceComparison as $item)
                                            <tr>
                                                <td class="font-hold fw-bold">{{ $item['item_code'] }}</td>
                                                <td class="font-hold fw-bold">{{ $item['item_name'] }}</td>
                                                <td class="font-hold fw-bold text-center">
                                                    {{ number_format($item['purchase_price'], 2) }}
                                                </td>
                                                <td class="font-hold fw-bold text-center">
                                                    {{ number_format($item['sale_price'], 2) }}
                                                </td>
                                                <td class="font-hold fw-bold text-center">
                                                    @if($item['trend'] === 'up')
                                                        <i class="las la-arrow-up text-success" style="font-size: 1.5rem;" title="سعر البيع أعلى"></i>
                                                    @elseif($item['trend'] === 'down')
                                                        <i class="las la-arrow-down text-danger" style="font-size: 1.5rem;" title="سعر البيع أقل"></i>
                                                    @else
                                                        <i class="las la-minus text-muted" style="font-size: 1.5rem;" title="متساوي"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
