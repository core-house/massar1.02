{{-- قسم إدارة الأقسام --}}
@can('view Departments')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('departments.index') }}">
            <i class="las la-building"></i>{{ __('navigation.departments') }}
        </a>
    </li>
@endcan

{{-- قسم إدارة الوظائف --}}
@can('view Jobs')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('jobs.index') }}">
            <i class="las la-briefcase"></i>{{ __('navigation.jobs') }}
        </a>
    </li>
@endcan

{{-- قسم إدارة العناوين (الدول، المحافظات، المدن، الأحياء) --}}
@canany(['view Countries', 'view States', 'view Cities', 'view Towns'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-map-marked-alt"></i>{{ __('navigation.addresses') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Countries')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('countries.index') }}">
                        <i class="las la-globe"></i>{{ __('navigation.countries') }}
                    </a>
                </li>
            @endcan
            @can('view States')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('states.index') }}">
                        <i class="las la-map"></i>{{ __('navigation.states') }}
                    </a>
                </li>
            @endcan
            @can('view Cities')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('cities.index') }}">
                        <i class="las la-city"></i>{{ __('navigation.cities') }}
                    </a>
                </li>
            @endcan
            @can('view Towns')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('towns.index') }}">
                        <i class="las la-map-marker-alt"></i>{{ __('navigation.towns') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

{{-- قسم إدارة الورديات --}}
@can('view Shifts')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('shifts.index') }}">
            <i class="las la-clock"></i>{{ __('navigation.shifts') }}
        </a>
    </li>
@endcan

{{-- قسم إدارة الموظفين --}}
@can('view Employees')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('employees.index') }}">
            <i class="las la-users"></i>{{ __('navigation.employees') }}
        </a>
    </li>
@endcan

{{-- قسم مؤشرات الأداء وتقييم الموظفين --}}
@canany(['view KPIs', 'view Employee Evaluations'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-chart-line"></i>{{ __('navigation.performance_kpis') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view KPIs')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('kpis.index') }}">
                        <i class="las la-tasks"></i>{{ __('navigation.kpis') }}
                    </a>
                </li>
            @endcan
            @can('view Employee Evaluations')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('kpis.employeeEvaluation') }}">
                        <i class="las la-star"></i>{{ __('navigation.employee_performance_kpis') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

{{-- قسم إدارة الحضور والانصراف --}}
@canany(['view Attendances', 'view Attendance Processing'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-user-check"></i>{{ __('navigation.attendance') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Attendances')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('attendances.index') }}">
                        <i class="las la-clipboard-list"></i>{{ __('navigation.attendance_records') }}
                    </a>
                </li>
            @endcan
            {{-- Mobile Fingerprint Login --}}
            @can('view Mobile-fingerprint')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('mobile.employee-login') }}">
                        <i class="las la-fingerprint"></i>{{ __('navigation.mobile_fingerprint_login') }}
                    </a>
                </li>
            @endcan
        </ul>

    </li>
@endcanany

{{-- قسم إدارة الرواتب والسلف والخصومات والمكافآت --}}
<li class="nav-item has-submenu">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
        <i class="las la-money-bill-wave"></i>{{ __('navigation.employees_payroll') }}
        <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
    </a>
    <ul class="sub-menu mm-collapse">
        @can('view Attendance Processing')
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.attendance.processing') }}">
                    <i class="las la-calculator"></i>{{ __('navigation.attendance_processing') }}
                </a>
            </li>
        @endcan
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.flexible-salary.processing.index') }}">
                <i class="las la-hand-holding-usd"></i>{{ __('navigation.flexible_salary_processing') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.employee-advances.index') }}">
                <i class="las la-coins"></i>{{ __('navigation.employee_advances') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.employee-deductions-rewards.index') }}">
                <i class="las la-award"></i>{{ __('navigation.employee_deductions_rewards') }}
            </a>
        </li>
    </ul>
</li>

{{-- قسم إدارة الإجازات --}}
@canany(['view Leave Types', 'view Leave Balances', 'view Leave Requests', 'create Leave Balances', 'edit Leave
    Balances', 'create Leave Requests', 'edit Leave Requests'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-calendar-times"></i>{{ __('navigation.leave_management') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view Leave Types')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.leaves.types.manage') }}">
                        <i class="las la-list"></i>{{ __('navigation.leave_types') }}
                    </a>
                </li>
            @endcan
            <!-- @can('view Leave Balances')
        <li class="nav-item">
                            <a class="nav-link" href="{{ route('hr.leaves.balances.index') }}">
                                <i class="ti-control-record"></i>{{ __('navigation.leave_balances') }}
                            </a>
                        </li>
    @endcan -->
            <!-- @can('create Leave Balances')
        <li class="nav-item">
                            <a class="nav-link" href="{{ route('hr.leaves.balances.create') }}">
                                <i class="ti-control-record"></i>{{ __('navigation.create_leave_balance') }}
                            </a>
                        </li>
    @endcan -->
            @can('view Leave Requests')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.leaves.requests.index') }}">
                        <i class="las la-clipboard-list"></i>{{ __('navigation.leave_requests') }}
                    </a>
                </li>
            @endcan
            @can('create Leave Requests')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.leaves.requests.create') }}">
                        <i class="las la-plus-circle"></i>{{ __('navigation.create_leave_request') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

{{-- قسم إدارة التوظيف --}}
@canany(['view Contract Types', 'view CVs', 'view Contracts', 'view Job Postings', 'view Interviews', 'view
    Onboardings', 'view Terminations'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-user-plus"></i>{{ __('recruitment.recruitment_management') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.dashboard') }}">
                    <i class="las la-tachometer-alt"></i>{{ __('recruitment.recruitment_dashboard') }}
                </a>
            </li>
            @can('view Contract Types')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.contract-types.index') }}">
                        <i class="las la-file-contract"></i>{{ __('navigation.contract_types') }}
                    </a>
                </li>
            @endcan
            @can('view Job Postings')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.job-postings.index') }}">
                        <i class="las la-bullhorn"></i>{{ __('recruitment.job_postings') }}
                    </a>
                </li>
            @endcan
            @can('view CVs')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.cvs.index') }}">
                        <i class="las la-file-alt"></i>{{ __('recruitment.cvs') }}
                    </a>
                </li>
            @endcan
            @can('view Interviews')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.interviews.index') }}">
                        <i class="las la-comments"></i>{{ __('recruitment.interviews') }}
                    </a>
                </li>
            @endcan
            @can('view Interviews')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.interviews.calendar') }}">
                        <i class="las la-calendar-alt"></i>{{ __('recruitment.interview_calendar') }}
                    </a>
                </li>
            @endcan
            @can('view Contracts')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.contracts.index') }}">
                        <i class="las la-file-signature"></i>{{ __('recruitment.contracts') }}
                    </a>
                </li>
            @endcan
            @can('view Onboardings')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.onboardings.index') }}">
                        <i class="las la-user-check"></i>{{ __('recruitment.onboardings') }}
                    </a>
                </li>
            @endcan
            @can('view Terminations')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('recruitment.terminations.index') }}">
                        <i class="las la-user-times"></i>{{ __('recruitment.terminations') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

{{-- قسم إدارة العهد --}}
@can('view Covenants')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('covenants.index') }}">
            <i class="las la-handshake"></i>{{ __('navigation.covenants') }}
        </a>
    </li>
@endcan

{{-- قسم إدارة مأموريات العمل --}}
@can('view Errands')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('errands.index') }}">
            <i class="las la-route"></i>{{ __('navigation.errands') }}
        </a>
    </li>
@endcan

{{-- قسم إدارة أذونات العمل --}}
@can('view Work Permissions')
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('work-permissions.index') }}">
            <i class="las la-user-clock"></i>{{ __('navigation.work_permissions') }}
        </a>
    </li>
@endcan

{{-- قسم إعدادات الموارد البشرية --}}
@canany(['view HR Settings', 'edit HR Settings'])
    <li class="nav-item has-submenu">
        <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="javascript: void(0);">
            <i class="las la-cog"></i>{{ __('navigation.hr_settings') }}
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse">
            @can('view HR Settings')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.settings.index') }}">
                        <i class="las la-sliders-h"></i>{{ __('navigation.view_hr_settings') }}
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

{{-- route name:  hr.attendance.reports.project --}}

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2 font-hold fw-bold transition-base" href="{{ route('hr.attendance.reports.project') }}">
        <i class="las la-file-alt"></i>{{ __('hr.project_attendance_report') }}
    </a>
</li>
