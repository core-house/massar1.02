<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-head">
            <h2>ميزان الحسابات</h2>
            <div class="text-muted">حتى تاريخ: <?php echo e($asOfDate ? \Carbon\Carbon::parse($asOfDate)->format('Y-m-d') : now()->format('Y-m-d')); ?></div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="as_of_date">حتى تاريخ:</label>
                    <input type="date" id="as_of_date" class="form-control" wire:model="asOfDate">
                </div>
                <div class="col-md-3">
                    <label for="account_group">مجموعة الحساب:</label>
                    <select id="account_group" class="form-control" wire:model="accountGroup">
                        <option value="">الكل</option>
                        <option value="1">الأصول</option>
                        <option value="2">الخصوم</option>
                        <option value="3">حقوق الملكية</option>
                        <option value="4">الإيرادات</option>
                        <option value="5">المصروفات</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary mt-4" wire:click="generateReport">توليد التقرير</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>رقم الحساب</th>
                            <th>اسم الحساب</th>
                            <th class="text-end">مدين</th>
                            <th class="text-end">دائن</th>
                            <th class="text-end">الرصيد</th>
                            <th>نوع الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $accountBalances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $balance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($balance->code); ?></td>
                            <td><?php echo e($balance->aname); ?></td>
                            <td class="text-end"><?php echo e($balance->debit > 0 ? number_format($balance->debit, 2) : '---'); ?></td>
                            <td class="text-end"><?php echo e($balance->credit > 0 ? number_format($balance->credit, 2) : '---'); ?></td>
                            <td class="text-end"><?php echo e(number_format($balance->balance, 2)); ?></td>
                            <td>
                                <?php if($balance->balance > 0): ?>
                                    <span class="badge bg-primary">مدين</span>
                                <?php elseif($balance->balance < 0): ?>
                                    <span class="badge bg-success">دائن</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">صفر</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">لا توجد بيانات متاحة.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <th colspan="2">الإجمالي</th>
                            <th class="text-end"><?php echo e(number_format($totalDebit, 2)); ?></th>
                            <th class="text-end"><?php echo e(number_format($totalCredit, 2)); ?></th>
                            <th class="text-end"><?php echo e(number_format($totalBalance, 2)); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if($accountBalances->hasPages()): ?>
                <div class="d-flex justify-content-center">
                    <?php echo e($accountBalances->links()); ?>

                </div>
            <?php endif; ?>

            <!-- ملخص -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert <?php echo e($totalDebit == $totalCredit ? 'alert-success' : 'alert-warning'); ?>">
                        <strong>النتيجة:</strong> 
                        <?php if($totalDebit == $totalCredit): ?>
                            الميزان متوازن ✓
                        <?php else: ?>
                            الميزان غير متوازن - الفرق: <?php echo e(number_format(abs($totalDebit - $totalCredit), 2)); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/reports/general-account-balances.blade.php ENDPATH**/ ?>