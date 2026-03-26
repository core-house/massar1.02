@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">لوحة تحكم إدارة الصيانة</h1>
                <p class="text-muted mb-0">نظرة شاملة على طلبات الصيانة والأداء</p>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <i class="fas fa-tools text-primary fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">إجمالي الطلبات</p>
                                <h3 class="mb-0">{{ $stats['overview']['total_maintenances'] }}</h3>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-week me-1"></i>
                                    {{ $stats['overview']['this_week'] }} هذا الأسبوع
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded p-3">
                                    <i class="fas fa-clock text-warning fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">قيد الانتظار</p>
                                <h3 class="mb-0">{{ $stats['overview']['pending'] }}</h3>
                                @if ($stats['performance']['pending_urgent'] > 0)
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $stats['performance']['pending_urgent'] }} عاجل
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded p-3">
                                    <i class="fas fa-wrench text-info fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">قيد التنفيذ</p>
                                <h3 class="mb-0">{{ $stats['overview']['in_progress'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-1">مكتملة</p>
                                <h3 class="mb-0">{{ $stats['overview']['completed'] }}</h3>
                                <small class="text-success">
                                    <i class="fas fa-chart-line me-1"></i>
                                    {{ $stats['performance']['completion_rate'] }}% معدل الإنجاز
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">الأداء الشهري</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الشهر الحالي</span>
                            <h4 class="mb-0">{{ $stats['performance']['current_month_count'] }}</h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">الشهر الماضي</span>
                            <h4 class="mb-0">{{ $stats['performance']['last_month_count'] }}</h4>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">نسبة التغيير</span>
                            <h4 class="mb-0 {{ $stats['performance']['is_increase'] ? 'text-success' : 'text-danger' }}">
                                <i
                                    class="fas fa-arrow-{{ $stats['performance']['is_increase'] ? 'up' : 'down' }} me-1"></i>
                                {{ abs($stats['performance']['change_percentage']) }}%
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">توزيع الحالات</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($stats['status_breakdown'] as $status => $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>
                                        <i class="fas fa-{{ $data['icon'] }} text-{{ $data['color'] }} me-2"></i>
                                        {{ $data['label'] }}
                                    </span>
                                    <strong>{{ $data['count'] }} ({{ $data['percentage'] }}%)</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $data['color'] }}" role="progressbar"
                                        style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">مؤشرات الأداء</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <svg width="120" height="120" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef"
                                        stroke-width="10" />
                                    <circle cx="60" cy="60" r="50" fill="none" stroke="#28a745"
                                        stroke-width="10"
                                        stroke-dasharray="{{ $stats['performance']['completion_rate'] * 3.14 }} 314"
                                        stroke-linecap="round" transform="rotate(-90 60 60)" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <h3 class="mb-0">{{ $stats['performance']['completion_rate'] }}%</h3>
                                    <small class="text-muted">معدل الإنجاز</small>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="mb-2">
                                <i class="fas fa-hourglass-half text-info me-2"></i>
                                <strong>{{ $stats['performance']['avg_completion_days'] }}</strong> يوم
                            </p>
                            <small class="text-muted">متوسط وقت الإنجاز</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Types Stats -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">إحصائيات أنواع الصيانة</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>نوع الصيانة</th>
                                        <th class="text-center">إجمالي الطلبات</th>
                                        <th class="text-center">قيد الانتظار</th>
                                        <th class="text-center">قيد التنفيذ</th>
                                        <th class="text-center">مكتملة</th>
                                        <th class="text-center">ملغاة</th>
                                        <th class="text-center">معدل الإنجاز</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['service_types'] as $type)
                                        <tr>
                                            <td>
                                                <strong>{{ $type->name }}</strong>
                                                @if ($type->description)
                                                    <br><small class="text-muted">{{ $type->description }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $type->total_maintenances }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">{{ $type->pending }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $type->in_progress }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $type->completed }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $type->cancelled }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px; min-width: 100px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $type->completion_rate }}%">
                                                        {{ $type->completion_rate }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                لا توجد أنواع صيانة مسجلة
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

        {{-- Charts Section --}}
        @include('maintenance::dashboard.charts-section')

        <!-- Recent Maintenances -->
        <div class="row g-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">أحدث طلبات الصيانة</h5>
                        <a href="{{ route('maintenances.index') }}" class="btn btn-sm btn-outline-primary">
                            عرض الكل <i class="fas fa-arrow-left ms-2"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم العميل</th>
                                        <th>البند</th>
                                        <th>رقم البند</th>
                                        <th>نوع الصيانة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_maintenances'] as $maintenance)
                                        <tr>
                                            <td>{{ $maintenance['id'] }}</td>
                                            <td>{{ $maintenance['client_name'] }}</td>
                                            <td>{{ $maintenance['item_name'] }}</td>
                                            <td>{{ $maintenance['item_number'] }}</td>
                                            <td>{{ $maintenance['service_type'] }}</td>
                                            <td>{{ $maintenance['date'] ?? $maintenance['created_at'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $maintenance['status_color'] }}">
                                                    {{ $maintenance['status_label'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('maintenances.edit', $maintenance['id']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                لا توجد طلبات صيانة
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

    </div>
@endsection

@push('styles')
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyTrendData = @json($stats['monthly_trend']);

            if (monthlyTrendData.labels.length > 0) {
                const ctx = document.getElementById('monthlyTrendChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: monthlyTrendData.labels,
                            datasets: [{
                                    label: 'إجمالي الطلبات',
                                    data: monthlyTrendData.data.total,
                                    borderColor: 'rgb(75, 192, 192)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                    tension: 0.4
                                },
                                {
                                    label: 'مكتملة',
                                    data: monthlyTrendData.data.completed,
                                    borderColor: 'rgb(40, 167, 69)',
                                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            console.log('Maintenance Dashboard loaded successfully');
        });
    </script>
@endpush
