<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-head">
            <h2><?php echo e(__('الميزانية العمومية')); ?></h2>
            <div class="text-muted"><?php echo e(__('حتى تاريخ:')); ?> <?php echo e($asOfDate ? \Carbon\Carbon::parse($asOfDate)->format('Y-m-d') : now()->format('Y-m-d')); ?></div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="as_of_date"><?php echo e(__('حتى تاريخ:')); ?></label>
                    <input type="date" id="as_of_date" class="form-control" wire:model="asOfDate">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary mt-4" wire:click="generateReport"><?php echo e(__('توليد التقرير')); ?></button>
                </div>
            </div>

            <div class="row">
                <!-- الأصول -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4><?php echo e(__('الأصول')); ?></h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('الحساب')); ?></th>
                                        <th class="text-end"><?php echo e(__('المبلغ')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="<?php echo e($asset->is_basic == 1 ? 'background-color: #f0f0f0;' : ''); ?>">
                                        <td><?php echo e($asset->code); ?> - <?php echo e($asset->aname); ?></td>
                                        <td class="text-end"><?php echo e(number_format($asset->balance, 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="table-primary">
                                        <th><?php echo e(__('إجمالي الأصول')); ?></th>
                                        <th class="text-end"><?php echo e($totalAssets); ?></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- الخصوم وحقوق الملكية -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4><?php echo e(__('الخصوم وحقوق الملكية')); ?></h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('الحساب')); ?></th>
                                        <th class="text-end"><?php echo e(__('المبلغ')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $liabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="<?php echo e($liability->is_basic == 1 ? 'background-color: #f0f0f0;' : ''); ?>">
                                        <td><?php echo e($liability->code); ?> - <?php echo e($liability->aname); ?></td>
                                        <td class="text-end"><?php echo e(number_format($liability->balance, 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php $__currentLoopData = $equity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="<?php echo e($asset->is_basic == 1 ? 'background-color: #f0f0f0;' : ''); ?>">
                                        <td><?php echo e($eq->code); ?> - <?php echo e($eq->aname); ?></td>
                                        <td class="text-end"><?php echo e(number_format($eq->balance, 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="table-primary">
                                        <th><?php echo e(__('ارباح و خسائر ( حساب اقفال)')); ?></th>
                                        <th class="text-end"><?php echo e(number_format($netProfit, 2)); ?></th>
                                    </tr>
                                    <tr class="table-success">
                                        <th><?php echo e(__('إجمالي الخصوم وحقوق الملكية')); ?></th>
                                        <th class="text-end"><?php echo e(number_format($totalLiabilitiesEquity, 2)); ?></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ملخص -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert <?php echo e($totalAssets == $totalLiabilitiesEquity ? 'alert-success' : 'alert-warning'); ?>">
                        <strong><?php echo e(__('النتيجة:')); ?></strong> 
                        <?php if($totalAssets == $totalLiabilitiesEquity): ?>
                            <?php echo e(__('الميزانية متوازنة ✓')); ?>

                        <?php else: ?>
                            <?php echo e(__('الميزانية غير متوازنة - الفرق: :diff', ['diff' => number_format(abs($totalAssets + $totalLiabilitiesEquity), 2)])); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/reports/general-balance-sheet.blade.php ENDPATH**/ ?>