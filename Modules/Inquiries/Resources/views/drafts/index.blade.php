@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.drafts'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.home'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.drafts')],
        ],
    ])

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            {{ __('inquiries::inquiries.draft_inquiries') }}
                        </h2>
                    </div>
                    <div>
                        @can('create Inquiries')
                            <a href="{{ route('inquiries.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                {{ __('inquiries::inquiries.new_inquiry') }}
                            </a>
                        @endcan

                        @can('view Inquiries')
                            <a href="{{ route('inquiries.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>
                                {{ __('inquiries::inquiries.view_all_inquiries') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        @if ($drafts->count() > 0)
                            <x-inquiries::bulk-actions model="Modules\Inquiries\Models\Inquiry" permission="delete My Drafts">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>
                                                    <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleAll">
                                                </th>
                                                <th>{{ __('inquiries::inquiries.tender_no') }}</th>
                                                <th>{{ __('inquiries::inquiries.tender_id') }}</th>
                                                <th>{{ __('inquiries::inquiries.work_type') }}</th>
                                                <th>{{ __('inquiries::inquiries.city_town') }}</th>
                                                <th>{{ __('inquiries::inquiries.project_size') }}</th>
                                                <th>{{ __('inquiries::inquiries.created_at') }}</th>
                                                <th class="text-end">{{ __('inquiries::inquiries.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($drafts as $draft)
                                                <tr>
                                                    <td>
                                                        @can('delete My Drafts')
                                                            @if ($draft->created_by == auth()->id())
                                                                <input type="checkbox" class="form-check-input bulk-checkbox"
                                                                    value="{{ $draft->id }}" x-model="selectedIds">
                                                            @endif
                                                        @endcan
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ $draft->tender_number }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($draft->tender_id)
                                                            {{ \Illuminate\Support\Str::limit($draft->tender_id, 30) }}
                                                        @else
                                                            <span class="text-muted">{{ __('inquiries::inquiries.not_set') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($draft->final_work_type)
                                                            <small>{{ \Illuminate\Support\Str::limit($draft->final_work_type, 25) }}</small>
                                                        @else
                                                            <span class="text-muted">{{ __('inquiries::inquiries.not_selected') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($draft->city || $draft->town)
                                                            <small>
                                                                {{ $draft->city?->title }}
                                                                @if ($draft->town)
                                                                    <br>{{ $draft->town->title }}
                                                                @endif
                                                            </small>
                                                        @else
                                                            <span class="text-muted">{{ __('inquiries::inquiries.not_set') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($draft->projectSize)
                                                            <span class="badge bg-info">{{ $draft->projectSize->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <small>{{ $draft->created_at->format('Y-m-d') }}</small>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group" role="group">
                                                            @can('edit My Drafts')
                                                                <a href="{{ route('inquiries.drafts.edit', $draft->id) }}"
                                                                    class="btn btn-sm btn-primary"
                                                                    title="{{ __('inquiries::inquiries.continue_editing') }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endcan

                                                            @can('edit My Drafts')
                                                                <form action="{{ route('inquiries.drafts.publish', $draft->id) }}" method="POST"
                                                                    class="d-inline"
                                                                    onsubmit="return confirm('{{ __('inquiries::inquiries.confirm_publish_inquiry') }}');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-success"
                                                                        title="{{ __('inquiries::inquiries.publish_inquiry') }}">
                                                                        <i class="fas fa-check-circle"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan

                                                            @can('delete My Drafts')
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="confirmDelete({{ $draft->id }})"
                                                                    title="{{ __('inquiries::inquiries.delete_draft') }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endcan
                                                        </div>

                                                        @can('delete My Drafts')
                                                            <form id="delete-form-{{ $draft->id }}"
                                                                action="{{ route('inquiries.drafts.destroy', $draft->id) }}"
                                                                method="POST" class="d-none">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </x-inquiries::bulk-actions>

                            <div class="mt-3">
                                {{ $drafts->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">{{ __('inquiries::inquiries.no_drafts_found') }}</h4>
                                <p class="text-muted">{{ __('inquiries::inquiries.start_creating_inquiry') }}</p>

                                @can('Create Inquiries')
                                    <a href="{{ route('inquiries.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>
                                        {{ __('inquiries::inquiries.create_new_inquiry') }}
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(draftId) {
                if (confirm('{{ __('inquiries::inquiries.confirm_delete_draft') }}')) {
                    document.getElementById('delete-form-' + draftId).submit();
                }
            }
        </script>
    @endpush
@endsection
