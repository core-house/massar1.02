@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('sops::sops.categories'),
        'breadcrumb_items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('sops::sops.sops'), 'url' => route('sops.index')],
            ['label' => __('sops::sops.categories')],
        ],
    ])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row" x-data="{ 
        isOpen: false, 
        isEdit: false,
        category: { id: '', name: '', description: '', is_active: 1 },
        openModal(cat = null) {
            if (cat) {
                this.isEdit = true;
                this.category = { ...cat };
                this.formUrl = '{{ url('sop-categories') }}/' + cat.id;
            } else {
                this.isEdit = false;
                this.category = { id: '', name: '', description: '', is_active: 1 };
                this.formUrl = '{{ route('sop-categories.store') }}';
            }
            this.isOpen = true;
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        }
    }">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">{{ __('sops::sops.categories') }}</h5>
                        <button @click="openModal()" class="btn btn-main">
                            <i class="las la-plus me-1"></i>
                            {{ __('sops::sops.add_new') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('sops::sops.category_name') }}</th>
                                    <th>{{ __('sops::sops.description') }}</th>
                                    <th>{{ __('sops::sops.status') }}</th>
                                    <th class="text-center">{{ __('sops::sops.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $index => $cat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $cat->name }}</td>
                                        <td>{{ $cat->description }}</td>
                                        <td>
                                            @if($cat->is_active)
                                                <span class="badge bg-success">{{ __('sops::sops.active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button @click="openModal(@json($cat))" class="btn btn-warning btn-sm">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <form action="{{ route('sop-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Modal -->
        <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" x-cloak>
            <div class="modal-dialog">
                <div class="modal-content">
                    <form :action="isEdit ? '{{ url('sop-categories') }}/' + category.id : '{{ route('sop-categories.store') }}'" method="POST">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <div class="modal-header">
                            <h5 class="modal-title" x-text="isEdit ? '{{ __('sops::sops.edit') }}' : '{{ __('sops::sops.add_new') }}'"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">{{ __('sops::sops.category_name') }}</label>
                                <input type="text" name="name" class="form-control" x-model="category.name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('sops::sops.description') }}</label>
                                <textarea name="description" class="form-control" x-model="category.description" rows="3"></textarea>
                            </div>
                            <div class="mb-3" x-show="isEdit">
                                <label class="form-label">{{ __('sops::sops.status') }}</label>
                                <select name="is_active" class="form-select" x-model="category.is_active">
                                    <option value="1">{{ __('sops::sops.active') }}</option>
                                    <option value="0">{{ __('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('sops::sops.cancel') }}</button>
                            <button type="submit" class="btn btn-main">{{ __('sops::sops.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
