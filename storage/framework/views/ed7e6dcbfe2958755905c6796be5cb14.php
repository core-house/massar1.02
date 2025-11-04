<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قيد يومية')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('journal.statistics')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('Journal Statistics')); ?>

        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('journals.create', ['type' => 'basic_journal'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.daily_journal')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قيد يوميه متعدد')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-journals.create')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.multi_journal')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قيود يومية عمليات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('journals.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.daily_ledgers_operations')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قيود يوميه عمليات متعدده')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-journals.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.multi_daily_ledgers_operations')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قيود يوميه حسابات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('journal-summery')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.daily_ledgers_accounts')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض تسجيل الارصده الافتتاحيه للمخازن')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('inventory-balance.create')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.opening_inventory_balance')); ?>

        </a>
    </li>
<?php endif; ?>

<li class="nav-item">
    <a class="nav-link font-family-cairo fw-bold" href="<?php echo e(route('accounts.startBalance')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.opening_balance_accounts')); ?>

    </a>
</li>


<li class="nav-item">
    <a class="nav-link font-family-cairo fw-bold" href="<?php echo e(route('account-movement')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.account_movement_report')); ?>

    </a>
</li>



<li class="nav-item">
    <a class="nav-link font-family-cairo fw-bold" href="<?php echo e(route('accounts.balanceSheet')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.balance_sheet')); ?>

    </a>
</li>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/journals.blade.php ENDPATH**/ ?>