<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('vouchers.statistics')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('Vouchers Statistics')); ?>

    </a>
</li>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض سند قبض')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('vouchers.index', ['type' => 'receipt'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.general_receipt_voucher')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(' سند دفع عامل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('vouchers.index', ['type' => 'payment'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.general_payment_voucher')); ?>

        </a>
    </li>
<?php endif; ?>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('vouchers.index', ['type' => 'payment'])); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.general_payment_voucher')); ?>

    </a>
</li>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض السندات')): ?>
    <li class="nav-item">

        <a class="nav-link" href="<?php echo e(route('vouchers.index', ['type' => 'exp-payment'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.general_payment_voucher_for_expenses')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض سند دفع متعدد')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.index', ['type' => 'multi_payment'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.multi_payment_voucher')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض سند قبض متعدد')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.index', ['type' => 'multi_receipt'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.multi_receipt_voucher')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/vouchers.blade.php ENDPATH**/ ?>