<div class="left-sidenav">
    
    {{-- Custom Sidebar Header --}}
    <div class="brand-box d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-hard-hat fa-2x text-dark"></i>
            <h5 class="mb-0 text-dark fw-bold logo-text">Daily progress</h5>
        </div>
        {{-- Close Button --}}
        <button type="button" class="btn btn-sm btn-soft-secondary d-none d-lg-block" id="internal-sidebar-close" onclick="toggleSidebarMenu()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">
            
            <li class="menu-label mt-3 mb-2 px-3 text-muted fw-bold d-flex justify-content-between align-items-center">
                <span>Menu</span>
            </li>

            {{-- 1. Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link d-flex align-items-center gap-3">
                    <i class="fas fa-home menu-icon"></i>
                    <span>{{ __('navigation.dashboard') }}</span>
                </a>
            </li>

            {{-- 2. Projects --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('progress.project.index') }}">
                    <i class="fas fa-project-diagram menu-icon"></i>
                    <span>{{ __('navigation.projects') }}</span>
                </a>
            </li>

            {{-- 3. Issues --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('issues.index') }}">
                    <i class="fas fa-bug menu-icon"></i>
                    <span>Issues</span>
                </a>
            </li>

            {{-- 4. Daily Progress --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('daily_progress.index') }}">
                    <i class="fas fa-list-alt menu-icon"></i>
                    <span>{{ __('navigation.daily_progress') }}</span>
                </a>
            </li>

            {{-- 5. Create Daily Progress --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('daily_progress.create') }}">
                    <i class="fas fa-plus-circle menu-icon"></i>
                    <span>Create Daily Progress</span>
                </a>
            </li>

            {{-- 6. Clients --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('clients.index') }}">
                    <i class="fas fa-users menu-icon"></i>
                    <span>{{ __('navigation.clients') }}</span>
                </a>
            </li>

            {{-- 7. Employees --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('employees.index') }}">
                    <i class="fas fa-user-tie menu-icon"></i>
                    <span>{{ __('navigation.employees') }}</span>
                </a>
            </li>

            {{-- 8. Categories --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('work-item-categories.index') }}">
                    <i class="fas fa-layer-group menu-icon"></i>
                    <span>{{ __('navigation.categories') }}</span>
                </a>
            </li>

            {{-- 9. Work Items --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('work.items.index') }}">
                    <i class="fas fa-list menu-icon"></i>
                    <span>{{ __('navigation.work_items') }}</span>
                </a>
            </li>

            {{-- 10. Item Statuses --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('item-statuses.index') }}">
                    <i class="fas fa-tags menu-icon"></i>
                    <span>Item Statuses</span>
                </a>
            </li>

            {{-- 11. Project Templates --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('project.template.index') }}">
                    <i class="fas fa-file-alt menu-icon"></i>
                    <span>{{ __('navigation.project_template') }}</span>
                </a>
            </li>

            {{-- 12. Project Type --}}
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="{{ route('project.types.index') }}">
                    <i class="fas fa-cubes menu-icon"></i> 
                    <span>Project Types</span>
                </a>
            </li>

            {{-- 13. Activity Logs --}}
             <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-3" href="#">
                    <i class="fas fa-history menu-icon"></i>
                    <span>Activity Logs</span>
                </a>
            </li>

        </ul>
    </div>
</div>
<!-- No custom style block - rely on Metrica theme defaults -->
