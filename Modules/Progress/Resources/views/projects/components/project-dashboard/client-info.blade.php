<div class="card border-0 shadow-sm mb-4" x-data="{ expanded: false }">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center"
         @click="expanded = !expanded" 
         style="cursor: pointer;">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-user-tie me-2"></i>{{ __('general.client_info') }}
        </h5>
        <i class="fas" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </div>
    <div class="card-body" x-show="expanded" x-collapse style="display: none;">
        <div class="text-center mb-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($project->client->name ?? 'Unknown') }}&size=128&background=primary&color=fff" 
                 class="rounded-circle mb-3 shadow-sm" 
                 width="80" height="80" 
                 alt="{{ $project->client->name ?? 'Unknown Client' }}">
            <h5 class="fw-bold mb-1">{{ $project->client->name ?? __('general.unknown_client') }}</h5>
            <p class="text-muted small mb-0">{{ $project->client->contact_person ?? '' }}</p>
        </div>
        
        <div class="list-group list-group-flush">
            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-envelope me-2"></i>{{ __('general.email') }}
                </span>
                <span class="fw-medium small">{{ $project->client->email ?? 'N/A' }}</span>
            </div>
            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-phone me-2"></i>{{ __('general.phone') }}
                </span>
                <span class="fw-medium small" dir="ltr">{{ $project->client->phone ?? 'N/A' }}</span>
            </div>
            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-map-marker-alt me-2"></i>{{ __('general.address') }}
                </span>
                <span class="fw-medium small text-truncate" style="max-width: 150px;" title="{{ $project->client->address ?? '' }}">
                    {{ $project->client->address ?? 'N/A' }}
                </span>
            </div>
             <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="fas fa-project-diagram me-2"></i>{{ __('general.projects') }}
                </span>
                <span class="badge bg-primary rounded-pill">{{ $project->client->projects_count ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>
