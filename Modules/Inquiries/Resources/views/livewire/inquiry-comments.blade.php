{{-- resources/views/inquiries/livewire/inquiry-comments.blade.php --}}
<div>
    <div class="card border-primary">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="fas fa-comments me-2"></i>
                {{ __('inquiries::inquiries.comments_and_notes') }}
            </h6>
            <small class="d-block mt-1">{{ count($comments) }} {{ __('inquiries::inquiries.comments') }}</small>
        </div>
        <div class="card-body">
            @if (session()->has('comment_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('comment_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form لإضافة تعليق جديد -->
            <div class="mb-4">
                <form wire:submit.prevent="addComment">
                    <div class="mb-3">
                        <label for="newComment" class="form-label fw-bold">
                            <i class="fas fa-pen me-2"></i>
                            {{ __('inquiries::inquiries.add_new_comment') }}
                        </label>
                        <textarea wire:model="newComment" id="newComment" class="form-control @error('newComment') is-invalid @enderror"
                            rows="3" placeholder="{{ __('inquiries::inquiries.write_comment_here') }}"></textarea>
                        @error('newComment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ __('inquiries::inquiries.add_comment') }}
                    </button>
                </form>
            </div>

            <hr>

            <!-- عرض التعليقات -->
            <div class="comments-list">
                @forelse($comments as $comment)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-user-circle me-2 text-primary"></i>
                                        {{ $comment['user']['name'] ?? __('inquiries::inquiries.user') }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($comment['created_at'])->diffForHumans() }}
                                    </small>
                                </div>
                                @if ($comment['user_id'] === auth()->id() || auth()->user()->hasRole('admin'))
                                    <button wire:click="deleteComment({{ $comment['id'] }})"
                                        wire:confirm="{{ __('inquiries::inquiries.confirm_delete_comment') }}"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                            <p class="mb-0">{{ $comment['comment'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ __('inquiries::inquiries.no_comments_yet') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
