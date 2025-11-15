@extends('pos::layouts.master')

@section('content')
<div class="container py-4">
    <div class="reports-header">
        <div class="header-content">
            <div class="reports-title">
                <h1><i class="fas fa-chart-bar"></i> تقارير نقاط البيع</h1>
                <p>تحليل شامل لأداء المبيعات والمعاملات</p>
            </div>
            
            <div class="header-actions">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    طباعة التقرير
                </button>
                
                <a href="{{ route('pos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i>
                    العودة لنظام نقاط البيع
                </a>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="quick-stats">
        <div class="stat-card sales-today">
            <div class="stat-icon">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="stat-content">
                <h3>مبيعات اليوم</h3>
                <div class="stat-value">{{ number_format($todayStats['total_sales']) }}</div>
                <div class="stat-unit">ريال</div>
            </div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up text-success"></i>
                <span>نشط</span>
            </div>
        </div>

        <div class="stat-card transactions-today">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <h3>معاملات اليوم</h3>
                <div class="stat-value">{{ $todayStats['transactions_count'] }}</div>
                <div class="stat-unit">معاملة</div>
            </div>
            <div class="stat-trend">
                <i class="fas fa-chart-line text-info"></i>
                <span>إجمالي</span>
            </div>
        </div>

        <div class="stat-card items-sold">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3>أصناف مباعة</h3>
                <div class="stat-value">{{ number_format($todayStats['items_sold']) }}</div>
                <div class="stat-unit">قطعة</div>
            </div>
            <div class="stat-trend">
                <i class="fas fa-cubes text-warning"></i>
                <span>اليوم</span>
            </div>
        </div>

        <div class="stat-card avg-transaction">
            <div class="stat-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-content">
                <h3>متوسط المعاملة</h3>
                <div class="stat-value">
                    {{ number_format($additionalStats['avg_transaction_value'] ?? 0, 2) }}
                </div>
                <div class="stat-unit">ريال</div>
            </div>
            <div class="stat-trend">
                <i class="fas fa-clock text-info"></i>
                <span>
                    @if($additionalStats['last_transaction_time'] ?? null)
                        {{ \Carbon\Carbon::parse($additionalStats['last_transaction_time'])->diffForHumans() }}
                    @else
                        لا توجد معاملات
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- تفاصيل التقارير -->
    <div class="reports-content">
        <!-- تقرير المبيعات اليومية -->
        <div class="report-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-day"></i> تقرير المبيعات اليومية</h2>
                <span class="report-date">{{ today()->format('Y-m-d') }}</span>
            </div>
            
            <div class="report-grid">
                <div class="report-card">
                    <div class="card-header">
                        <h4>ملخص المبيعات</h4>
                    </div>
                    <div class="card-content">
                        <div class="summary-row">
                            <span>إجمالي المبيعات:</span>
                            <span class="amount">{{ number_format($todayStats['total_sales'], 2) }} ريال</span>
                        </div>
                        <div class="summary-row">
                            <span>عدد المعاملات:</span>
                            <span class="count">{{ $todayStats['transactions_count'] }} معاملة</span>
                        </div>
                        <div class="summary-row">
                            <span>أصناف مباعة:</span>
                            <span class="count">{{ number_format($todayStats['items_sold']) }} قطعة</span>
                        </div>
                        <div class="summary-row total">
                            <span>متوسط قيمة المعاملة:</span>
                            <span class="amount">
                                {{ $todayStats['transactions_count'] > 0 ? number_format($todayStats['total_sales'] / $todayStats['transactions_count'], 2) : '0.00' }} ريال
                            </span>
                        </div>
                    </div>
                </div>

                <div class="report-card">
                    <div class="card-header">
                        <h4>أداء الساعات</h4>
                    </div>
                    <div class="card-content">
                        <div class="hours-chart">
                            <div class="chart-placeholder">
                                <i class="fas fa-clock fa-3x text-muted"></i>
                                <p>رسم بياني لأداء المبيعات خلال ساعات اليوم</p>
                                <small class="text-muted">سيتم تطوير هذه الميزة قريباً</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- أكثر الأصناف مبيعاً -->
        <div class="report-section">
            <div class="section-header">
                <h2><i class="fas fa-star"></i> أكثر الأصناف مبيعاً</h2>
                <span class="report-period">اليوم</span>
            </div>
            
            <div class="top-items-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>الصنف</th>
                            <th>الكمية المباعة</th>
                            <th>إجمالي المبيعات</th>
                            <th>متوسط السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- سيتم ملء البيانات هنا من قاعدة البيانات -->
                        <tr>
                            <td><span class="rank gold">1</span></td>
                            <td>
                                <div class="item-info">
                                    <span class="item-name">صنف تجريبي</span>
                                    <small class="item-code">كود: DEMO001</small>
                                </div>
                            </td>
                            <td><span class="quantity">25 قطعة</span></td>
                            <td><span class="sales">1,250.00 ريال</span></td>
                            <td><span class="avg-price">50.00 ريال</span></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="no-data">
                                <i class="fas fa-info-circle"></i>
                                لا توجد بيانات كافية لعرض التقرير. ابدأ بإجراء بعض المعاملات.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- طرق الدفع -->
        <div class="report-section">
            <div class="section-header">
                <h2><i class="fas fa-credit-card"></i> تحليل طرق الدفع</h2>
                <span class="report-period">اليوم</span>
            </div>
            
            <div class="payment-methods-grid">
                <div class="payment-card cash">
                    <div class="payment-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="payment-info">
                        <h4>نقدي كامل</h4>
                        <div class="payment-amount">{{ number_format($paymentAnalysis['cash_total'] ?? 0, 2) }} ريال</div>
                        <div class="payment-percentage">
                            {{ $todayStats['total_sales'] > 0 ? round(($paymentAnalysis['cash_total'] ?? 0) / $todayStats['total_sales'] * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>

                <div class="payment-card card">
                    <div class="payment-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="payment-info">
                        <h4>دفع جزئي</h4>
                        <div class="payment-amount">{{ number_format($paymentAnalysis['partial_payment'] ?? 0, 2) }} ريال</div>
                        <div class="payment-percentage">
                            {{ $todayStats['total_sales'] > 0 ? round(($paymentAnalysis['partial_payment'] ?? 0) / $todayStats['total_sales'] * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>

                <div class="payment-card mixed">
                    <div class="payment-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="payment-info">
                        <h4>متوسط المعاملة</h4>
                        <div class="payment-amount">{{ number_format($additionalStats['avg_transaction_value'] ?? 0, 2) }} ريال</div>
                        <div class="payment-percentage">
                            {{ $todayStats['transactions_count'] }} معاملة
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات إضافية -->
    <div class="report-footer">
        <div class="footer-info">
            <div class="info-item">
                <i class="fas fa-calendar"></i>
                <span>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-user"></i>
                <span>المستخدم: {{ auth()->user()->name }}</span>
            </div>
            <div class="info-item">
                <i class="fas fa-store"></i>
                <span>نقطة البيع: {{ config('app.name') }}</span>
            </div>
        </div>
    </div>
</div>

@endsection
