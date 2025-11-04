<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('multi-vouchers.statistics')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('Multi vouchers Statistics')); ?>

    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('depreciation.index')); ?>">
        <i class="ti-control-record"></i>قائمة الاصول
    </a>
</li>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض اهلاك الاصل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'depreciation'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.depreciation')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض بيع الاصول')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'sell_asset'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.sell_asset')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض شراء اصل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'buy_asset'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.buy_asset')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض زيادة في قيمة الاصل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'increase_asset_value'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.increase_asset_value')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض نقص في قيمة الاصل')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'decrease_asset_value'])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.decrease_asset_value')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/multi-vouchers.blade.php ENDPATH**/ ?>