@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.project_sizes'),
        'breadcrumb_items' => [['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')], ['label' => __('inquiries::inquiries.project_sizes')]],
    ])

    <div class="row">
        <div class="col-lg-12">

            {{-- Add Button --}}
            @can('create Project Size')
                <a href="{{ route('project-size.create') }}" class="btn btn-main font-hold fw-bold">
                    {{ __('inquiries::inquiries.add_new') }}
                    <i class="fas fa-plus me-2"></i>
                </a>
            @endcan

            <br><br>

            <div class="card">
                <div class="card-body">
                    <x-inquiries::bulk-actions model="Modules\Inquiries\Models\ProjectSize" permission="delete Project Size">
                        <div class="table-responsive" style="overflow-x:auto;">

                        <x-table-export-actions table-id="project-size-table" filename="project-size-table"
                            excel-label="{{ __('inquiries::inquiries.export_excel') }}" pdf-label="{{ __('inquiries::inquiries.export_pdf') }}"
                            print-label="{{ __('inquiries::inquiries.print') }}" />

                        <table id="project-size-table" class="table table-striped mb-0" style="min-width: 800px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleAll">
                                    </th>
                                    <th>#</th>
                                    <th>{{ __('inquiries::inquiries.name') }}</th>
                                    <th>{{ __('inquiries::inquiries.description') }}</th>

                                    @canany(['edit Project Size', 'delete Project Size'])
                                        <th>{{ __('inquiries::inquiries.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projectSizes as $size)
                                    <tr class="text-center">
                                        <td>
                                            <input type="checkbox" class="form-check-input bulk-checkbox" 
                                                   value="{{ $size->id }}" x-model="selectedIds">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $size->name }}</td>
                                        <td>{{ $size->description ?? '-' }}</td>

                                        @canany(['edit Project Size', 'delete Project Size'])
                                            <td>
                                                @can('edit Project Size')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('project-size.edit', $size->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Project Size')
                                                    <form action="{{ route('project-size.destroy', $size->id) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('inquiries::inquiries.confirm_delete') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">
                                                {{ __('inquiries::inquiries.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </x-inquiries::bulk-actions>
                </div>
            </div>
        </div>
    </div>
@endsection
