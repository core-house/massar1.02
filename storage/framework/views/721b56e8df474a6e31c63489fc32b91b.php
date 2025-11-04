<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.accounts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.breadcrumb', [
        'title' => __('انشاء حساب'),
        'items' => [['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')], ['label' => __('انشاء')]],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <?php
                    $parent = request()->get('parent');
                    $isClientOrSupplier = in_array($parent, ['1103', '2101']); // تحديد ما إذا كان حساب عميل أو مورد
                ?>
                <?php
                    // خريطة تربط parent_id بنوع الحساب
                    $parentTypeMap = [
                        '1103' => '1', // العملاء
                        '2101' => '2', // الموردين
                        '1101' => '3', // الصناديق
                        '1102' => '4', // البنوك
                        '2102' => '5', //الموظفين
                        '1104' => '6', // المخازن
                        '5'    => '7', // المصروفات
                        '42'   => '8', // الإيرادات
                        '2104' => '9', // دائنين اخرين
                        '1106' => '10', // مدينين آخرين
                        '31'   => '11', // الشريك الرئيسي
                        '32'   => '12', // جاري الشريك
                        '12'   => '13', // الأصول
                        '1202' => '14', // الأصول القابلة للتأجير
                        '1105' => '17', // حافظات أوراق القبض
                        '2103' => '18', // حافظات أوراق الدفع
                    ];
                    $type = $parentTypeMap[$parent] ?? '0';
                ?>

                <section class="content">
                    <?php if(in_array($parent, [
                            '1103',
                            '2101',
                            '1105',
                            '2103',
                            '1101',
                            '1102',
                            '5',
                            '42',
                            '2104',
                            '1106',
                            '31',
                            '32',
                            '12',
                            '2102',
                            '1202',
                            '1104',
                        ])): ?>
                        <form id="myForm" action="<?php echo e(route('accounts.store')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="q" value="<?php echo e($parent); ?>">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3><?php echo e(__('اضافة حساب')); ?></h3>
                                </div>
                                <div class="card-body">
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger">
                                            <ul>
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                                <input
                                                    type="text"
                                                    class="form-control font-bold"
                                                    id="type"
                                                    name="acc_type"
                                                    value="<?php echo e($type); ?>"
                                                    readonly hidden
                                                >

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="code"><?php echo e(__('الكود')); ?></label><span
                                                    class="text-danger">*</span>
                                                <input readonly required class="form-control font-bold" type="text"
                                                    name="code" value="<?php echo e($last_id); ?>" id="code">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="aname"><?php echo e(__('الاسم')); ?></label><span
                                                    class="text-danger">*</span>
                                                <input required class="form-control font-bold frst" type="text"
                                                    name="aname" id="aname">
                                                <div id="resaname"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="is_basic"><?php echo e(__('نوع الحساب')); ?></label><span
                                                    class="text-danger">*</span>
                                                <select class="form-control font-bold" name="is_basic" id="is_basic">
                                                    <option value="1"><?php echo e(__('اساسي')); ?></option>
                                                    <option selected value="0"><?php echo e(__('حساب عادي')); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="parent_id"><?php echo e(__('يتبع ل')); ?></label><span
                                                    class="text-danger">*</span>
                                                <select class="form-control font-bold" name="parent_id" id="parent_id">
                                                    <?php $__currentLoopData = $resacs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowacs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($rowacs->id); ?>">
                                                            <?php echo e($rowacs->code); ?> - <?php echo e($rowacs->aname); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="branch_id"><?php echo e(__('الفرع')); ?></label>
                                                <select class="form-control font-bold" name="branch_id" id="branch_id">
                                                    <option value=""><?php echo e(__('اختر الفرع')); ?></option>
                                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="phone"><?php echo e(__('تليفون')); ?></label>
                                                <input class="form-control font-bold" type="text" name="phone"
                                                    id="phone" placeholder="<?php echo e(__('التليفون او تليفون المسؤول')); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <?php if($isClientOrSupplier): ?>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="zatca_name"><?php echo e(__('الاسم التجاري (ZATCA)')); ?></label>
                                                    <input class="form-control" type="text" name="zatca_name"
                                                        id="zatca_name" placeholder="<?php echo e(__('الاسم التجاري')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="vat_number">الرقم الضريبي (VAT)</label>
                                                    <input class="form-control" type="text" name="vat_number"
                                                        id="vat_number" placeholder="<?php echo e(__('الرقم الضريبي')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="national_id">رقم الهوية</label>
                                                    <input class="form-control" type="text" name="national_id"
                                                        id="national_id" placeholder="<?php echo e(__('رقم الهوية')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="zatca_address">العنوان الوطني (ZATCA)</label>
                                                    <input class="form-control" type="text" name="zatca_address"
                                                        id="zatca_address" placeholder="<?php echo e(__('العنوان الوطني')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="company_type">نوع العميل</label>
                                                    <select class="form-control" name="company_type" id="company_type">
                                                        <option value=""><?php echo e(__('اختر النوع')); ?></option>
                                                        <option value="شركة">شركة</option>
                                                        <option value="فردي">فردي</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nationality">الجنسية</label>
                                                    <input class="form-control" type="text" name="nationality"
                                                        id="nationality" placeholder="<?php echo e(__('الجنسية')); ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <?php if (isset($component)) { $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $attributes; } ?>
<?php $component = App\View\Components\DynamicSearch::resolve(['name' => 'country_id','label' => 'الدولة','column' => 'title','model' => 'App\Models\Country','placeholder' => 'ابحث عن الدولة...','required' => false,'class' => 'form-select'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\DynamicSearch::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $attributes = $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $component = $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <?php if (isset($component)) { $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $attributes; } ?>
<?php $component = App\View\Components\DynamicSearch::resolve(['name' => 'city_id','label' => 'المدينة','column' => 'title','model' => 'App\Models\City','placeholder' => 'ابحث عن المدينة...','required' => false,'class' => 'form-select'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\DynamicSearch::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $attributes = $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $component = $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <?php if (isset($component)) { $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $attributes; } ?>
<?php $component = App\View\Components\DynamicSearch::resolve(['name' => 'state_id','label' => 'المنطقة','column' => 'title','model' => 'App\Models\State','placeholder' => 'ابحث عن المنطقة...','required' => false,'class' => 'form-select'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\DynamicSearch::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $attributes = $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $component = $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <?php if (isset($component)) { $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b = $attributes; } ?>
<?php $component = App\View\Components\DynamicSearch::resolve(['name' => 'town_id','label' => 'الحي','column' => 'title','model' => 'App\Models\Town','placeholder' => 'ابحث عن الحي...','required' => false,'class' => 'form-select'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\DynamicSearch::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $attributes = $__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__attributesOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b)): ?>
<?php $component = $__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b; ?>
<?php unset($__componentOriginal5892ed0ac50cfeb55a6dee6c6ce7b69b); ?>
<?php endif; ?>
                                            </div>

                                        </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="is_stock">مخزون</label>
                                                <input type="checkbox" name="is_stock" value="0" hidden>
                                                <input type="checkbox" name="is_stock" id="is_stock"
                                                    <?php echo e($parent == '123' ? 'checked' : ''); ?>>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="secret">حساب سري</label>
                                                <input type="checkbox" name="secret" id="secret">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="is_fund">حساب صندوق</label>
                                                <input type="checkbox" name="is_fund" id="is_fund"
                                                    <?php echo e($parent == '121' ? 'checked' : ''); ?>>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="rentable">أصل قابل للتأجير</label>
                                                <input type="checkbox" name="rentable" id="rentable"
                                                    <?php echo e($parent == '112' ? 'checked' : ''); ?>>
                                            </div>
                                        </div>

                                        <?php if($parent == 44): ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="employees_expensses">حساب رواتب للموظفين</label>
                                                    <input type="checkbox" name="employees_expensses"
                                                        id="employees_expensses">
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <?php if($parent == '12'): ?>
                                        <div class="alert alert-warning"
                                            style="font-family: 'Cairo', sans-serif; direction: rtl;">
                                            <?php echo e(__('سيتم اضافة حساب مجمع اهلاك و حساب مصروف اهلاك للأصل')); ?>

                                        </div>
                                                   <input hidden type="text" readonly name="reserve" id="reserve" value="1">
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($component)) { $__componentOriginal1827ccb0bb7a7a47de69354725d7f163 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1827ccb0bb7a7a47de69354725d7f163 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'branches::components.branch-select','data' => ['branches' => $branches]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('branches::branch-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['branches' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($branches)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1827ccb0bb7a7a47de69354725d7f163)): ?>
<?php $attributes = $__attributesOriginal1827ccb0bb7a7a47de69354725d7f163; ?>
<?php unset($__attributesOriginal1827ccb0bb7a7a47de69354725d7f163); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1827ccb0bb7a7a47de69354725d7f163)): ?>
<?php $component = $__componentOriginal1827ccb0bb7a7a47de69354725d7f163; ?>
<?php unset($__componentOriginal1827ccb0bb7a7a47de69354725d7f163); ?>
<?php endif; ?>

                                </div>

                                <div class="card-footer">
                                    <div class="d-flex justify-content-start">
                                        <button class="btn btn-success m-1" type="submit">
                                            <i class="las la-save"></i> تأكيد
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <p>خطأ في تحديد نوع الحساب</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </section>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/accounts/create.blade.php ENDPATH**/ ?>