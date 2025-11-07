<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قائمة الخصومات المسموح بها')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('discounts.general-statistics')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('Discounts.Statistics')); ?>

        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('discounts.index', ['type' => 30])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.allowed_discounts')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض قائمة الخصومات المكتسبة')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('discounts.index', ['type' => 31])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.earned_discounts')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض خصم مسموح به')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('discounts.create', ['type' => 30, 'q' => md5(30)])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.allowed_discount')); ?>

        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض خصم مكتسب')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('discounts.create', ['type' => 31, 'q' => md5(31)])); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.earned_discount')); ?>

        </a>
    </li>
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/discounts.blade.php ENDPATH**/ ?>