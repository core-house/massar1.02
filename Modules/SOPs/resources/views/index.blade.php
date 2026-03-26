@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('sops::sops.sops'),
        'breadcrumb_items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('sops::sops.sops')],
        ],
    ])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('sops::sops.sops') }}</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('sop-categories.index') }}" class="btn btn-outline-primary">
                                <i class="las la-tags me-1"></i>
                                {{ __('sops::sops.categories') }}
                            </a>
                            <a href="{{ route('sops.create') }}" class="btn btn-main">
                                <i class="las la-plus me-1"></i>
                                {{ __('sops::sops.add_new') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" x-data="sopFilter()">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('sops::sops.title') }}</th>
                                    <th>{{ __('sops::sops.category') }}</th>
                                    <th>{{ __('sops::sops.version') }}</th>
                                    <th>{{ __('sops::sops.status') }}</th>
                                    <th>{{ __('sops::sops.created_by') }}</th>
                                    <th class="text-center">{{ __('sops::sops.actions') }}</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th><input type="text" x-model="filters.title" class="form-control form-control-sm" placeholder="{{ __('sops::sops.title') }}"></th>
                                    <th>
                                        <select x-model="filters.category" class="form-select form-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            <template x-for="cat in categories" :key="cat">
                                                <option :value="cat" x-text="cat"></option>
                                            </template>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th>
                                        <select x-model="filters.status" class="form-select form-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            <option value="draft">{{ __('sops::sops.draft') }}</option>
                                            <option value="active">{{ __('sops::sops.active') }}</option>
                                            <option value="archived">{{ __('sops::sops.archived') }}</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(sop, index) in filteredSops" :key="sop.id">
                                    <tr>
                                        <td x-text="index + 1"></td>
                                        <td x-text="sop.title"></td>
                                        <td>
                                            <span class="badge bg-light text-dark" x-text="sop.category"></span>
                                        </td>
                                        <td x-text="sop.version"></td>
                                        <td>
                                            <template x-if="sop.status === 'active'">
                                                <span class="badge bg-success">{{ __('sops::sops.active') }}</span>
                                            </template>
                                            <template x-if="sop.status === 'draft'">
                                                <span class="badge bg-warning text-dark">{{ __('sops::sops.draft') }}</span>
                                            </template>
                                            <template x-if="sop.status === 'archived'">
                                                <span class="badge bg-danger">{{ __('sops::sops.archived') }}</span>
                                            </template>
                                        </td>
                                        <td x-text="sop.creator"></td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a :href="`{{ url('sops') }}/${sop.id}`" class="btn btn-info btn-sm">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a :href="`{{ url('sops') }}/${sop.id}/edit`" class="btn btn-warning btn-sm">
                                                    <i class="las la-edit"></i>
                                                </a>
                                                <form :action="`{{ url('sops') }}/${sop.id}`" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="las la-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredSops.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            {{ __('No results found') }}
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sopFilter() {
            return {
                sops: @json($sops->map(fn($s) => [
                    'id' => $s->id,
                    'title' => $s->title,
                    'category' => $s->category->name ?? '',
                    'version' => $s->version,
                    'status' => $s->status,
                    'creator' => $s->creator->aname ?? $s->creator->name ?? 'N/A'
                ])),
                filters: {
                    title: '',
                    category: '',
                    status: ''
                },
                get categories() {
                    return [...new Set(this.sops.map(s => s.category).filter(c => c))];
                },
                get filteredSops() {
                    return this.sops.filter(sop => {
                        return (!this.filters.title || sop.title.toLowerCase().includes(this.filters.title.toLowerCase())) &&
                               (!this.filters.category || sop.category === this.filters.category) &&
                               (!this.filters.status || sop.status === this.filters.status);
                    });
                }
            }
        }
    </script>
@endsection
