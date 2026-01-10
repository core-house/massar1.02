<div class="card border-0 shadow-sm mb-4" x-data="{ expanded: false }">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center" 
         @click="expanded = !expanded" 
         style="cursor: pointer;">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-history me-2"></i>{{ __('general.recent_activity') }}
        </h5>
        <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </div>
    <div class="card-body p-0" x-show="expanded" x-collapse style="display: none;">
        <div class="list-group list-group-flush">
            @forelse($recentProgress as $date => $items)
                <div class="list-group-item bg-light fw-bold py-2 small text-uppercase">
                   <i class="far fa-calendar-alt me-2"></i> {{ $date }}
                </div>
                @foreach($items as $progress)
                    <div class="list-group-item px-3 py-3 border-start border-4 border-{{ $loop->even ? 'info' : 'primary' }} ps-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="mb-1 text-dark fw-bold">
                                    {{ $progress->projectItem->workItem->name ?? __('general.unknown_item') }}
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-user-circle me-1"></i> 
                                    {{ $progress->user->name ?? __('general.unknown_user') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success fs-6">
                                    +{{ number_format($progress->quantity) }} 
                                    <small>{{ $progress->projectItem->workItem->unit ?? '' }}</small>
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="text-center py-4 text-muted">
                    {{ __('general.no_recent_activity') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
