@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.accounts')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title  fw-bold">تقرير الأرباح والخسائر</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="from_date" class="form-label  fw-bold">من تاريخ:</label>
                            <input type="date" id="from_date" class="form-control" value="{{ $fromDate }}" onchange="updateDateRange()">
                        </div>
                        <div class="col-md-4">
                            <label for="to_date" class="form-label  fw-bold">إلى تاريخ:</label>
                            <input type="date" id="to_date" class="form-control" value="{{ $toDate }}" onchange="updateDateRange()">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary" onclick="updateDateRange()">
                                <i class="fas fa-search"></i> تحديث التقرير
                            </button>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title  fw-bold">إجمالي الإيرادات</h5>
                                    <h1 class="text-white">{{ number_format($totalRevenue, 2) }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title  fw-bold">إجمالي المصروفات</h5>
                                    <h1 class="text-white">{{ number_format($totalExpenses, 2) }}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title  fw-bold">{{ $netProfit >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}</h5>
                                    <h1 class="text-white">{{ number_format(abs($netProfit), 2) }}</h1>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title  fw-bold mb-0">الإيرادات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                            <thead class="table-danger">
                                                <tr>
                                                    <th class=" fw-bold">كود الحساب</th>
                                                    <th class=" fw-bold">اسم الحساب</th>
                                                    <th class=" fw-bold text-end">المبلغ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($revenueAccounts as $account)
                                                    @php
                                                        $revenue = $account->balance ?? 0;
                                                    @endphp
                                                    @if($revenue != 0 )
                                                        <tr>
                                                            <td class="">{{ $account->code }}</td>
                                                            <td class="">{{ $account->aname }}</td>
                                                            <td class="text-end  fw-bold text-success">
                                                                {{ number_format($revenue, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center ">لا توجد إيرادات</td>
                                                    </tr>
                                                @endforelse
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title  fw-bold mb-0">المصروفات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-danger">
                                                <tr>
                                                    <th class=" fw-bold">كود الحساب</th>
                                                    <th class=" fw-bold">اسم الحساب</th>
                                                    <th class=" fw-bold text-end">المبلغ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($expenseAccounts as $account)
                                                    @php
                                                        $expense = $account->balance ?? 0;
                                                    @endphp
                                                    @if($expense > 0)
                                                        <tr>
                                                            <td class="">{{ $account->code }}</td>
                                                            <td class="">{{ $account->aname }}</td>
                                                            <td class="text-end  fw-bold text-danger">
                                                                {{ number_format($expense, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center ">لا توجد مصروفات</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert {{ $netProfit >= 0 ? 'alert-success' : 'alert-warning' }}">
                                <h5 class=" fw-bold">
                                    {{ $netProfit >= 0 ? 'النتيجة: ربح' : 'النتيجة: خسارة' }}
                                </h5>
                                <p class=" mb-0">
                                    <strong>إجمالي الإيرادات:</strong> {{ number_format($totalRevenue, 2) }} |
                                    <strong>إجمالي المصروفات:</strong> {{ number_format($totalExpenses, 2) }} |
                                    <strong>{{ $netProfit >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}:</strong> {{ number_format(abs($netProfit), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateDateRange() {
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    
    if (fromDate && toDate) {
        const url = new URL(window.location);
        url.searchParams.set('from_date', fromDate);
        url.searchParams.set('to_date', toDate);
        window.location.href = url.toString();
    }
}
</script>
@endsection 