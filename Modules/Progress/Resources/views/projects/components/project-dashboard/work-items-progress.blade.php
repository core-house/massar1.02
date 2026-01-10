<div class="card stat-card border-0 shadow-sm mb-4" x-data="{ expanded: false }">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center"
         @click="expanded = !expanded" 
         style="cursor: pointer;">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-list-alt me-2"></i>{{ __('general.work_items_progress') }}
        </h5>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill">{{ count($project->items) }}</span>
            <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </div>
    </div>
    <div class="card-body bg-light" x-show="expanded" x-collapse style="display: none;">
        <div class="row g-3">
            @forelse($project->items as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title text-dark fw-bold mb-0 text-truncate" title="{{ $item->workItem->name ?? 'Unknown' }}" style="max-width: 70%;">
                                    {{ $item->workItem->name ?? 'Unknown' }}
                                </h6>
                                @if($item->status)
                                    <span class="badge" 
                                          style="background-color: {{ $item->status->color }}; color: #fff; font-size: 0.7rem;">
                                        <i class="{{ $item->status->icon }} me-1"></i>
                                        {{ $item->status->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">No Status</span>
                                @endif
                            </div>
                            
                            <div class="small text-muted mb-2">
                                <i class="fas fa-layer-group me-1"></i> {{ $item->subproject_name ?? 'Main Project' }}
                            </div>

                            <div class="small text-muted mb-3 d-flex align-items-center gap-2">
                                <span><i class="far fa-calendar-alt me-1"></i> {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : '--' }}</span>
                                <i class="fas fa-arrow-right text-muted" style="font-size: 0.8rem;"></i>
                                <span>{{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') : '--' }}</span>
                            </div>

                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span class="text-dark">{{ __('general.progress') }}</span>
                                <span class="{{ $item->completion_percentage >= 100 ? 'text-success' : 'text-primary' }}">
                                    {{ round($item->completion_percentage, 1) }}%
                                </span>
                            </div>
                            
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar {{ $item->completion_percentage >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $item->completion_percentage }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span>
                                    <strong class="text-dark">{{ round($item->completed_quantity, 1) }}</strong> / {{ round($item->total_quantity, 1) }}
                                    <span class="ms-1">{{ $item->workItem->unit ?? '' }}</span>
                                </span>
                                @if($item->daily_quantity)
                                    <span>Daily: {{ round($item->daily_quantity, 1) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="las la-inbox fa-3x mb-3"></i>
                    <p>{{ __('general.no_items_found') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
