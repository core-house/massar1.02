<li>
    <div class="tree-item <?php echo e($account->is_basic == 1 ? 'basic-account' : ''); ?>">
        <div class="account-info">
            <?php if($account->children->count()): ?>
                <i class="fas fa-folder-open text-warning" style="font-size: 1.3rem;"></i>
            <?php else: ?>
                <i class="fas fa-file-invoice-dollar text-info" style="font-size: 1.2rem;"></i>
            <?php endif; ?>

            <span class="account-code"><?php echo e($account->code); ?></span>
            <span class="account-name"><?php echo e($account->aname); ?></span>

            <?php if($account->is_basic == 1): ?>
                <span class="basic-badge">حساب أساسي</span>
            <?php endif; ?>

            <?php if($account->is_basic == 1 && $account->children->count()): ?>
                <span class="children-count"><?php echo e($account->children->count()); ?></span>
            <?php endif; ?>

            <span class="account-balance <?php echo e($account->balance < 0 ? 'negative' : ''); ?>">
                <?php echo e(number_format($account->balance, 2)); ?>

            </span>
        </div>

        <?php if($account->children->count()): ?>
            <span class="toggle-icon">
                <i class="fas fa-minus"></i>
            </span>
        <?php endif; ?>
    </div>

    <?php if($account->children->count()): ?>
        <ul class="nested">
            <?php $__currentLoopData = $account->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('reports.partials.account-node', ['account' => $child], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</li>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/reports/partials/account-node.blade.php ENDPATH**/ ?>