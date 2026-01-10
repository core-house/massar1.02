<div class="card border-0 shadow-sm mb-4" x-data="{ expanded: false }">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center"
         @click="expanded = !expanded" 
         style="cursor: pointer;">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-users-cog me-2"></i>{{ __('general.team_performance') }}
        </h5>
        <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </div>
    <div class="card-body p-0" x-show="expanded" x-collapse style="display: none;">
        <div class="list-group list-group-flush">
            @forelse($teamPerformance as $user)
                <div class="list-group-item px-3 py-3">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" 
                             class="rounded-circle me-3" 
                             width="32" height="32">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold small">{{ $user->name }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">{{ $user->position ?? 'User' }}</small>
                        </div>
                         <div class="text-end">
                            <span class="fw-bold small text-primary">{{ number_format($user->project_total_quantity ?? 0) }}</span>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ __('general.units') }}</small>
                        </div>
                    </div>
                    @php
                        // Estimate performance for UI visualization (avoid div by zero)
                        $barWidth = isset($user->performance_percentage) ? $user->performance_percentage : 0;
                    @endphp
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $barWidth }}%"></div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    {{ __('general.no_team_members') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
