<div class="row g-4 mb-4">
    <!-- Work Items -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">{{ __('general.work_items') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $totalItems }}</h3>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary rounded-circle p-3">
                        <i class="fas fa-tasks fa-lg"></i>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min($overallProgress, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">{{ __('general.overall_progress') }}</p>
                        <h3 class="mb-0 fw-bold {{ $overallProgress >= 100 ? 'text-success' : 'text-primary' }}">
                            {{ number_format($overallProgress, 1) }}%
                        </h3>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success rounded-circle p-3">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min($overallProgress, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Days Passed -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">{{ __('general.days_passed') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $daysPassed }}</h3>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning rounded-circle p-3">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    @php
                        $daysTotal = $daysPassed + $daysRemaining;
                        $daysPercent = $daysTotal > 0 ? ($daysPassed / $daysTotal) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $daysPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Employees -->
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small text-uppercase fw-bold mb-1">{{ __('general.team_members') }}</p>
                        <h3 class="mb-0 fw-bold">{{ $totalEmployees }}</h3>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info rounded-circle p-3">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <!-- Avatar Stack Preview -->
                <div class="avatar-group mt-2">
                    @foreach($project->employees->take(4) as $employee)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name ?? 'User') }}&background=random&color=fff" 
                             class="rounded-circle border border-white" 
                             width="24" height="24" 
                             title="{{ $employee->name ?? 'User' }}"
                             alt="{{ $employee->name ?? 'User' }}">
                    @endforeach
                    @if($project->employees->count() > 4)
                        <span class="small text-muted ms-2">+{{ $project->employees->count() - 4 }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
