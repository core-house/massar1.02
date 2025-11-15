@extends('admin.dashboard')

@section('title', 'إدارة الشيكات')

{{-- Dynamic Sidebar: نعرض فقط الشيكات والحسابات --}}
@section('sidebar')
    @include('components.sidebar.checks')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- رسائل النجاح والأخطاء -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <!-- Header كبير وواضح -->
                <div class="card-header bg-gradient-{{ $pageType === 'incoming' ? 'success' : 'danger' }} text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-white text-{{ $pageType === 'incoming' ? 'success' : 'danger' }} rounded-circle p-3 me-3">
                                <i class="fas fa-{{ $pageType === 'incoming' ? 'arrow-circle-down' : 'arrow-circle-up' }} fa-2x"></i>
                            </div>
                            <div>
                                <h1 class="mb-1 fw-bold header-title text-white">{{ $pageTitle }}</h1>
                                <p class="mb-0 text-white-75 header-subtitle">

                                </p>
                            </div>
                        </div>
                        <!-- التبديل بين الأنواع -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('checks.incoming') }}" class="btn btn-{{ $pageType === 'incoming' ? 'light' : 'outline-light' }} btn-lg">
                                <i class="fas fa-arrow-circle-down me-2"></i> أوراق القبض
                            </a>
                            <a href="{{ route('checks.outgoing') }}" class="btn btn-{{ $pageType === 'outgoing' ? 'light' : 'outline-light' }} btn-lg">
                                <i class="fas fa-arrow-circle-up me-2"></i> أوراق الدفع
                            </a>
                        </div>
                    </div>
                </div>

                <!-- شريط العمليات - أزرار كبيرة وموحدة -->
                <div class="card-body border-bottom bg-light py-4">
                    <div class="row g-3">
                        <div class="col-auto">
                            <a href="{{ route('checks.' . $pageType . '.create') }}" class="btn btn-lg btn-light shadow-sm px-4 py-3">
                                <i class="fas fa-plus fa-lg text-success me-2"></i>
                                <span class="fw-bold">إضافة {{ $pageType === 'incoming' ? 'ورقة قبض' : 'ورقة دفع' }}</span>
                            </a>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="openSelectedCheck()">
                                <i class="fas fa-folder-open fa-lg text-info me-2"></i>
                                <span class="fw-bold">فتح الورقة المختارة</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="collectSelected()">
                                <i class="fas fa-check-circle fa-lg text-primary me-2"></i>
                                <span class="fw-bold">تحصيل الأوراق المحددة</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="endorseSelected()">
                                <i class="fas fa-exchange-alt fa-lg text-warning me-2"></i>
                                <span class="fw-bold">تظهير الأوراق</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="cancelWithReversalEntry()">
                                <i class="fas fa-ban fa-lg text-danger me-2"></i>
                                <span class="fw-bold">إلغاء بقيد عكسي</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="exportToExcel()">
                                <i class="fas fa-file-excel fa-lg text-success me-2"></i>
                                <span class="fw-bold">تصدير Excel</span>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-lg btn-light shadow-sm px-4 py-3" onclick="window.print()">
                                <i class="fas fa-print fa-lg text-secondary me-2"></i>
                                <span class="fw-bold">طباعة</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- فلاتر البحث -->
                <div class="card-body">
                    <form method="GET" action="{{ route('checks.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">البحث</label>
                                <input type="text" name="search" class="form-control"
                                       placeholder="رقم الشيك، البنك، أو الاسم..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">حالة الشيك</label>
                                <select name="status" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                                    <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>مصفى</option>
                                    <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>مرتد</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">من تاريخ</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">إلى تاريخ</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-1 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- جدول الشيكات -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="checksTable">
                            <thead class="">
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>رقم الشيك</th>
                                    <th>البنك</th>
                                    <th>رقم الحساب</th>
                                    <th>المبلغ</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الحالة</th>
                                    <th>النوع</th>
                                    <th>صاحب الحساب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($checks as $check)
                                    <tr class="{{ $check->isOverdue() ? 'table-warning' : '' }}" data-check-id="{{ $check->id }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input check-item" value="{{ $check->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $check->check_number }}</strong>
                                            @if($check->reference_number)
                                                <br><small class="text-muted">مرجع: {{ $check->reference_number }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $check->bank_name }}
                                        </td>
                                        <td>{{ $check->account_number }}</td>
                                        <td><strong class="text-primary">{{ number_format($check->amount, 2) }} ر.س</strong></td>
                                        <td>
                                            {{ $check->due_date->format('Y-m-d') }}
                                            @if($check->isOverdue())
                                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> متأخر</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $check->status_color }}">
                                                @if($check->status == 'pending') معلق
                                                @elseif($check->status == 'cleared') مصفى
                                                @elseif($check->status == 'bounced') مرتد
                                                @else ملغى
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $check->type === 'incoming' ? 'success' : 'info' }}">
                                                {{ $check->type === 'incoming' ? 'قبض' : 'دفع' }}
                                            </span>
                                        </td>
                                        <td>{{ $check->account_holder_name }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" onclick="viewCheck({{ $check->id }})" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" onclick="editCheck({{ $check->id }})" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($check->status == 'pending')
                                                    <button class="btn btn-outline-success" onclick="clearCheck({{ $check->id }})" title="تصفية">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-danger" onclick="deleteCheck({{ $check->id }})" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">لا توجد شيكات مطابقة للبحث</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- التذييل مع المعلومات والترقيم -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div><strong>عدد السجلات:</strong> <span class="badge bg-primary">{{ $checks->total() }}</span></div>
                                <div><strong>المحدد:</strong> <span id="selectedCount" class="badge bg-info">0</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{ $checks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('checks::modals.add-edit')
@include('checks::modals.view')
@include('checks::modals.collect')
@endsection

@push('styles')
<style>
/* تحسينات التصميم */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.icon-shape {
    width: 60px;
    height: 60px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.header-title {
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-subtitle {
    font-family: 'Cairo', 'Segoe UI', Tahoma, sans-serif;
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.9;
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.btn-light {
    background-color: #ffffff;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
    border-color: #dee2e6;
}

.btn-light:active {
    transform: translateY(0);
}

.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border: none;
}

.table-dark th {
    background-color: #2d3748;
    border-color: #2d3748;
    font-weight: 600;
}

.shadow-sm {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.check-item').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox
    $('.check-item').on('change', function() {
        updateSelectedCount();
        $('#selectAll').prop('checked', $('.check-item:checked').length === $('.check-item').length);
    });

    function updateSelectedCount() {
        const count = $('.check-item:checked').length;
        $('#selectedCount').text(count);
    }
});

function openAddModal() {
    // سيتم تنفيذها في ملف المودال
    $('#addEditModal').modal('show');
    $('#modalTitle').text('إضافة ورقة جديدة');
    $('#checkForm')[0].reset();
    $('#checkId').val('');
}

function editCheck(id) {
    // Load check data and open modal
    $.get(`/checks/${id}/edit`, function(data) {
        // Fill form with data
        $('#addEditModal').modal('show');
        $('#modalTitle').text('تعديل الورقة');
        // Fill fields...
    });
}

function viewCheck(id) {
    $.get(`/checks/${id}`, function(data) {
        $('#viewModal').modal('show');
        // Display check details
    });
}

function clearCheck(id) {
    // فتح modal التحصيل لشيك واحد
    $('#selectedChecksInfo').html(`
        <div class="alert alert-success">
            <strong>عدد الشيكات المحددة:</strong> 1
        </div>
    `);

    $('#collectModal').modal('show');

    $('#collectForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            _token: '{{ csrf_token() }}',
            bank_account_id: $('#bank_account_id').val(),
            collection_date: $('#collection_date').val(),
            branch_id: $('input[name="branch_id"]').val()
        };

        $.post(`/checks/${id}/clear`, formData, function(response) {
            $('#collectModal').modal('hide');
            if(response.success) {
                location.reload();
            } else {
                alert(response.message || 'حدث خطأ');
            }
        }).fail(function(xhr) {
            alert('حدث خطأ: ' + (xhr.responseJSON?.message || 'خطأ في الاتصال'));
        });
    });
}

function deleteCheck(id) {
    if(confirm('هل أنت متأكد من حذف هذا الشيك؟ لا يمكن التراجع عن هذا الإجراء.')) {
        $.ajax({
            url: `/checks/${id}`,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function() {
                location.reload();
            }
        });
    }
}

function openSelectedCheck() {
    const selected = $('.check-item:checked');
    if(selected.length === 0) {
        alert('يرجى اختيار شيك واحد على الأقل');
        return;
    }
    if(selected.length > 1) {
        alert('يرجى اختيار شيك واحد فقط');
        return;
    }
    viewCheck(selected.val());
}

function collectSelected() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('يرجى اختيار شيكات أولاً');
        return;
    }

    if(confirm(`هل أنت متأكد من تحصيل ${selected.length} شيك؟`)) {
        $.post('/checks/batch-collect', {
            _token: '{{ csrf_token() }}',
            ids: selected
        }, function() {
            location.reload();
        });
    }
}

function endorseSelected() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('يرجى اختيار شيكات أولاً');
        return;
    }

    alert('عملية التظهير قيد التطوير');
}

function cancelWithReversalEntry() {
    const selected = $('.check-item:checked').map(function() {
        return this.value;
    }).get();

    if(selected.length === 0) {
        alert('يرجى اختيار شيكات أولاً');
        return;
    }

    if(confirm(`هل أنت متأكد من إلغاء ${selected.length} شيك بقيد عكسي؟`)) {
        $.post('/checks/batch-cancel-reversal', {
            _token: '{{ csrf_token() }}',
            ids: selected
        }, function() {
            location.reload();
        });
    }
}

function exportToExcel() {
    window.location.href = '/checks/export?' + $('#filterForm').serialize();
}
</script>
@endpush
