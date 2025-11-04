<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.breadcrumb', [
        'title' => __('Journals'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Journals')]],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="card-header">
        
        <a href="<?php echo e(route('multi-journals.create')); ?>" type="button" class="btn btn-primary"><?php echo e(__('Add New')); ?>

            <i class="fas fa-plus me-2"></i>
        </a>
        
    </div>
    <div class="card-body">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-striped mb-0" style="min-width: 1200px;">
                <thead class="table-light text-center align-middle">

                    <tr>
                        <th class="font-family-cairo fw-bold font-14 text-center">#</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">التاريخ</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">رقم العمليه</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">نوع العمليه</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">البيان</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">المبلغ</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">من حساب</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">الي حساب</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">الموظف</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">الموظف 2 </th>
                        <th class="font-family-cairo fw-bold font-14 text-center">المستخدم</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">تم الانشاء في </th>
                        <th class="font-family-cairo fw-bold font-14 text-center">ملاحظات</th>
                        <th class="font-family-cairo fw-bold font-14 text-center">تم المراجعه</th>
                        <th class="font-family-cairo fw-bold font-14 text-center" class="text-end">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $multis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $multi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($loop->iteration); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->pro_date); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->pro_id); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->type->ptext ?? '—'); ?>

                            </td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->details); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->pro_value); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center">
                                <?php echo e($multi->account1->aname ?? 'مذكروين'); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center">
                                <?php echo e($multi->account2->aname ?? 'مذكروين'); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->emp1->aname ?? ''); ?>

                            </td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->emp2->aname ?? ''); ?>

                            </td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->user); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->created_at); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($multi->info); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center">
                                <?php echo e($multi->confirmed ? 'نعم' : 'لا'); ?></td>
                            <td class="font-family-cairo fw-bold font-14 text-center" x-show="columns[16]">
                                <button>
                                    <a href="<?php echo e(route('multi-journals.edit', $multi)); ?>" class="btn btn-primary font-16"><i
                                            class="las la-eye"></i></a>
                                </button>
                                <form action="<?php echo e(route('multi-journals.destroy', $multi->id)); ?>" method="POST"
                                    style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-danger btn-icon-square-sm"
                                        onclick="return confirm(' أنت متأكد انك عايز تمسح العملية و القيد المصاحب لها؟')">
                                        <i class="las la-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="15" class="text-center">
                                <div class="alert alert-info py-3 mb-0" style="font-size: 1.2rem; font-weight: 500;">
                                    <i class="las la-info-circle me-2"></i>
                                    لا توجد بيانات
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/multi-journals/index.blade.php ENDPATH**/ ?>