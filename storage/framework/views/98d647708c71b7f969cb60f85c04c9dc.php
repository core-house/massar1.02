<?php
    $purchases = [
        11 => 'فاتورة مشتريات',
        13 => 'مردود مشتريات',
        15 => 'أمر شراء',
        17 => 'عرض سعر من مورد',
        24 => 'فاتورة خدمه',
        25 => 'طلب احتياج',
    ];
?>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('purchases.statistics')); ?>">
        <i class="ti-control-record"></i>Purchases Statistics
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('invoice-templates.index')); ?>">
        <i class="ti-control-record"></i>نماذج الفواتير
    </a>
</li>

<?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('invoices.index', ['type' => $type])); ?>">
            <i class="ti-control-record"></i> <?php echo e(__($label)); ?>

        </a>
    </li>
    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('invoices.track.search')); ?>">
        <i class="ti-control-record"></i> تتبع مسار الفاتورة
    </a>
</li>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/purchases-invoices.blade.php ENDPATH**/ ?>