@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.permissions')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('authorization::users.user_details'),
        'breadcrumb_items' => [
            ['label' => __('authorization::users.users'), 'url' => route('users.index')],
            ['label' => __('authorization::users.user_details')],
        ],
    ])

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box no-print">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="page-title">{{ __('authorization::users.user_details') }}: {{ $user->name }}</h4>
                        <div class="d-flex gap-2">
                            @can('edit Users')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> {{ __('authorization::users.edit') }}
                                </a>
                            @endcan
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> {{ __('authorization::users.print') }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> {{ __('authorization::users.back') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card printable-content">
                    <div class="card-header bg-white p-0 border-bottom">
                        <ul class="nav nav-tabs card-header-tabs" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#info-data" type="button" role="tab">
                                    <i class="fas fa-user me-2"></i>
                                    {{ __('authorization::users.user_information') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="projects-tab" data-bs-toggle="tab"
                                    data-bs-target="#projects-data" type="button" role="tab">
                                    <i class="fas fa-project-diagram me-2"></i>
                                    {{ __('authorization::users.projects') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="userTabsContent">
                            <!-- معلومات المستخدم -->
                            <div class="tab-pane fade show active" id="info-data" role="tabpanel">
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">{{ __('authorization::users.name') }}:</label>
                                            <div class="form-control-static">{{ $user->name }}</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">{{ __('authorization::users.email') }}:</label>
                                            <div class="form-control-static">{{ $user->email }}</div>
                                        </div>
                                        @if ($user->phone)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">{{ __('authorization::users.phone_number') }}:</label>
                                                <div class="form-control-static">{{ $user->phone }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($user->branches->count() > 0)
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">{{ __('authorization::users.branches') }}:</label>
                                                <div class="form-control-static">
                                                    @foreach ($user->branches as $branch)
                                                        <span class="badge bg-info me-1">{{ $branch->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($user->roles->count() > 0)
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">{{ __('authorization::users.roles') }}:</label>
                                                <div class="form-control-static">
                                                    @foreach ($user->roles as $role)
                                                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($user->permissions->count() > 0)
                                        <div class="row mt-4">
                                            <div class="col-12 mb-3">
                                                <label class="form-label fw-bold">{{ __('authorization::users.permissions') }}:</label>
                                                <div class="form-control-static">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($permissions as $category => $perms)
                                                            <div class="mb-2">
                                                                <strong>{{ $category }}:</strong>
                                                                @foreach ($perms as $perm)
                                                                    @if ($user->permissions->contains('id', $perm->id))
                                                                        <span class="badge bg-success me-1">{{ $perm->name }}</span>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">{{ __('authorization::users.created_at') }}:</label>
                                            <div class="form-control-static">
                                                {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i') : __('authorization::users.na') }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">{{ __('authorization::users.updated_at') }}:</label>
                                            <div class="form-control-static">
                                                {{ $user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('Y-m-d H:i') : __('authorization::users.na') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- المشاريع -->
                            <div class="tab-pane fade" id="projects-data" role="tabpanel">
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="m-0 fw-bold text-primary">
                                            <i class="fas fa-project-diagram me-2"></i>
                                            {{ __('authorization::users.projects') }}
                                        </h6>
                                    </div>
                                    @if ($userProjects->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="text-start">{{ __('projects.name') }}</th>
                                                        <th class="text-start">{{ __('projects.client') }}</th>
                                                        <th class="text-center">{{ __('general.status') }}</th>
                                                        <th class="text-center">{{ __('projects.start_date') }}</th>
                                                        <th class="text-center">{{ __('projects.end_date') }}</th>
                                                        <th class="text-center">{{ __('general.actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($userProjects as $index => $project)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td class="text-start">{{ $project->name }}</td>
                                                            <td class="text-start">{{ $project->client?->cname ?? '-' }}</td>
                                                            <td class="text-center">
                                                                @php
                                                                    $statusClass = match ($project->status) {
                                                                        'pending' => 'bg-warning',
                                                                        'in_progress' => 'bg-info',
                                                                        'completed' => 'bg-success',
                                                                        'cancelled' => 'bg-danger',
                                                                        default => 'bg-secondary',
                                                                    };
                                                                    $statusText = match ($project->status) {
                                                                        'pending' => 'قيد الانتظار',
                                                                        'in_progress' => 'قيد التنفيذ',
                                                                        'completed' => 'مكتمل',
                                                                        'cancelled' => 'ملغي',
                                                                        default => 'غير معروف',
                                                                    };
                                                                @endphp
                                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                            </td>
                                                            <td class="text-center">{{ $project->start_date?->format('Y-m-d') ?? '-' }}</td>
                                                            <td class="text-center">{{ $project->end_date?->format('Y-m-d') ?? '-' }}</td>
                                                            <td class="text-center">
                                                                <a href="{{ route('progress.project.show', $project->id) }}"
                                                                    class="btn btn-sm btn-primary"
                                                                    title="{{ __('authorization::users.perm_view') }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            @if ($user->employee)
                                                {{ __('authorization::users.no_projects') }}
                                            @else
                                                {{ __('authorization::users.no_employee_link') }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .form-control-static {
                padding: 0.5rem;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                min-height: 2.5rem;
                display: flex;
                align-items: center;
            }

            @media print {
                .no-print { display: none !important; }
                .card { border: 1px solid #000 !important; box-shadow: none !important; }
                .card-header { background: #f1f1f1 !important; color: #000 !important; }
                body { font-size: 12px; }
                .form-control-static { background: #fff !important; border: 1px solid #000 !important; }
            }
        </style>
    @endpush
@endsection
