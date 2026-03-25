@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.quality')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => $sop->title,
        'breadcrumb_items' => [
            ['label' => __('Dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('sops::sops.sops'), 'url' => route('sops.index')],
            ['label' => $sop->title],
        ],
    ])

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $sop->title }}</h5>
                    <span class="badge bg-light text-dark">{{ __('sops::sops.version') }}: {{ $sop->version }}</span>
                </div>
                <div class="card-body">
                    <h6>{{ __('sops::sops.description') }}</h6>
                    <p>{{ $sop->description ?: __('No description') }}</p>
                    <hr>
                    <h6>{{ __('sops::sops.content') }}</h6>
                    <div class="sop-content p-3 bg-light border rounded">
                        {!! nl2br(e($sop->content)) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Details') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('sops::sops.category') }}
                            <span class="badge bg-primary">{{ $sop->category->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('sops::sops.status') }}
                            @if($sop->status === 'active')
                                <span class="badge bg-success">{{ __('sops::sops.active') }}</span>
                            @elseif($sop->status === 'draft')
                                <span class="badge bg-warning text-dark">{{ __('sops::sops.draft') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('sops::sops.archived') }}</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('Department') }}
                            <span>{{ $sop->department->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('sops::sops.created_by') }}
                            <span>{{ $sop->creator->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('Created At') }}
                            <span>{{ $sop->created_at->format('Y-m-d H:i') }}</span>
                        </li>
                    </ul>

                    @if($sop->attachment)
                        <div class="mt-4">
                            <a href="{{ asset('storage/' . $sop->attachment) }}" target="_blank" class="btn btn-main w-100">
                                <i class="las la-download me-1"></i> {{ __('sops::sops.attachment') }}
                            </a>
                        </div>
                    @endif

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('sops.edit', $sop->id) }}" class="btn btn-warning flex-grow-1">
                            <i class="las la-edit"></i> {{ __('sops::sops.edit') }}
                        </a>
                        <form action="{{ route('sops.destroy', $sop->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="las la-trash"></i> {{ __('sops::sops.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
