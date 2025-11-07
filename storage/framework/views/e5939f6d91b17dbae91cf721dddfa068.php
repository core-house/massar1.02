<?php
    $inventory = [
        18 => 'فاتورة تالف',
        19 => 'فواتير أمر صرف',
        20 => 'أمر إضافة',
        21 => 'تحويل من مخزن لمخزن',
    ];
    $viewPermissions = collect($inventory)->map(fn($label) => 'عرض ' . $label)->toArray();
?>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('inventory.statistics')); ?>">
        <i class="ti-control-record"></i>Inventory Statistics
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('invoice-templates.index')); ?>">
        <i class="ti-control-record"></i>نماذج الفواتير
    </a>
</li>

<?php $__currentLoopData = $inventory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض ' . $label)): ?>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('invoices.index', ['type' => $type])); ?>">
                <i class="ti-control-record"></i> <?php echo e(__($label)); ?>

            </a>
        </li>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/inventory-invoices.blade.php ENDPATH**/ ?>