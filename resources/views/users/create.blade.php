@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.permissions')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('إنشاء مستخدم'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('المستخدمين'), 'url' => route('users.index')],
            ['label' => __('إنشاء')],
        ],
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 mb-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body p-2">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                            <button class="nav-link active text-end" id="v-pills-home-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-home" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>
                                البيانات الاساسيه
                            </button>
                            <button class="nav-link text-end" id="v-pills-profile-tab" data-bs-toggle="pill"
                                data-bs-target="#v-pills-profile" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>
                                الصلاحيات
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-10 col-md-9">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- البيانات الأساسية -->
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 fw-bold text-primary">
                                        <i class="fas fa-user-plus me-2"></i>
                                        البيانات الأساسية
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- بيانات المستخدم -->
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">الاسم</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ old('name') }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">البريد الإلكتروني</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email') }}" required>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">كلمة المرور</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password" class="form-control"
                                                            id="password" required>
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            onclick="togglePassword('password', this)">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold">تأكيد كلمة المرور</label>
                                                    <div class="input-group mt-4">
                                                        <input type="password" name="password_confirmation"
                                                            class="form-control" id="password_confirmation" required>
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            onclick="togglePassword('password_confirmation', this)">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- الفروع -->
                                        <div class="col-lg-4">
                                            <div class="card bg-light border-0 h-100">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3 text-success">
                                                        <i class="fas fa-code-branch me-2"></i>
                                                        اختر الفروع
                                                    </h6>
                                                    <div class="row g-2">
                                                        @foreach ($branches as $branch)
                                                            <div class="col-12">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="branches[]" value="{{ $branch->id }}"
                                                                        id="branch_{{ $branch->id }}"
                                                                        {{ in_array($branch->id, old('branches', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="branch_{{ $branch->id }}">
                                                                        {{ $branch->name }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الصلاحيات -->
                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel">
                            @php use Illuminate\Support\Str; @endphp
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white border-bottom py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 fw-bold text-primary">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            إدارة الصلاحيات
                                        </h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label fw-semibold" for="selectAll">
                                                تحديد الكل
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- القائمة الجانبية للفئات -->
                                        <div class="col-lg-3 col-md-4 mb-3">
                                            <div class="list-group sticky-top" style="top: 20px;">
                                                @foreach ($permissions as $category => $perms)
                                                    <button type="button"
                                                        class="list-group-item list-group-item-action permission-category text-end {{ $loop->first ? 'active' : '' }}"
                                                        data-category="{{ Str::slug($category) }}">
                                                        <i class="fas fa-folder me-2"></i>
                                                        {{ $category }}
                                                        <span
                                                            class="badge bg-primary float-start">{{ $perms->count() }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- جداول الصلاحيات -->
                                        <div class="col-lg-9 col-md-8">
                                            <div class="permissions-container">
                                                @foreach ($permissions as $category => $perms)
                                                    @php
                                                        $grouped = [];
                                                        foreach ($perms as $perm) {
                                                            $parts = explode(' ', $perm->name, 2);
                                                            $action = $parts[0];
                                                            $target = $parts[1] ?? '';
                                                            $grouped[$target][$action] = $perm;
                                                        }
                                                    @endphp

                                                    <div class="permission-category-content {{ !$loop->first ? 'd-none' : '' }}"
                                                        id="{{ Str::slug($category) }}">
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-hover table-bordered text-center align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="text-start">الصلاحية</th>
                                                                        <th><i class="fas fa-eye text-primary"></i> عرض
                                                                        </th>
                                                                        <th><i class="fas fa-plus text-success"></i> إضافة
                                                                        </th>
                                                                        <th><i class="fas fa-edit text-warning"></i> تعديل
                                                                        </th>
                                                                        <th><i class="fas fa-trash text-danger"></i> حذف
                                                                        </th>
                                                                        <th><i class="fas fa-print text-info"></i> طباعة
                                                                        </th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($grouped as $title => $actions)
                                                                        <tr>
                                                                            <td class="text-start fw-semibold">
                                                                                {{ $title }}</td>
                                                                            @php
                                                                                $actionMap = [
                                                                                    'عرض' => ['عرض', 'View'],
                                                                                    'إضافة' => ['إضافة', 'Create'],
                                                                                    'تعديل' => ['تعديل', 'Edit'],
                                                                                    'حذف' => ['حذف', 'Delete'],
                                                                                    'طباعة' => ['طباعة', 'Print'],
                                                                                ];
                                                                            @endphp

                                                                            @foreach ($actionMap as $act => $aliases)
                                                                                <td>
                                                                                    @foreach ($aliases as $alias)
                                                                                        @if (isset($actions[$alias]))
                                                                                            <input type="checkbox"
                                                                                                class="form-check-input"
                                                                                                name="permissions[]"
                                                                                                value="{{ $actions[$alias]->id }}"
                                                                                                {{ in_array($actions[$alias]->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                                            @break
                                                                                        @endif
                                                                                    @endforeach
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الحفظ -->
                    <div class="card shadow-sm border-0 mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> حفظ
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-danger px-4">
                                    <i class="fas fa-times me-1"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .permission-category {
            cursor: pointer;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
        }

        .permission-category:hover {
            background-color: #f8f9fa;
            border-right-color: #0d6efd;
        }

        .permission-category.active {
            background-color: #0d6efd;
            color: white;
            border-right-color: #0d6efd;
        }

        .permission-category.active .badge {
            background-color: white !important;
            color: #0d6efd !important;
        }

        .permissions-container {
            max-height: 600px;
            overflow-y: auto;
        }

        .table th {
            white-space: nowrap;
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .sticky-top {
            position: sticky;
            z-index: 1020;
        }

        .nav-pills .nav-link {
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link:hover {
            background-color: #f8f9fa;
        }

        .nav-pills .nav-link.active {
            background-color: #0d6efd;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');

            // Select All - يعمل على الفئة النشطة فقط
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const activeCategory = document.querySelector(
                        '.permission-category-content:not(.d-none)');
                    if (activeCategory) {
                        activeCategory.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                            cb.checked = this.checked;
                        });
                    }
                });
            }

            // التبديل بين الفئات
            document.querySelectorAll('.permission-category').forEach(tab => {
                tab.addEventListener('click', function() {
                    // إزالة active من الكل
                    document.querySelectorAll('.permission-category').forEach(t =>
                        t.classList.remove('active')
                    );

                    // إضافة active للعنصر المختار
                    this.classList.add('active');

                    // إخفاء كل المحتوى
                    const categoryId = this.getAttribute('data-category');
                    document.querySelectorAll('.permission-category-content').forEach(content => {
                        content.classList.add('d-none');
                    });

                    // إظهار المحتوى المطلوب
                    const activeContent = document.getElementById(categoryId);
                    if (activeContent) {
                        activeContent.classList.remove('d-none');
                    }

                    // إعادة تعيين Select All
                    if (selectAll) {
                        const allChecked = activeContent &&
                            Array.from(activeContent.querySelectorAll('input[type="checkbox"]'))
                            .every(cb => cb.checked);
                        selectAll.checked = allChecked;
                    }
                });
            });

            // تحديث حالة Select All عند تغيير أي checkbox
            document.querySelectorAll('.permission-category-content').forEach(container => {
                container.addEventListener('change', function(e) {
                    if (e.target.type === 'checkbox' && e.target.id !== 'selectAll') {
                        const allChecked = Array.from(
                            this.querySelectorAll('input[type="checkbox"]')
                        ).every(cb => cb.checked);

                        if (selectAll) {
                            selectAll.checked = allChecked;
                        }
                    }
                });
            });
        });
    </script>
@endpush
