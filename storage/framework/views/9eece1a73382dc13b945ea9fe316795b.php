<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php
        $permissionTypes = [
            'clients' => 'العملاء',
            'suppliers' => 'الموردين',
            'funds' => 'الصناديق',
            'banks' => 'البنوك',
            'employees' => 'الموظفين',
            'warhouses' => 'المخازن',
            'expenses' => 'المصروفات',
            'revenues' => 'الايرادات',
            'creditors' => 'دائنين متنوعين',
            'debtors' => 'مدينين متنوعين',
            'partners' => 'الشركاء',
            'current-partners' => 'جارى الشركاء',
            'assets' => 'الأصول الثابتة',
            'rentables' => 'الأصول القابلة للتأجير',
            'check-portfolios-incoming' => 'حافظات أوراق القبض',
            'check-portfolios-outgoing' => 'حافظات أوراق الدفع',
        ];

        $parentCodes = [
            'clients' => '1103', // العملاء
            'suppliers' => '2101', // الموردين
            'banks' => '1102', // البنوك
            'funds' => '1101', // الصناديق
            'warhouses' => '1104', // المخازن
            'expenses' => '5', // المصروفات
            'revenues' => '42', // الايرادات
            'creditors' => '2104', // دائنين اخرين
            'debtors' => '1106', // مدينين آخرين
            'partners' => '31', // الشريك الرئيسي
            'current-partners' => '3201', // جاري الشريك
            'assets' => '12', // الأصول
            'employees' => '2102', // الموظفين
            'rentables' => '1202', // مباني (أصل قابل للإيجار)
            'check-portfolios-incoming' => '1105', // حافظات أوراق القبض
            'check-portfolios-outgoing' => '2103', // حافظات أوراق الدفع
        ];

        $type = request('type');
        $permName = $permissionTypes[$type] ?? null;
        $parentCode = $parentCodes[$type] ?? null;
    ?>

    <div class="container">

        
        <?php if(session('error')): ?>
            <div class="alert alert-danger mt-3">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <section class="content-header">
            <div class="container-fluid">
                <?php echo $__env->make('components.breadcrumb', [
                    'title' => $permName ? __('قائمة الحسابات - ' . $permName) : __('قائمة الحسابات'),
                    'items' => [
                        ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
                        $permName ? ['label' => $permName] : ['label' => __('قائمة الحسابات')],
                    ],
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </section>

        
        <div class="row mt-3 justify-content-between align-items-center">
            <div class="col-md-3">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("إضافة $permName")): ?>
                        <a href="<?php echo e(route('accounts.create', ['parent' => $parentCode])); ?>" class="btn btn-primary">
                            <i class="las la-plus"></i> <?php echo e(__('إضافة حساب جديد')); ?>

                        </a>
                    <?php endif; ?>
            </div>

            <div class="col-md-4 text-end">
                <input class="form-control form-control-lg" type="text" id="itmsearch"
                    placeholder="بحث بالكود | اسم الحساب | ID">
            </div>
        </div>

        
        <div class="card-body px-0 mt-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><?php echo e($permName ? 'قائمة ' . $permName : 'قائمة الحسابات'); ?></h5>
                    <?php if (isset($component)) { $__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-export-actions','data' => ['tableId' => 'myTable','filename' => 'accounts','excelLabel' => 'تصدير Excel','pdfLabel' => 'تصدير PDF','printLabel' => 'طباعة']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-export-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['table-id' => 'myTable','filename' => 'accounts','excel-label' => 'تصدير Excel','pdf-label' => 'تصدير PDF','print-label' => 'طباعة']); ?>
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
                </div>

                <div class="table-responsive" id="printed" style="overflow-x: auto;">
                    <table id="myTable" class="table table-striped table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الرصيد</th>
                                <th>العنوان</th>
                                <th>التليفون</th>
                                <th>ID</th>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(["تعديل $permName", "حذف $permName"])): ?>
                                    <th>عمليات</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e($acc->code); ?> - <?php echo e($acc->aname); ?></td>
                                    <td>
                                        <a class="btn btn-lg btn-outline-dark"
                                            href="<?php echo e(route('account-movement', ['accountId' => $acc['id']])); ?>">
                                            <?php echo e($acc->balance ?? 0.0); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($acc->address ?? '__'); ?></td>
                                    <td><?php echo e($acc->phone ?? '__'); ?></td>
                                    <td><?php echo e($acc->id); ?></td>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(["تعديل $permName", "حذف $permName"])): ?>
                                        <td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("تعديل $permName")): ?>
                                                <a href="<?php echo e(route('accounts.edit', $acc->id)); ?>" class="btn btn-success btn-sm">
                                                    <i class="las la-pen"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("حذف $permName")): ?>
                                                <form action="<?php echo e(route('accounts.destroy', $acc->id)); ?>" method="POST"
                                                    style="display:inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                        <i class="las la-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info py-3 mb-0">
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

    
    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('itmsearch');
                const table = document.getElementById('myTable');
                if (!searchInput || !table) return;

                searchInput.addEventListener('keyup', function() {
                    const filter = this.value.trim().toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(function(row) {
                        let text = row.textContent.replace(/\s+/g, ' ').toLowerCase();
                        row.style.display = (filter === '' || text.indexOf(filter) !== -1) ? '' :
                        'none';
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/accounts/index.blade.php ENDPATH**/ ?>