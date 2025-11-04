<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب الثابت للموظفين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'salary_calculation'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.fixed_salary_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب الاضافي للموظفين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'extra_calc'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.extra_salary_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب خصم للموظفين')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'discount_calc'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.discount_salary_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب تأمينات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'insurance_calc'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.insurance_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض احتساب ضريبة دخل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'tax_calc'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.tax_calculation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/merit-vouchers.blade.php ENDPATH**/ ?>