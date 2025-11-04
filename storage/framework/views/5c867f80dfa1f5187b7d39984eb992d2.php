<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.statistics')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('Transfers Statistics')); ?>

    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.create', ['type' => 'cash_to_cash'])); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.cash_to_cash_transfer')); ?>

    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.create', ['type' => 'cash_to_bank'])); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.cash_to_bank_transfer')); ?>

    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.create', ['type' => 'bank_to_cash'])); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.bank_to_cash_transfer')); ?>

    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.create', ['type' => 'bank_to_bank'])); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.bank_to_bank_transfer')); ?>

    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('transfers.index')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.cash_transfers')); ?>

    </a>
</li>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/transfers.blade.php ENDPATH**/ ?>