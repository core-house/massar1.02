@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('أحجام المشاريع'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('أحجام المشاريع')],
        ],
    ])
    <div class="row">
        <div class="col-lg-12">
            {{-- @can('إضافة حجم المشروع') --}}
            <a href="{{ route('project-size.create') }}" class="btn btn-primary font-family-cairo fw-bold">
                إضافة جديد
                <i class="fas fa-plus me-2"></i>
            </a>
            {{-- @endcan --}}
            <br><br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x:auto;">
                        <x-table-export-actions table-id="project-size-table" filename="project-size-table"
                            excel-label="تصدير Excel" pdf-label="تصدير PDF" print-label="طباعة" />

                        <table id="project-size-table" class="table table-striped mb-0" style="min-width: 800px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('الاسم') }}</th>
                                    <th>{{ __('الوصف') }}</th>
                                    {{-- @canany(['تعديل حجم المشروع', 'حذف حجم المشروع']) --}}
                                    <th>{{ __('العمليات') }}</th>
                                    {{-- @endcanany --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projectSizes as $size)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $size->name }}</td>
                                        <td>{{ $size->description ?? '-' }}</td>
                                        {{-- @canany(['تعديل حجم المشروع', 'حذف حجم المشروع']) --}}
                                        <td>
                                            {{-- @can('تعديل حجم المشروع') --}}
                                            <a class="btn btn-success btn-icon-square-sm"
                                                href="{{ route('project-size.edit', $size->id) }}">
                                                <i class="las la-edit"></i>
                                            </a>
                                            {{-- @endcan

                                                @can('حذف حجم المشروع') --}}
                                            <form action="{{ route('project-size.destroy', $size->id) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                            <div class="alert alert-info py-3 mb-0">لا توجد بيانات</div>
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
