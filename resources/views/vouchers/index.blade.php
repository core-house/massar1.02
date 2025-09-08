@extends('admin.dashboard')

@section('content')
    @include('components.breadcrumb', [
        'title' => $currentTypeInfo['title'],
        'items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => $currentTypeInfo['title']],
        ],
    ])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>{{ $currentTypeInfo['title'] }}</h2>

            @if (isset($currentTypeInfo['show_dropdown']) && $currentTypeInfo['show_dropdown'])
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-plus me-2"></i>
                        إضافة سند جديد
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('vouchers.create', ['type' => 'receipt']) }}">
                                <i class="fas fa-plus-circle text-success me-2"></i>سند قبض عام
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('vouchers.create', ['type' => 'payment']) }}">
                                <i class="fas fa-minus-circle text-danger me-2"></i>سند دفع عام
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('vouchers.create', ['type' => 'exp-payment']) }}">
                                <i class="fas fa-credit-card text-warning me-2"></i>سند دفع مصاريف
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item"
                                href="{{ route('multi-vouchers.create', ['type' => 'multi_payment']) }}">
                                <i class="fas fa-list-alt text-info me-2"></i>سند دفع متعدد
                            </a></li>
                        <li><a class="dropdown-item"
                                href="{{ route('multi-vouchers.create', ['type' => 'multi_receipt']) }}">
                                <i class="fas fa-list-ul text-primary me-2"></i>سند قبض متعدد
                            </a></li>
                    </ul>
                </div>
            @else
                @if (in_array($type, ['multi_payment', 'multi_receipt']))
                    <a href="{{ route('multi-vouchers.create', ['type' => $type]) }}"
                        class="btn btn-{{ $currentTypeInfo['color'] }}">
                        <i class="fas {{ $currentTypeInfo['icon'] }} me-2"></i>
                        {{ $currentTypeInfo['create_text'] }}
                    </a>
                @else
                    <a href="{{ route('vouchers.create', ['type' => $type]) }}"
                        class="btn btn-{{ $currentTypeInfo['color'] }}">
                        <i class="fas {{ $currentTypeInfo['icon'] }} me-2"></i>
                        {{ $currentTypeInfo['create_text'] }}
                    </a>
                @endif
            @endif
        </div>

        <div class="card-body border-bottom">
            <div class="row">
                <div class="col-md-12">
                    <div class="btn-group flex-wrap" role="group">
                        <a href="{{ route('vouchers.index') }}"
                            class="btn btn-outline-secondary {{ $type === 'all' ? 'active' : '' }}">
                            <i class="fas fa-list me-1"></i>الكل
                        </a>
                        <a href="{{ route('vouchers.index', ['type' => 'receipt']) }}"
                            class="btn btn-outline-success {{ $type === 'receipt' ? 'active' : '' }}">
                            <i class="fas fa-plus-circle me-1"></i>سندات القبض
                        </a>
                        <a href="{{ route('vouchers.index', ['type' => 'payment']) }}"
                            class="btn btn-outline-danger {{ $type === 'payment' ? 'active' : '' }}">
                            <i class="fas fa-minus-circle me-1"></i>سندات الدفع
                        </a>
                        <a href="{{ route('vouchers.index', ['type' => 'exp-payment']) }}"
                            class="btn btn-outline-warning {{ $type === 'exp-payment' ? 'active' : '' }}">
                            <i class="fas fa-credit-card me-1"></i>سندات المصاريف
                        </a>
                        <a href="{{ route('vouchers.index', ['type' => 'multi_payment']) }}"
                            class="btn btn-outline-info {{ $type === 'multi_payment' ? 'active' : '' }}">
                            <i class="fas fa-list-alt me-1"></i>دفع متعدد
                        </a>
                        <a href="{{ route('vouchers.index', ['type' => 'multi_receipt']) }}"
                            class="btn btn-outline-primary {{ $type === 'multi_receipt' ? 'active' : '' }}">
                            <i class="fas fa-list-ul me-1"></i>قبض متعدد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped mb-0" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>م</th>
                            <th>التاريخ</th>
                            <th>رقم العملية</th>
                            <th>نوع العملية</th>
                            <th>البيان</th>
                            <th>المبلغ</th>
                            <th>الحساب</th>
                            <th>الحساب المقابل</th>
                            <th>الموظف</th>
                            <th>المستخدم</th>
                            <th>تاريخ الإنشاء</th>
                            <th>ملاحظات</th>
                            <th>المراجعة</th>
                            @canany(['حذف السندات', 'تعديل السندات'])
                                <th>العمليات</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @php $x = 1; @endphp
                        @forelse ($vouchers as $index => $voucher)
                            <tr>
                                <td>{{ $x++ }}</td>
                                <td>{{ $voucher->pro_date }}</td>
                                <td>{{ $voucher->pro_id }}</td>
                                <td>
                                    <span
                                        class="badge
                                        @if ($voucher->pro_type == 1) bg-success
                                        @elseif($voucher->pro_type == 2) bg-danger
                                        @elseif($voucher->pro_type == 3) bg-warning
                                        @elseif($voucher->pro_type == 4) bg-info
                                        @elseif($voucher->pro_type == 5) bg-primary
                                        @else bg-secondary @endif">
                                        {{ $voucher->type->ptext ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td>{{ $voucher->details }}</td>
                                <td class="h5 fw-bold">{{ number_format($voucher->pro_value, 2) }}</td>
                                <td>{{ $voucher->account1->aname ?? '' }}</td>
                                <td>{{ $voucher->account2->aname ?? '' }}</td>
                                <td>{{ $voucher->emp1->aname ?? '' }}</td>
                                <td>{{ $voucher->user->name ?? '' }}</td>
                                <td>{{ $voucher->created_at ? $voucher->created_at->format('Y-m-d') : '' }}</td>
                                <td>{{ $voucher->notes ?? '' }}</td>
                                <td>
                                    <span class="badge {{ $voucher->is_approved ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $voucher->is_approved ? 'نعم' : 'لا' }}
                                    </span>
                                </td>
                                @canany(['حذف السندات', 'تعديل السندات'])
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('تعديل السندات')
                                                <a href="{{ route('vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning"
                                                    title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('حذف السندات')
                                                <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا السند؟')" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">
                                    <div class="alert alert-info py-4 mb-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>لا توجد {{ $currentTypeInfo['title'] }} حالياً</strong>
                                        <br>
                                        <small class="text-muted mt-2 d-block">
                                            يمكنك إضافة {{ strtolower($currentTypeInfo['create_text']) }} باستخدام الزر
                                            أعلاه
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (method_exists($vouchers, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $vouchers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activeFilter = '{{ $type }}';
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
@endpush
