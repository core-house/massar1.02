<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        <i class="fas fa-tachometer-alt"></i> لوحة تحكم الشيكات
                    </h2>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="dateFilter" id="week" value="week" wire:model.live="dateFilter">
                        <label class="btn btn-outline-primary" for="week">أسبوع</label>

                        <input type="radio" class="btn-check" name="dateFilter" id="month" value="month" wire:model.live="dateFilter">
                        <label class="btn btn-outline-primary" for="month">شهر</label>

                        <input type="radio" class="btn-check" name="dateFilter" id="year" value="year" wire:model.live="dateFilter">
                        <label class="btn btn-outline-primary" for="year">سنة</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-left-primary">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي الشيكات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-square fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-warning">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    شيكات معلقة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['pending']) }}
                                </div>
                                <div class="text-xs text-muted">
                                    {{ number_format($stats['pendingAmount'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-success">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    شيكات مصفاة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['cleared']) }}
                                </div>
                                <div class="text-xs text-muted">
                                    {{ number_format($stats['clearedAmount'], 2) }} ر.س
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-left-danger">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    شيكات مرتدة
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['bounced']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="row mb-4">
            <!-- Overdue Checks -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-exclamation-circle"></i> الشيكات المتأخرة
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($overdueChecks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>رقم الشيك</th>
                                            <th>البنك</th>
                                            <th>المبلغ</th>
                                            <th>تاريخ الاستحقاق</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($overdueChecks as $check)
                                            <tr>
                                                <td>{{ $check->check_number }}</td>
                                                <td>{{ $check->bank_name }}</td>
                                                <td>{{ number_format($check->amount, 2) }}</td>
                                                <td class="text-danger">
                                                    {{ $check->due_date->format('Y-m-d') }}
                                                    <br><small>{{ $check->due_date->diffForHumans() }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="text-muted">لا توجد شيكات متأخرة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Checks by Bank -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-university"></i> الشيكات حسب البنك
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($checksByBank->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>البنك</th>
                                            <th>العدد</th>
                                            <th>إجمالي المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checksByBank as $bank)
                                            <tr>
                                                <td>{{ $bank->bank_name }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $bank->count }}</span>
                                                </td>
                                                <td>{{ number_format($bank->total_amount, 2) }} ر.س</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-university fa-2x text-muted mb-2"></i>
                                <p class="text-muted">لا توجد بيانات</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Checks -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history"></i> الشيكات الأخيرة
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($recentChecks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>رقم الشيك</th>
                                            <th>البنك</th>
                                            <th>المبلغ</th>
                                            <th>تاريخ الاستحقاق</th>
                                            <th>الحالة</th>
                                            <th>النوع</th>
                                            <th>أُنشئ بواسطة</th>
                                            <th>تاريخ الإنشاء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentChecks as $check)
                                            <tr>
                                                <td>
                                                    <strong>{{ $check->check_number }}</strong>
                                                </td>
                                                <td>{{ $check->bank_name }}</td>
                                                <td>{{ number_format($check->amount, 2) }} ر.س</td>
                                                <td>{{ $check->due_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $check->status_color }}">
                                                        {{ ucfirst($check->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $check->type === 'incoming' ? 'success' : 'info' }}">
                                                        {{ ucfirst($check->type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $check->creator->name ?? 'غير محدد' }}</td>
                                                <td>{{ $check->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد شيكات</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trend Chart (if needed) -->
        @if($monthlyTrend->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-line"></i> الاتجاه الشهري
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyTrendChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if($monthlyTrend->count() > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
    @script
    <script>
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        const monthlyData = @json($monthlyTrend);
        
        const labels = monthlyData.map(item => `${item.year}-${item.month.toString().padStart(2, '0')}`);
        const counts = monthlyData.map(item => item.count);
        const amounts = monthlyData.map(item => item.total_amount);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'عدد الشيكات',
                    data: counts,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'إجمالي المبلغ',
                    data: amounts,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'اتجاه الشيكات الشهري'
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'الشهر'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'عدد الشيكات'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'المبلغ (ر.س)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    </script>
    @endscript
    @endif

    @style
    <style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .text-gray-800 {
        color: #5a5c69 !important;
    }

    .text-gray-300 {
        color: #dddfeb !important;
    }
    </style>
    @endstyle
</div>