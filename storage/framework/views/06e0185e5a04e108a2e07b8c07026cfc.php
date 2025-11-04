
<?php
    // نفس المنطق القديم للـ sidebar parameter
    $section = request('sidebar') ?? session('sidebar', 'all');
    $map = [
        'main' => null,
        'accounts' => ['components.sidebar.accounts'],
        'items' => ['components.sidebar.items'],
        'discounts' => ['components.sidebar.discounts'],
        'manufacturing' => ['components.sidebar.manufacturing'],
        'permissions' => ['components.sidebar.permissions'],
        'crm' => ['components.sidebar.crm'],
        'sales-invoices' => ['components.sidebar.sales-invoices'],
        'purchases-invoices' => ['components.sidebar.purchases-invoices'],
        'inventory-invoices' => ['components.sidebar.inventory-invoices'],
        'vouchers' => ['components.sidebar.vouchers'],
        'transfers' => ['components.sidebar.transfers'],
        'multi-vouchers' => ['components.sidebar.merit-vouchers'],
        'contract-journals' => ['components.sidebar.contract-journals'],
        'Assets-operations' => ['components.sidebar.multi-vouchers'],
        'depreciation' => ['components.sidebar.depreciation'],
        'basic_journal-journals' => ['components.sidebar.journals'],
        'projects' => ['components.sidebar.projects'],
        'departments' => ['components.sidebar.departments'],
        'settings' => ['components.sidebar.settings'],
        'rentals' => ['components.sidebar.rentals'],
        'service' => ['components.sidebar.service'],
        'shipping' => ['components.sidebar.shipping'],
        'POS' => ['components.sidebar.POS'],
        'daily_progress' => ['components.sidebar.daily_progress'],
        'inquiries' => ['components.sidebar.inquiries'],
        'checks' => ['components.sidebar.checks'],
    ];
    $allowed = $section === 'all' ? 'all' : ($map[$section] ?? []);
?>

<!-- Left Sidenav -->
<div class="left-sidenav">

    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">

            <li class="menu-label my-2"><a href="<?php echo e(route('home')); ?>"><?php echo e(config('public_settings.campany_name')); ?></a>
            </li>

            <li class="nav-item border-bottom pb-1 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>"
                    class="nav-link d-flex align-items-center gap-2 font-family-cairo fw-bold">
                    <i data-feather="home" style="color:#4e73df" class="menu-icon"></i>
                    <?php echo e(__('navigation.home')); ?>

                </a>

            </li>

            <?php if($section !== 'all'): ?>
                <li class="nav-item mb-2">
                    <div class="alert alert-info d-flex align-items-center justify-content-between" style="margin: 0; padding: 0.5rem 0.75rem;">
                        <small class="mb-0">
                            <i data-feather="filter" style="width: 14px; height: 14px;" class="me-1"></i>
                            عرض: <?php echo e($section); ?>

                        </small>
                    </div>
                </li>
            <?php endif; ?>

            <?php if(View::hasSection('sidebar-filter')): ?>
                <?php echo $__env->yieldContent('sidebar-filter'); ?>
            <?php else: ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.accounts', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.items', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.items', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.discounts', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.discounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.manufacturing', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.manufacturing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.permissions', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.permissions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.crm', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.crm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.sales-invoices', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.sales-invoices', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.purchases-invoices', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.purchases-invoices', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.inventory-invoices', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.inventory-invoices', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.vouchers', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.vouchers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.transfers', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.transfers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.merit-vouchers', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.merit-vouchers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.contract-journals', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.contract-journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.multi-vouchers', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.multi-vouchers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.depreciation', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.depreciation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.journals', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.projects', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.projects', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.departments', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.departments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.settings', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.rentals', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.rentals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.service', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.service', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.shipping', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.shipping', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.POS', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.POS', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.daily_progress', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.daily_progress', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.inquiries', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.inquiries', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <?php if($allowed === 'all' || in_array('components.sidebar.checks', $allowed)): ?>
                    <?php echo $__env->make('components.sidebar.checks', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            <?php endif; ?>

        </ul>
    </div>
</div>

<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/admin/partials/sidebar-default.blade.php ENDPATH**/ ?>