@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.ticket_details'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.tickets'), 'url' => route('tickets.index')],
            ['label' => __('crm::crm.details')],
        ],
    ])

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Ticket Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    {{-- رقم التذكرة المرجعي --}}
                    @if ($ticket->ticket_number)
                        <div class="ticket-number-banner d-flex align-items-center gap-2 mb-3 p-2 bg-white rounded border border-primary border-opacity-25">
                            <i class="las la-ticket-alt text-primary fs-5"></i>
                            <span class="text-muted small">{{ __('crm::crm.ticket_number') }}</span>
                            <span class="fw-bold text-primary font-monospace fs-6 ms-1">{{ $ticket->ticket_number }}</span>
                            <button type="button"
                                class="btn btn-sm btn-outline-secondary ms-auto py-0 px-2"
                                onclick="navigator.clipboard.writeText('{{ $ticket->ticket_number }}')"
                                title="{{ __('crm::crm.copy') }}">
                                <i class="las la-copy"></i>
                            </button>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $ticket->subject }}</h4>
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ __('crm::crm.created_at') }}: {{ $ticket->created_at->format('Y-m-d H:i') }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'warning' : 'info') }} fs-6">
                            {{ __('crm::crm.' . $ticket->priority) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user fa-2x text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.client') }}</small>
                                    <strong>{{ $ticket->client->cname ?? __('crm::crm.na') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user-tie fa-2x text-success me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.assigned_to') }}</small>
                                    <strong>{{ $ticket->assignedTo->name ?? __('crm::crm.unassigned') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-user-plus fa-2x text-info me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.created_by') }}</small>
                                    <strong>{{ $ticket->createdBy->name ?? __('crm::crm.na') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-info-circle fa-2x text-warning me-3"></i>
                                <div>
                                    <small class="text-muted d-block">{{ __('crm::crm.status') }}</small>
                                    <span class="badge bg-{{ $ticket->status === 'open' ? 'primary' : ($ticket->status === 'in_progress' ? 'warning' : ($ticket->status === 'resolved' ? 'success' : 'secondary')) }}">
                                        {{ __('crm::crm.' . $ticket->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if ($ticket->ticket_type)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-tag fa-2x text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('crm::crm.ticket_type') }}</small>
                                        <strong>{{ $ticket->ticket_type }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($ticket->ticket_reference)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-hashtag fa-2x text-success me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('crm::crm.ticket_reference') }}</small>
                                        <strong>{{ $ticket->ticket_reference }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($ticket->status_title)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-info-circle fa-2x text-info me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">{{ __('crm::crm.status_title') }}</small>
                                        <strong>{{ $ticket->status_title }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-2">{{ __('crm::crm.description') }}</h6>
                        <p class="text-muted">{{ $ticket->description }}</p>
                    </div>

                    @can('edit Tickets')
                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary">
                                <i class="las la-edit me-1"></i> {{ __('crm::crm.edit_ticket') }}
                            </a>
                            @can('delete Tickets')
                                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                                    onsubmit="return confirm('{{ __('crm::crm.confirm_delete_ticket') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="las la-trash me-1"></i> {{ __('crm::crm.delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="far fa-comments me-2 text-primary"></i>
                        {{ __('crm::crm.comments') }}
                        <span class="badge bg-secondary ms-2">{{ $ticket->comments->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @forelse ($ticket->comments as $comment)
                        <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="avatar-circle bg-primary text-white">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $comment->user->name }}</strong>
                                        <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="far fa-comment-dots text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2">{{ __('crm::crm.no_comments_yet') }}</p>
                        </div>
                    @endforelse

                    @can('edit Tickets')
                        <hr>
                        <form action="{{ route('tickets.addComment', $ticket) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="form-label fw-bold">{{ __('crm::crm.add_comment') }}</label>
                                <textarea name="comment" id="comment" class="form-control" rows="3" 
                                          placeholder="{{ __('crm::crm.write_comment_placeholder') }}" required></textarea>
                                @error('comment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="las la-comment me-1"></i> {{ __('crm::crm.add_comment') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-cog me-2 text-primary"></i>
                        {{ __('crm::crm.update_status') }}
                    </h5>
                </div>
                <div class="card-body">
                    @can('edit Tickets')
                        <form action="{{ route('tickets.updateStatus') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <div class="mb-3">
                                <label for="new_status" class="form-label">{{ __('crm::crm.new_status') }}</label>
                                <select name="new_status" id="new_status" class="form-select" required>
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>
                                        {{ __('crm::crm.open') }}
                                    </option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>
                                        {{ __('crm::crm.in_progress') }}
                                    </option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>
                                        {{ __('crm::crm.resolved') }}
                                    </option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>
                                        {{ __('crm::crm.closed') }}
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="las la-sync me-1"></i> {{ __('crm::crm.update_status') }}
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }
    </style>
@endsection
