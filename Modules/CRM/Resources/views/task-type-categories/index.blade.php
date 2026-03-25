@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.tasks')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.task_types'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.task_types')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-end mb-3">
                @can('create Task Types')
                    <a href="{{ route('tasks.type-categories.create') }}" class="btn btn-main font-hold fw-bold">
                        <i class="fas fa-plus me-2"></i> {{ __('crm::crm.add_new') }}
                    </a>
                @endcan
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">

                        <x-table-export-actions table-id="task-type-categories-table" filename="task-type-categories" :excel-label="__('crm::crm.export_excel')"
                            :pdf-label="__('crm::crm.export_pdf')" :print-label="__('crm::crm.print')" />

                        <table id="task-type-categories-table" class="table table-striped text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.name') }}</th>
                                    <th>{{ __('crm::crm.description') }}</th>
                                    <th>{{ __('crm::crm.task_types') }}</th>
                                    @canany(['edit Task Types', 'delete Task Types'])
                                        <th>{{ __('crm::crm.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ Str::limit($category->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->taskTypes->count() }}</span>
                                        </td>
                                        @canany(['edit Task Types', 'delete Task Types'])
                                            <td>
                                                @can('edit Task Types')
                                                    <a href="{{ route('tasks.type-categories.edit', $category->id) }}"
                                                        class="btn btn-success btn-icon-square-sm">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Task Types')
                                                    <button type="button" class="btn btn-danger btn-icon-square-sm" onclick="deleteCategory({{ $category->id }})">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $category->id }}" action="{{ route('tasks.type-categories.destroy', $category->id) }}" method="POST" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_data_available') }}
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

    <script>
        function deleteCategory(categoryId) {
            Swal.fire({
                title: '{{ __('crm::crm.are_you_sure') }}',
                text: '{{ __('crm::crm.confirm_delete_category') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('crm::crm.delete') }}',
                cancelButtonText: '{{ __('crm::crm.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + categoryId).submit();
                }
            });
        }
    </script>
@endsection
