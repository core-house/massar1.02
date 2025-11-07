<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض اهلاك الاصل')): ?>
    <li class="li-main">
        <a href="javascript: void(0);" class="has-arrow waves-effect waves-dark">
            <i data-feather="calculator" style="color:#28a745" class="align-self-center menu-icon"></i>
            <span><?php echo e(__('إدارة إهلاك الأصول')); ?></span>
            <span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="sub-menu mm-collapse" aria-expanded="false">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('depreciation.index')); ?>">
                    <i class="ti-control-record"></i><?php echo e(__('إدارة الإهلاك')); ?>

                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('depreciation.schedule')); ?>">
                    <i class="ti-control-record"></i><?php echo e(__('جدولة الإهلاك')); ?>

                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('depreciation.report')); ?>">
                    <i class="ti-control-record"></i><?php echo e(__('تقرير الإهلاك')); ?>

                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo e(route('multi-vouchers.create', ['type' => 'depreciation'])); ?>">
                    <i class="ti-control-record"></i><?php echo e(__('قيد إهلاك')); ?>

                </a>
            </li>
        </ul>
    </li>
<?php endif; ?> <?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/depreciation.blade.php ENDPATH**/ ?>