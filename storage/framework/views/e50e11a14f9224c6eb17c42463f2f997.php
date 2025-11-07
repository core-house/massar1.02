<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('dashboard.components.summary-cards', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('dashboard.components.summary-tables', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="row">
        <?php for($i = 1; $i <= 20; $i++): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <?php echo $__env->make('dashboard.components.chart' . $i, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endfor; ?>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('dashboard.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/dashboard/charts.blade.php ENDPATH**/ ?>