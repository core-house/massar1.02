@extends('pos::layouts.master')

@section('content')
<div class="pos-index-container">
    <div class="pos-header">
        <a href="{{ route('dashboard') }}" class="back-link">
            <i class="fas fa-arrow-right"></i>
            <span>العودة</span>
        </a>
        <div class="pos-title">
            <h1>نظام نقاط البيع</h1>
        </div>
        <label class="dark-mode-switch" title="تبديل الوضع الداكن">
            <input type="checkbox" id="darkModeToggle">
            <span class="slider"></span>
        </label>
    </div>
    
    <div class="pos-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-cash-register"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">مبيعات اليوم</div>
                <div class="stat-value">{{ number_format($todayStats['total_sales'] ?? 0) }} ريال</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">المعاملات</div>
                <div class="stat-value">{{ $todayStats['transactions_count'] ?? 0 }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">أصناف مباعة</div>
                <div class="stat-value">{{ $todayStats['items_sold'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="pos-actions">
        <a href="{{ route('pos.create') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
                <div class="action-title">معاملة جديدة</div>
                <div class="action-desc">ابدأ معاملة بيع جديدة</div>
            </div>
            <div class="action-shortcut">F1</div>
        </a>
        <a href="{{ route('pos.reports') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="action-content">
                <div class="action-title">التقارير</div>
                <div class="action-desc">عرض تقارير المبيعات</div>
            </div>
            <div class="action-shortcut">F2</div>
        </a>
        <a href="{{ route('pos.settings') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="action-content">
                <div class="action-title">الإعدادات</div>
                <div class="action-desc">تعديل الإعدادات</div>
            </div>
            <div class="action-shortcut">F4</div>
        </a>
        <a href="{{ route('invoices.index') }}" class="action-card">
            <div class="action-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="action-content">
                <div class="action-title">الفواتير</div>
                <div class="action-desc">إدارة جميع الفواتير</div>
            </div>
            <div class="action-shortcut">F3</div>
        </a>
    </div>

    @if(count($recentTransactions) > 0)
    <div class="pos-recent">
        <div class="recent-header">
            <h3>المعاملات الأخيرة</h3>
            <span class="recent-count">آخر {{ count($recentTransactions) }} معاملات</span>
        </div>
        <div class="recent-table">
            <table>
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
                        <td><span class="badge-neutral">{{ $transaction->pro_id }}</span></td>
                        <td>{{ $transaction->acc1Head->aname ?? 'عميل نقدي' }}</td>
                        <td class="amount">{{ number_format($transaction->fat_net) }} ريال</td>
                        <td class="time">{{ $transaction->created_at->format('H:i') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('pos.show', $transaction->id) }}" class="btn-neutral" title="عرض"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('pos.print', $transaction->id) }}" class="btn-neutral" title="طباعة" target="_blank"><i class="fas fa-print"></i></a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection

@push('styles')
<style>
    :root {
        /* Mint Green Colors from Main Style */
        --mint-green-50: #e6faf5;
        --mint-green-100: #b3f0e0;
        --mint-green-200: #80e6cb;
        --mint-green-300: #4ddcb6;
        --mint-green-400: #34d3a3;
        --mint-green-500: #2ab88d;
    }

    .pos-index-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        background: #ffffff;
        min-height: 100vh;
    }

    .pos-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 3rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--mint-green-200);
    }

    .back-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--mint-green-500);
    }

    .pos-title h1 {
        font-size: 2rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .pos-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: var(--mint-green-50);
        border: 2px solid var(--mint-green-200);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.2s;
    }

    .stat-card:hover {
        border-color: var(--mint-green-300);
        box-shadow: 0 2px 8px rgba(52, 211, 163, 0.15);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #ffffff;
        border: 2px solid var(--mint-green-300);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mint-green-500);
        font-size: 1.25rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--mint-green-500);
    }

    .pos-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .action-card {
        background: #ffffff;
        border: 2px solid var(--mint-green-200);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        position: relative;
    }

    .action-card:hover {
        border-color: var(--mint-green-300);
        box-shadow: 0 4px 12px rgba(52, 211, 163, 0.2);
        transform: translateY(-2px);
    }

    .action-icon {
        width: 48px;
        height: 48px;
        background: var(--mint-green-50);
        border: 2px solid var(--mint-green-300);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mint-green-500);
        font-size: 1.25rem;
    }

    .action-content {
        flex: 1;
    }

    .action-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .action-desc {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .action-shortcut {
        background: var(--mint-green-50);
        border: 1px solid var(--mint-green-200);
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--mint-green-500);
    }

    .pos-recent {
        background: #ffffff;
        border: 2px solid var(--mint-green-200);
        border-radius: 12px;
        overflow: hidden;
    }

    .recent-header {
        padding: 1.5rem;
        border-bottom: 2px solid var(--mint-green-200);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--mint-green-50);
    }

    .recent-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--mint-green-500);
        margin: 0;
    }

    .recent-count {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .recent-table {
        overflow-x: auto;
    }

    .recent-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .recent-table thead {
        background: var(--mint-green-50);
    }

    .recent-table th {
        padding: 1rem 1.5rem;
        text-align: right;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--mint-green-500);
        border-bottom: 2px solid var(--mint-green-200);
    }

    .recent-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--mint-green-100);
        color: #111827;
    }

    .recent-table tbody tr:hover {
        background: var(--mint-green-50);
    }

    .badge-neutral {
        background: var(--mint-green-100);
        color: var(--mint-green-500);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid var(--mint-green-200);
    }

    .amount {
        font-weight: 600;
        color: var(--mint-green-500);
    }

    .time {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-neutral {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--mint-green-50);
        border: 1px solid var(--mint-green-200);
        border-radius: 6px;
        color: var(--mint-green-500);
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-neutral:hover {
        background: var(--mint-green-200);
        border-color: var(--mint-green-300);
        color: var(--mint-green-500);
    }

    /* Dark Mode Toggle Switch */
    .dark-mode-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        cursor: pointer;
    }

    .dark-mode-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .dark-mode-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 28px;
    }

    .dark-mode-switch .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .dark-mode-switch input:checked + .slider {
        background-color: #374151;
    }

    .dark-mode-switch input:checked + .slider:before {
        transform: translateX(24px);
    }

    .dark-mode-switch:hover .slider {
        background-color: #9ca3af;
    }

    .dark-mode-switch input:checked:hover + .slider {
        background-color: #4b5563;
    }

    /* Dark Mode Styles */
    body.dark-mode {
        background: #111827;
        color: #f9fafb;
    }

    body.dark-mode .pos-index-container {
        background: #111827;
    }

    body.dark-mode .pos-header {
        border-bottom-color: #374151;
    }

    body.dark-mode .back-link {
        color: #9ca3af;
    }

    body.dark-mode .back-link:hover {
        color: #d1d5db;
    }

    body.dark-mode .pos-title h1 {
        color: #f9fafb;
    }

    body.dark-mode .stat-card {
        background: #1f2937;
        border-color: #374151;
    }

    body.dark-mode .stat-card:hover {
        border-color: #4b5563;
    }

    body.dark-mode .stat-icon {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-mode .stat-label {
        color: #9ca3af;
    }

    body.dark-mode .stat-value {
        color: #f9fafb;
    }

    body.dark-mode .action-card {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }

    body.dark-mode .action-card:hover {
        border-color: #4b5563;
    }

    body.dark-mode .action-icon {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-mode .action-title {
        color: #f9fafb;
    }

    body.dark-mode .action-desc {
        color: #9ca3af;
    }

    body.dark-mode .action-shortcut {
        background: #374151;
        border-color: #4b5563;
        color: #9ca3af;
    }

    body.dark-mode .pos-recent {
        background: #1f2937;
        border-color: #374151;
    }

    body.dark-mode .recent-header {
        border-bottom-color: #374151;
    }

    body.dark-mode .recent-header h3 {
        color: #f9fafb;
    }

    body.dark-mode .recent-count {
        color: #9ca3af;
    }

    body.dark-mode .recent-table thead {
        background: #374151;
    }

    body.dark-mode .recent-table th {
        color: #d1d5db;
        border-bottom-color: #4b5563;
    }

    body.dark-mode .recent-table td {
        color: #f9fafb;
        border-bottom-color: #374151;
    }

    body.dark-mode .recent-table tbody tr:hover {
        background: #374151;
    }

    body.dark-mode .badge-neutral {
        background: #374151;
        color: #d1d5db;
    }

    body.dark-mode .amount {
        color: #f9fafb;
    }

    body.dark-mode .time {
        color: #9ca3af;
    }

    body.dark-mode .btn-neutral {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    body.dark-mode .btn-neutral:hover {
        background: #4b5563;
        border-color: #6b7280;
        color: #f9fafb;
    }

    body.dark-mode .dark-mode-switch .slider {
        background-color: #4b5563;
    }

    body.dark-mode .dark-mode-switch:hover .slider {
        background-color: #6b7280;
    }

    /* Light Mode - Enhanced borders and mint green accents */
    body:not(.dark-mode) .pos-header {
        border-bottom-color: var(--mint-green-200);
    }

    body:not(.dark-mode) .stat-card {
        border-color: var(--mint-green-200);
    }

    body:not(.dark-mode) .action-card {
        border-color: var(--mint-green-200);
    }

    body:not(.dark-mode) .pos-recent {
        border-color: var(--mint-green-200);
    }

    @media (max-width: 768px) {
        .pos-index-container {
            padding: 1rem;
        }

        .pos-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .pos-stats,
        .pos-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Dark Mode Toggle
    (function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const body = document.body;
        
        // تحميل التفضيل المحفوظ
        const savedTheme = localStorage.getItem('pos-dark-mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        // تطبيق الوضع الداكن إذا كان محفوظاً أو إذا كان النظام في الوضع الداكن
        const isDark = savedTheme === 'enabled' || (!savedTheme && prefersDark);
        
        if (isDark) {
            body.classList.add('dark-mode');
            if (darkModeToggle) {
                darkModeToggle.checked = true;
            }
        }
        
        // التبديل عند تغيير الـ checkbox
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', function() {
                const isCurrentlyDark = this.checked;
                body.classList.toggle('dark-mode', isCurrentlyDark);
                
                // تحديث جميع الـ toggles الأخرى
                document.querySelectorAll('#darkModeToggle').forEach(function(toggle) {
                    if (toggle !== darkModeToggle) {
                        toggle.checked = isCurrentlyDark;
                    }
                });
                
                localStorage.setItem('pos-dark-mode', isCurrentlyDark ? 'enabled' : 'disabled');
            });
        }
    })();

    // Keyboard shortcuts
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
        
        if (e.key === 'F4') {
            e.preventDefault();
            window.location.href = '{{ route("pos.settings") }}';
        }
    });

    // تحديث الإحصائيات كل 5 دقائق
    setInterval(function() {
        location.reload();
    }, 300000);
</script>
@endpush