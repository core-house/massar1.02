@canany(['view Users', 'view roles', 'create Users'])
    <div class="sidebar-header mb-3">
        <h6 class="text-muted fw-bold px-3 mb-2">
            <i class="fas fa-users-cog me-2"></i>
            {{ trans_str('user management and permissions') }}
        </h6>
    </div>
@endcanany

{{-- User Management --}}
@can(abilities: 'view Users')
    <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <a class="nav-link " href="{{ route('users.index') }}">
            <i class="fas fa-users"></i>
            <span class="">{{ trans_str('users') }}</span>
            @can('create Users')
                <span class="badge bg-primary ms-auto">{{ trans_str('manage') }}</span>
            @endcan
        </a>
    </li>
@endcan

{{-- Add New User --}}
@can('create Users')
    <li class="nav-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.create') }}">
            <i class="fas fa-user-plus"></i>
            <span>{{ trans_str('add user') }}</span>
        </a>
    </li>
@endcan

<li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('roles.index') }}">
        <i class="fas fa-user-shield"></i>
        <span>{{ trans_str('roles and permissions') }}</span>
        @can('create Roles')
            <span class="badge bg-success ms-auto">{{ trans_str('manage') }}</span>
        @endcan
    </a>
</li>

<hr class="my-3 border-secondary">
<div class="sidebar-header mb-2">
    <h6 class="text-muted fw-bold px-3 mb-2">
        <i class="fas fa-code-branch me-2"></i>
        {{ trans_str('branch management') }}
    </h6>
</div>

<li class="nav-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('branches.index') }}">
        <i class="fas fa-building"></i>
        <span>{{ trans_str('branches') }}</span>
    </a>
</li>

{{-- System Settings --}}

<li class="nav-item {{ request()->routeIs('mysettings.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('mysettings.index') }}">
        <i class="fas fa-cogs"></i>
        <span>{{ trans_str('system settings') }}</span>
        <span class="badge bg-warning ms-auto">{{ trans_str('advanced') }}</span>
    </a>
</li>



<hr class="my-3 border-secondary">

<div class="sidebar-header mb-2">
    <h6 class="text-muted fw-bold px-3 mb-2">
        <i class="fas fa-history me-2"></i>
        {{ trans_str('logs and monitoring') }}
    </h6>
</div>

{{-- Login History --}}

<li class="nav-item {{ request()->routeIs('users.login-history*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('users.login-history') }}">
        <i class="fas fa-sign-in-alt"></i>
        <span>{{ trans_str('login history') }}</span>
    </a>
</li>


{{-- Active Sessions Management --}}

<li class="nav-item {{ request()->routeIs('users.active-sessions*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('users.active-sessions') }}">
        <i class="fas fa-desktop"></i>
        <span>{{ trans_str('active sessions') }}</span>
        <span class="badge bg-success ms-auto"
            id="active-sessions-count">{{ \App\Models\LoginSession::whereNull('logout_at')->count() }}</span>
    </a>
</li>



<li class="nav-item {{ request()->routeIs('users.activity-log*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('users.activity-log') }}">
        <i class="fas fa-list-alt"></i>
        <span>{{ trans_str('activity log') }}</span>
    </a>
</li>

<hr class="my-3 border-secondary">

<div class="sidebar-header mb-2">
    <h6 class="text-muted fw-bold px-3 mb-2">
        <i class="fas fa-chart-line me-2"></i>
        {{ trans_str('quick statistics') }}
    </h6>
</div>

{{-- System Statistics --}}

<li class="nav-item">
    <a class="nav-link" href="javascript:void(0);" onclick="showUserStats()">
        <i class="fas fa-chart-pie"></i>
        <span>{{ trans_str('system statistics') }}</span>
    </a>
</li>




@push('scripts')
    <script>
        function showUserStats() {
            Swal.fire({
                title: '<i class="fas fa-chart-pie text-primary"></i> {{ trans_str('system statistics') }}',
                html: `
            <div class="text-start">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \App\Models\User::count() }}</h3>
                                <small>{{ trans_str('total users') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-user-check fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</h3>
                                <small>{{ trans_str('active users') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-info bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-shield-alt fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \Modules\Authorization\Models\Permission::count() }}</h3>
                                <small>{{ trans_str('total permissions') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-warning bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-code-branch fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \Modules\Branches\Models\Branch::count() }}</h3>
                                <small>{{ trans_str('branches count') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-secondary bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-user-tag fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \Modules\Authorization\Models\Role::count() }}</h3>
                                <small>{{ trans_str('roles count') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="card border-0 shadow-sm bg-gradient text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fs-1 mb-2"></i>
                                <h3 class="mb-0">{{ \App\Models\User::where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                                <small>{{ trans_str('new users (7 days)') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
                showConfirmButton: true,
                confirmButtonText: '<i class="fas fa-times"></i> {{ trans_str('close') }}',
                width: '700px',
                customClass: {
                    popup: 'animated fadeInDown'
                }
            });
        }

        function showActiveSessions() {
            // Fetch active sessions via AJAX
            fetch('/api/active-sessions', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const sessions = data.sessions || [];
                    let html = '<div class="text-start">';

                    if (sessions.length === 0) {
                        html += `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ trans_str('no active sessions currently') }}
                </div>
            `;
                    } else {
                        html += '<div class="table-responsive"><table class="table table-hover mb-0">';
                        html += `
                <thead class="table-light">
                    <tr>
                        <th><i class="fas fa-user"></i> {{ trans_str('user') }}</th>
                        <th><i class="fas fa-laptop"></i> {{ trans_str('device') }}</th>
                        <th><i class="fas fa-clock"></i> {{ trans_str('last activity') }}</th>
                    </tr>
                </thead>
                <tbody>
            `;

                        sessions.forEach(session => {
                            html += `
                    <tr>
                        <td class="fw-bold">${session.user}</td>
                        <td><small class="text-muted">${session.device}</small></td>
                        <td><small class="text-muted">${session.last_activity}</small></td>
                    </tr>
                `;
                        });

                        html += '</tbody></table></div>';
                    }

                    html += '</div>';

                    Swal.fire({
                        title: '<i class="fas fa-desktop text-success"></i> {{ trans_str('active sessions') }}',
                        html: html,
                        confirmButtonText: '<i class="fas fa-times"></i> {{ trans_str('close') }}',
                        width: '600px',
                        customClass: {
                            popup: 'animated fadeInDown'
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: '<i class="fas fa-desktop text-info"></i> {{ trans_str('active sessions') }}',
                        html: `
                <div class="text-center py-4">
                    <i class="fas fa-users fs-1 text-success mb-3"></i>
                    <h5 class="text-muted">{{ \App\Models\LoginSession::where('logout_at', null)->count() }} {{ __('active session') }}</h5>
                    <p class="text-muted mb-0">{{ trans_str('users currently connected to the system') }}</p>
                </div>
            `,
                        confirmButtonText: '<i class="fas fa-times"></i> {{ trans_str('close') }}',
                        width: '500px'
                    });
                });
        }
    </script>
@endpush
