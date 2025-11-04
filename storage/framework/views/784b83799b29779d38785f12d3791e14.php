<?php
    $sales = [
        10 => 'فاتورة مبيعات',
        12 => 'مردود مبيعات',
        14 => 'أمر بيع',
        16 => 'عرض سعر لعميل',
        22 => 'أمر حجز',
    ];
    $viewPermissions = collect($sales)->map(fn($label) => 'عرض ' . $label)->toArray();
?>


<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('sales.statistics')); ?>">
        <i class="ti-control-record"></i>Sales Statistics
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('invoice-templates.index')); ?>">
        <i class="ti-control-record"></i>نماذج الفواتير
    </a>
</li>

<?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض ' . $label)): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('invoices.index', ['type' => $type])); ?>">
                <i class="ti-control-record"></i> <?php echo e(__($label)); ?>

            </a>
        </li>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/sales-invoices.blade.php ENDPATH**/ ?>