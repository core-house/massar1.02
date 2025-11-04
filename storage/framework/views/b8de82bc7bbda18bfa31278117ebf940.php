<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.breadcrumb', [
        'title' => __('Start Balance'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Start Balance')]],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('accounts.startBalance.manage-start-balance', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-2802638948-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/accounts/startBalance/manage-start-balance.blade.php ENDPATH**/ ?>