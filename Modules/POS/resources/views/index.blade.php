@extends('pos::layouts.master')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <div class="header-navigation">
            <a href="{{ route('dashboard') }}" class="back-to-main-btn">
                <i class="fas fa-home"></i>
                <span>العودة للصفحة الرئيسية</span>
            </a>
        </div>
        
        <div class="text-center mb-4">
            <h1 class="fw-bold">مرحباً بك في نظام نقاط البيع</h1>
            <p class="text-muted">إدارة المبيعات بسهولة وسرعة</p>
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-success p-3"><i class="fas fa-cash-register"></i></div>
                        <div>
                            <h6 class="mb-1 text-muted">مبيعات اليوم</h6>
                            <div class="fs-4 fw-bold">{{ number_format($todayStats['total_sales'] ?? 0) }} ريال</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-primary p-3"><i class="fas fa-receipt"></i></div>
                        <div>
                            <h6 class="mb-1 text-muted">المعاملات</h6>
                            <div class="fs-4 fw-bold">{{ $todayStats['transactions_count'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-warning p-3"><i class="fas fa-box"></i></div>
                        <div>
                            <h6 class="mb-1 text-muted">أصناف مباعة</h6>
                            <div class="fs-4 fw-bold">{{ $todayStats['items_sold'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('pos.create') }}" class="card h-100 shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-success p-3"><i class="fas fa-plus-circle"></i></div>
                        <div>
                            <h5 class="mb-1">معاملة جديدة</h5>
                            <div class="text-muted">ابدأ معاملة بيع جديدة</div>
                        </div>
                        <span class="ms-auto badge bg-secondary">F1</span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pos.reports') }}" class="card h-100 shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-primary p-3"><i class="fas fa-chart-bar"></i></div>
                        <div>
                            <h5 class="mb-1">التقارير</h5>
                            <div class="text-muted">عرض تقارير المبيعات</div>
                        </div>
                        <span class="ms-auto badge bg-secondary">F2</span>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('invoices.index') }}" class="card h-100 shadow-sm text-decoration-none">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="badge bg-info p-3"><i class="fas fa-file-invoice"></i></div>
                        <div>
                            <h5 class="mb-1">الفواتير</h5>
                            <div class="text-muted">إدارة جميع الفواتير</div>
                        </div>
                        <span class="ms-auto badge bg-secondary">F3</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @if(count($recentTransactions) > 0)
    <div class="card shadow-sm">
        <div class="card-header text-center">
            <h5 class="mb-0">المعاملات الأخيرة</h5>
            <div class="small text-muted">آخر {{ count($recentTransactions) }} معاملات اليوم</div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>الوقت</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $transaction)
                    <tr>
                        <td><span class="badge bg-primary">{{ $transaction->pro_id }}</span></td>
                        <td>
                            <div class="customer-info">
                                <span class="customer-name">{{ $transaction->acc1Head->aname ?? 'عميل نقدي' }}</span>
                            </div>
                        </td>
                        <td><span class="fw-bold text-success">{{ number_format($transaction->fat_net) }} ريال</span></td>
                        <td>
                            <span class="time">{{ $transaction->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pos.show', $transaction->id) }}" class="btn btn-sm btn-primary" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('pos.print', $transaction->id) }}" class="btn btn-sm btn-success" title="طباعة" target="_blank"><i class="fas fa-print"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
 

<script>
    // دعم اختصارات لوحة المفاتيح
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F1') {
            e.preventDefault();
            window.location.href = '{{ route("pos.create") }}';
        }
        
        if (e.key === 'F2') {
            e.preventDefault();
            window.location.href = '{{ route("pos.reports") }}';
        }
        
        if (e.key === 'F3') {
            e.preventDefault();
            window.location.href = '{{ route("invoices.index") }}';
        }
    });

    // تحديث الإحصائيات كل 5 دقائق
    setInterval(function() {
        location.reload();
    }, 300000);
</script>
@endsection