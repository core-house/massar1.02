@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('الأدوار (Inquiries Roles)'),
        'items' => [['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')], ['label' => __('الأدوار')]],
    ])

    <div class="row">
        <div class="col-lg-12">
            {{-- @can('إضافة الأدوار') --}}
                <a href="{{ route('inquiries-roles.create') }}" type="button" class="btn btn-primary font-family-cairo fw-bold">
                    اضافه جديده
                    <i class="fas fa-plus me-2"></i>
                </a>
            {{-- @endcan --}}

            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <x-table-export-actions table-id="inquiries-roles-table" filename="inquiries-roles-table"
                            excel-label="تصدير Excel" pdf-label="تصدير PDF" print-label="طباعة" />

                        <table id="inquiries-roles-table" class="table table-striped mb-0" style="min-width: 1000px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('الاسم') }}</th>
                                    <th>{{ __('الوصف') }}</th>
                                    @canany(['تعديل الأدوار', 'حذف الأدوار'])
                                        <th>{{ __('العمليات') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->description ?? '-' }}</td>

                                        {{-- @canany(['تعديل الأدوار', 'حذف الأدوار']) --}}
                                            <td>
                                                {{-- @can('تعديل الأدوار') --}}
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('inquiries-roles.edit', $role->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                {{-- @endcan --}}

                                                {{-- @can('حذف الأدوار') --}}
                                                    <form action="{{ route('inquiries-roles.destroy', $role->id) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                {{-- @endcan --}}
                                            </td>
                                        {{-- @endcanany --}}
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                لا توجد بيانات
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
@endsection
