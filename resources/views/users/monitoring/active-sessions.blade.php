@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.permissions')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('Active Sessions'),
        'breadcrumb_items' => [
            ['label' => __('Home'), 'url' => route('admin.dashboard')],
            ['label' => __('Users'), 'url' => route('users.index')],
            ['label' => __('Active Sessions')],
        ],
    ])

    <div class="row">
        <div class="col-12">
            {{-- إحصائيات سريعة --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $activeSessions->count() }}</h3>
                                    <p class="mb-0">{{ __('Active Session') }}</p>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $activeSessions->unique('user_id')->count() }}</h3>
                                    <p class="mb-0">{{ __('Connected User') }}</p>
                                </div>
                                <i class="fas fa-user-check fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-0">{{ $activeSessions->where('login_at', '>=', now()->subHour())->count() }}</h3>
                                    <p class="mb-0">{{ __('In Last Hour') }}</p>
                                </div>
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" x-data="{
                search: '',
                deviceFilter: 'all',
                get filteredSessions() {
                    let sessions = Array.from(document.querySelectorAll('#sessions-table tbody tr[data-session]'));
                    return sessions.filter(row => {
                        const userName = row.dataset.userName.toLowerCase();
                        const userEmail = row.dataset.userEmail.toLowerCase();
                        const ipAddress = row.dataset.ipAddress.toLowerCase();
                        const device = row.dataset.device.toLowerCase();
                        const searchLower = this.search.toLowerCase();
                        
                        const matchesSearch = !this.search || 
                            userName.includes(searchLower) || 
                            userEmail.includes(searchLower) || 
                            ipAddress.includes(searchLower);
                        
                        const matchesDevice = this.deviceFilter === 'all' || device === this.deviceFilter;
                        
                        return matchesSearch && matchesDevice;
                    });
                },
                updateDisplay() {
                    const sessions = Array.from(document.querySelectorAll('#sessions-table tbody tr[data-session]'));
                    const filtered = this.filteredSessions;
                    
                    sessions.forEach(row => {
                        row.style.display = filtered.includes(row) ? '' : 'none';
                    });
                    
                    document.getElementById('no-results').style.display = filtered.length === 0 ? '' : 'none';
                    document.getElementById('results-count').textContent = filtered.length;
                }
            }" x-init="$watch('search', () => updateDisplay()); $watch('deviceFilter', () => updateDisplay())">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-desktop me-2"></i>
                            {{ __('Currently Active Sessions') }}
                            <span class="badge bg-light text-primary ms-2" id="results-count">{{ $activeSessions->count() }}</span>
                        </h5>
                        <button class="btn btn-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> {{ __('Refresh') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    {{-- فلاتر البحث --}}
                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       x-model="search"
                                       placeholder="{{ __('Search by name, email, or IP...') }}"
                                       aria-label="{{ __('Search') }}">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        x-show="search"
                                        @click="search = ''"
                                        aria-label="{{ __('Clear') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" x-model="deviceFilter" aria-label="{{ __('Filter by device') }}">
                                <option value="all">{{ __('All Devices') }}</option>
                                <option value="mobile">{{ __('Mobile') }}</option>
                                <option value="tablet">{{ __('Tablet') }}</option>
                                <option value="computer">{{ __('Computer') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" 
                                    type="button"
                                    @click="search = ''; deviceFilter = 'all'">
                                <i class="fas fa-redo"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="sessions-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-user"></i> {{ __('User') }}</th>
                                    <th><i class="fas fa-envelope"></i> {{ __('Email') }}</th>
                                    <th><i class="fas fa-sign-in-alt"></i> {{ __('Login Time') }}</th>
                                    <th><i class="fas fa-hourglass-half"></i> {{ __('Since') }}</th>
                                    <th><i class="fas fa-network-wired"></i> IP</th>
                                    <th><i class="fas fa-laptop"></i> {{ __('Device') }}</th>
                                    @can('edit Users')
                                        <th><i class="fas fa-cog"></i> {{ __('Actions') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeSessions as $session)
                                    @php
                                        $deviceType = 'computer';
                                        if($session->user_agent) {
                                            if(str_contains($session->user_agent, 'Mobile')) {
                                                $deviceType = 'mobile';
                                            } elseif(str_contains($session->user_agent, 'Tablet')) {
                                                $deviceType = 'tablet';
                                            }
                                        }
                                    @endphp
                                    <tr data-session
                                        data-user-name="{{ $session->user->name ?? __('Unknown') }}"
                                        data-user-email="{{ $session->user->email ?? 'N/A' }}"
                                        data-ip-address="{{ $session->ip_address ?? 'N/A' }}"
                                        data-device="{{ $deviceType }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-bold">
                                            <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                                            {{ $session->user->name ?? __('Unknown') }}
                                        </td>
                                        <td>{{ $session->user->email ?? 'N/A' }}</td>
                                        <td>
                                            @if($session->login_at)
                                                <span class="badge bg-info">
                                                    {{ $session->login_at->format('Y-m-d H:i') }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($session->login_at)
                                                <span class="text-muted">
                                                    {{ $session->login_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $session->ip_address ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                @if($session->user_agent)
                                                    @if(str_contains($session->user_agent, 'Mobile'))
                                                        <i class="fas fa-mobile-alt text-primary"></i> {{ __('Mobile') }}
                                                    @elseif(str_contains($session->user_agent, 'Tablet'))
                                                        <i class="fas fa-tablet-alt text-info"></i> {{ __('Tablet') }}
                                                    @else
                                                        <i class="fas fa-desktop text-secondary"></i> {{ __('Computer') }}
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            </small>
                                        </td>
                                        @can('edit Users')
                                            <td>
                                                <form action="{{ route('users.terminate-session', $session->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('{{ __('Are you sure you want to terminate this session?') }}')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-power-off"></i> {{ __('Terminate') }}
                                                    </button>
                                                </form>
                                            </td>
                                        @endcan
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                {{ __('No active sessions currently') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="no-results" style="display: none;">
                                    <td colspan="8" class="text-center py-4">
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ __('No sessions match your filters') }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            white-space: nowrap;
        }

        code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        .input-group-text {
            border-right: 0;
        }

        .input-group .form-control {
            border-left: 0;
        }

        .input-group .form-control:focus {
            border-color: #ced4da;
            box-shadow: none;
        }

        .input-group .form-control:focus + .input-group-text {
            border-color: #ced4da;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
@endpush

