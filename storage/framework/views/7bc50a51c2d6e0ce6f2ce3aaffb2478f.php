<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.permissions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.breadcrumb', [
        'title' => __('المدراء '),
        'items' => [['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')], ['label' => __('المدراء')]],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="row">
        <div class="col-lg-12">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('إضافة المدراء')): ?>
                <a href="<?php echo e(route('users.create')); ?>" type="button" class="btn btn-primary font-family-cairo fw-bold">
                    اضافه جديده
                    <i class="fas fa-plus me-2"></i>
                </a>
            <?php endif; ?>
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <?php if (isset($component)) { $__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-export-actions','data' => ['tableId' => 'users-table','filename' => 'users-table','excelLabel' => 'تصدير Excel','pdfLabel' => 'تصدير PDF','printLabel' => 'طباعة']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-export-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['table-id' => 'users-table','filename' => 'users-table','excel-label' => 'تصدير Excel','pdf-label' => 'تصدير PDF','print-label' => 'طباعة']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7)): ?>
<?php $attributes = $__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7; ?>
<?php unset($__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7)): ?>
<?php $component = $__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7; ?>
<?php unset($__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7); ?>
<?php endif; ?>

                        <table id="users-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th><?php echo e(__('الاسم')); ?></th>
                                    <th><?php echo e(__('البريد الالكتروني ')); ?></th>
                                    <th><?php echo e(__('الصلاحيات')); ?></th>
                                    <th><?php echo e(__('الفروع')); ?></th>
                                    <th><?php echo e(__('تم الانشاء في ')); ?></th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['تعديل المدراء', 'حذف المدراء'])): ?>
                                        <th><?php echo e(__('العمليات')); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="text-center">
                                        <td class="font-family-cairo fw-bold font-14 text-center"> <?php echo e($loop->iteration); ?>

                                        </td>
                                        <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($user->name); ?></td>
                                        <td class="font-family-cairo fw-bold font-14 text-center"><?php echo e($user->email); ?></td>
                                        <td class="font-family-cairo fw-bold font-14 text-center">
                                            <span
                                                class="badge bg-primary"><?php echo e($user->permissions->count()); ?></span>
                                        </td>
                                        <td class="font-family-cairo fw-bold font-14 text-center">
                                            <?php $__currentLoopData = $user->branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge bg-info text-dark"><?php echo e($branch->name); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td><?php echo e($user->created_at->format('Y-m-d')); ?></td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['تعديل المدراء', 'حذف المدراء'])): ?>
                                            <td>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('تعديل المدراء')): ?>
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="<?php echo e(route('users.edit', $user->id)); ?>">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('حذف المدراء')): ?>
                                                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا التخصص؟');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="13" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
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
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/users/index.blade.php ENDPATH**/ ?>