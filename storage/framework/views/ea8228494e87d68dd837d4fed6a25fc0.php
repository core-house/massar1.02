<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض اتفاقية خدمة')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'contract'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.service_agreement')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض مصروفات مستحقة')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'accured_expense'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.accured_expenses')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض ايرادات مستحقة')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'accured_income'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.accured_revenues')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب عمولة بنكية')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'bank_commission'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.bank_commission_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض عقد بيع')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'sales_contract'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.sales_contract')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض توزيع الارباح علي الشركا')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'partner_profit_sharing'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.partner_profit_sharing')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/contract-journals.blade.php ENDPATH**/ ?>