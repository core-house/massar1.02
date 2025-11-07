<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('accounts.basic-data-statistics')); ?>">
        <i class="ti-list"></i><?php echo e(__('إحصائيات البيانات الأساسية')); ?>

    </a>
</li>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض جميع الحسابات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index')); ?>">
            <i class="ti-list"></i><?php echo e(__('navigation.all_accounts')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض العملاء')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'clients'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.clients')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الموردين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'suppliers'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.suppliers')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الصناديق')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'funds'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.funds')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض البنوك')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'banks'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.banks')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الموظفين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'employees'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.employees')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض المخازن')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'warhouses'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.warehouses')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض المصروفات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'expenses'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.expenses')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الايرادات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'revenues'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.revenues')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض دائنين متنوعين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'creditors'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.various_creditors')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض مدينين متنوعين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'debtors'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.various_debtors')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الشركاء')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'partners'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.partners')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض جارى الشركاء')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'current-partners'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.current_partners')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الأصول الثابتة')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'assets'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.fixed_assets')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الأصول القابلة للتأجير')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.index', ['type' => 'rentables'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.rentable_assets')); ?>

        </a>
    </li>
<?php endif; ?>


<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض حافظات أوراق القبض')): ?>
    <li class="nav-item">
        <a class="nav-link font-family-cairo fw-bold"
            href="<?php echo e(route('accounts.index', ['type' => 'check-portfolios-incoming'])); ?>">
            <i class="fas fa-folder-open" style="color:#28a745"></i> حافظات أوراق القبض
        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('إضافة حافظات أوراق القبض')): ?>
    <li class="nav-item">
        <a class="nav-link font-family-cairo" href="<?php echo e(route('accounts.create', ['parent' => '1105'])); ?>">
            <i class="fas fa-plus-circle" style="color:#28a745"></i> إضافة حافظة قبض
        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض حافظات أوراق الدفع')): ?>
    <li class="nav-item">
        <a class="nav-link font-family-cairo fw-bold"
            href="<?php echo e(route('accounts.index', ['type' => 'check-portfolios-outgoing'])); ?>">
            <i class="fas fa-folder-open" style="color:#dc3545"></i> حافظات أوراق الدفع
        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('إضافة حافظات أوراق الدفع')): ?>
    <li class="nav-item">
        <a class="nav-link font-family-cairo" href="<?php echo e(route('accounts.create', ['parent' => '2103'])); ?>">
            <i class="fas fa-plus-circle" style="color:#dc3545"></i> إضافة حافظة دفع
        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض تقرير حركة الحساب')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.account-movement-report')); ?>">
            <i class="ti-bar-chart"></i><?php echo e(__('navigation.account_movement_report')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الميزانية العمومية')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.balance-sheet')); ?>">
            <i class="ti-pie-chart"></i><?php echo e(__('navigation.balance_sheet')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('إدارة الرصيد الافتتاحي')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('accounts.start-balance')); ?>">
            <i class="ti-settings"></i><?php echo e(__('navigation.start_balance_management')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/accounts.blade.php ENDPATH**/ ?>