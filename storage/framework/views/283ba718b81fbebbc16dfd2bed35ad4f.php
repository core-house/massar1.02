<div class="container-fluid" dir="rtl">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">معالجة دفاتر الحضور</h4>
            </div>
        </div>
    </div>
 
    
    <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php if(session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <!--[if BLOCK]><![endif]--><?php if(session('error_type') == 'overlap'): ?>
                        <pre class="mb-0" style="white-space: pre-wrap; font-family: 'Cairo', sans-serif; font-size: 0.95rem; line-height: 1.6;"><?php echo e(session('error')); ?></pre>
                    <?php else: ?>
                        <?php echo e(session('error')); ?>

                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6><i class="fas fa-exclamation-circle"></i> يرجى تصحيح الأخطاء التالية:</h6>
            <ul class="mb-0">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                
                <div class="card-header bg-gradient-primary text-white border-0 py-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <i class="fas fa-cogs text-black fs-5"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 fw-bold">معالجة جديدة</h5>
                            <p class="card-subtitle mb-0 opacity-75 small">قم بمعالجة الحضور والانصراف للموظفين</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form wire:submit.prevent="processAttendance" wire:loading.attr="disabled">
                        
                        

                        
                        <div class="form-section">
                            <div class="row g-4">
                                
                                <div class="col-12 col-lg-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fas fa-filter text-primary me-2"></i>
                                            نوع المعالجة 
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="form-control-wrapper">
                                            <select wire:model.live.debounce.300ms="processingType" 
                                                    class="form-select form-select-modern <?php $__errorArgs = ['processingType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                <option value="single">
                                                    <i class="fas fa-user"></i> موظف واحد
                                                </option>
                                                <option value="multiple">
                                                    <i class="fas fa-users"></i> عدة موظفين
                                                </option>
                                                <option value="department">
                                                    <i class="fas fa-building"></i> قسم كامل
                                                </option>
                                            </select>
                                            <div class="form-control-icon">
                                                <i class="fas fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['processingType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                <?php echo e($message); ?>

                                            </div> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                
                                <div class="col-12 col-lg-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern mb-2">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            تاريخ البداية
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="form-control-wrapper">
                                            <input type="date" 
                                                wire:model="startDate" 
                                                class="form-control form-control-modern <?php $__errorArgs = ['startDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                style="font-family: 'Cairo', sans-serif; direction: rtl;">
                                            <div class="form-control-icon">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['startDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                <?php echo e($message); ?>

                                            </div> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                
                                <div class="col-12 col-lg-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern mb-2">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            تاريخ النهاية
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="form-control-wrapper">
                                            <input type="date" 
                                                wire:model="endDate" 
                                                class="form-control form-control-modern <?php $__errorArgs = ['endDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                style="font-family: 'Cairo', sans-serif; direction: rtl;">
                                            <div class="form-control-icon">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['endDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                            <div class="invalid-feedback-modern">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                <?php echo e($message); ?>

                                            </div> 
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>

                            
                            <div class="selection-section mt-4 pt-4 border-top border-light">
                                <div class="selection-wrapper" wire:key="selection-column-<?php echo e($processingType); ?>">
                                    
                                    <div class="selection-content-container">
                                        
                                        <!--[if BLOCK]><![endif]--><?php if($processingType === 'single'): ?>
                                            <div class="selection-content" wire:loading.remove wire:target="processingType">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern">
                                                        <i class="fas fa-user-check text-success me-2"></i>
                                                        اختيار الموظف 
                                                        <span class="text-danger ms-1">*</span>
                                                    </label>
                                                    <div class="select-wrapper" wire:key="tom-select-single-employee">
                                                        <?php if (isset($component)) { $__componentOriginal391e5bef920d393958d3dc69b840c47c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal391e5bef920d393958d3dc69b840c47c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tom-select','data' => ['wireModel' => 'selectedEmployee','name' => 'selectedEmployee','id' => 'selectedEmployee','required' => true,'options' => collect($employees)->map(function($employee) {
                                                                return [
                                                                    'id' => $employee->id,
                                                                    'text' => $employee->name . ' - ' . ($employee->department?->title ?? 'بدون قسم')
                                                                ];
                                                            })->toArray(),'placeholder' => '🔍 ابحث واختر موظف...','search' => true,'create' => false,'multiple' => false,'maxItems' => 1,'maxOptions' => 1000,'allowEmptyOption' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tom-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wireModel' => 'selectedEmployee','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedEmployee'),'id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedEmployee'),'required' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($employees)->map(function($employee) {
                                                                return [
                                                                    'id' => $employee->id,
                                                                    'text' => $employee->name . ' - ' . ($employee->department?->title ?? 'بدون قسم')
                                                                ];
                                                            })->toArray()),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('🔍 ابحث واختر موظف...'),'search' => true,'create' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'multiple' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'max-items' => 1,'max-options' => 1000,'allow-empty-option' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $attributes = $__attributesOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__attributesOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $component = $__componentOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__componentOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
                                                    </div>
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedEmployee'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                        <div class="invalid-feedback-modern">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            <?php echo e($message); ?>

                                                        </div> 
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <!--[if BLOCK]><![endif]--><?php if($processingType === 'multiple'): ?>
                                            <div class="selection-content" wire:loading.remove wire:target="processingType">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern">
                                                        <i class="fas fa-users text-info me-2"></i>
                                                        اختيار الموظفين 
                                                        <span class="text-danger ms-1">*</span>
                                                        <span class="badge bg-info bg-opacity-10 text-white ms-2 px-2 py-1 rounded-pill small text-white">
                                                            متعدد
                                                            <i class="fas fa-users"></i>
                                                        </span>
                                                    </label>
                                                    <div class="select-wrapper" wire:key="tom-select-multiple-employees">
                                                        <?php if (isset($component)) { $__componentOriginal391e5bef920d393958d3dc69b840c47c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal391e5bef920d393958d3dc69b840c47c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tom-select','data' => ['wireModel' => 'selectedEmployees','name' => 'selectedEmployees','id' => 'selectedEmployees','required' => true,'options' => collect($employees)->map(function($employee) {
                                                                return [
                                                                    'id' => $employee->id,
                                                                    'text' => $employee->name . ' - ' . ($employee->department?->title ?? 'بدون قسم')
                                                                ];
                                                            })->toArray(),'placeholder' => '🔍 ابحث واختر موظفين متعددين...','search' => true,'multiple' => true,'maxItems' => 1000,'maxOptions' => 1000]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tom-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wireModel' => 'selectedEmployees','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedEmployees'),'id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedEmployees'),'required' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($employees)->map(function($employee) {
                                                                return [
                                                                    'id' => $employee->id,
                                                                    'text' => $employee->name . ' - ' . ($employee->department?->title ?? 'بدون قسم')
                                                                ];
                                                            })->toArray()),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('🔍 ابحث واختر موظفين متعددين...'),'search' => true,'multiple' => true,'max-items' => 1000,'max-options' => 1000]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $attributes = $__attributesOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__attributesOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $component = $__componentOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__componentOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
                                                    </div>
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedEmployees'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                        <div class="invalid-feedback-modern">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            <?php echo e($message); ?>

                                                        </div> 
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <!--[if BLOCK]><![endif]--><?php if($processingType === 'department'): ?>
                                            <div class="selection-content" wire:loading.remove wire:target="processingType">
                                                <div class="form-group-modern">
                                                    <label class="form-label-modern">
                                                        <i class="fas fa-building text-warning me-2"></i>
                                                        اختيار القسم 
                                                        <span class="text-danger ms-1">*</span>
                                                        <span class="badge bg-warning bg-opacity-10 text-white ms-2 px-2 py-1 rounded-pill small">
                                                            قسم كامل
                                                            <i class="fas fa-building"></i>
                                                        </span>
                                                    </label>
                                                    <div class="select-wrapper">
                                                        <?php if (isset($component)) { $__componentOriginal391e5bef920d393958d3dc69b840c47c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal391e5bef920d393958d3dc69b840c47c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.tom-select','data' => ['wireModel' => 'selectedDepartment','name' => 'selectedDepartment','id' => 'selectedDepartment','required' => true,'options' => collect($departments)->map(function($department) {
                                                                return [
                                                                    'id' => $department->id,
                                                                    'text' => $department->title
                                                                ];
                                                            })->toArray(),'placeholder' => '🏢 اختر قسم...','search' => true,'maxItems' => 1,'maxOptions' => 1000]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('tom-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wireModel' => 'selectedDepartment','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedDepartment'),'id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('selectedDepartment'),'required' => true,'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($departments)->map(function($department) {
                                                                return [
                                                                    'id' => $department->id,
                                                                    'text' => $department->title
                                                                ];
                                                            })->toArray()),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('🏢 اختر قسم...'),'search' => true,'max-items' => 1,'max-options' => 1000]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $attributes = $__attributesOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__attributesOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal391e5bef920d393958d3dc69b840c47c)): ?>
<?php $component = $__componentOriginal391e5bef920d393958d3dc69b840c47c; ?>
<?php unset($__componentOriginal391e5bef920d393958d3dc69b840c47c); ?>
<?php endif; ?>
                                                    </div>
                                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedDepartment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                                        <div class="invalid-feedback-modern">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            <?php echo e($message); ?>

                                                        </div> 
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                        
                                        <div class="loading-overlay" wire:loading wire:target="processingType">
                                            <div class="loading-content">
                                                <div class="loading-spinner">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">جاري التحميل...</span>
                                                    </div>
                                                </div>
                                                <div class="loading-text mt-3">
                                                    <h6 class="mb-1">جاري تحديث النموذج...</h6>
                                                    <p class="text-muted small mb-0">يرجى الانتظار لحظة</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="notes-section mt-4 pt-4 border-top border-light">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-sticky-note text-secondary me-2"></i>
                                        ملاحظات
                                        <span class="badge bg-secondary bg-opacity-10 text-white ms-2 px-2 py-1 rounded-pill small">
                                            اختياري
                                        </span>
                                    </label>
                                    <div class="form-control-wrapper">
                                        <textarea wire:model="notes" 
                                                class="form-control form-control-modern <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                rows="4" 
                                                placeholder="أضف أي ملاحظات أو تعليقات تخص هذه المعالجة..."></textarea>
                                        <div class="form-control-icon textarea-icon">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                                        <div class="invalid-feedback-modern">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            <?php echo e($message); ?>

                                        </div> 
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            
                            <div class="action-section mt-5 pt-4 border-top border-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    
                                    <div class="action-buttons">
                                        <button type="submit" 
                                                class="btn btn-primary btn-lg px-4 py-3 rounded-pill shadow-sm" 
                                                wire:loading.attr="disabled" 
                                                wire:target="processAttendance">
                                            <span wire:loading.remove wire:target="processAttendance">
                                                <i class="fas fa-rocket me-2"></i>
                                                بدء المعالجة
                                            </span>
                                            <span wire:loading wire:target="processAttendance">
                                                <span class="spinner-border spinner-border-sm me-2" role="status">
                                                    <span class="visually-hidden">جاري المعالجة...</span>
                                                </span>
                                                جاري المعالجة...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجل المعالجات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>رقم المعالجة</th>
                                    <th>النوع</th>
                                    <th>الموظف/القسم</th>
                                    <th>الفترة</th>
                                    <th>أيام عمل فعليه أساسية</th>
                                    <th>أيام عمل فعليه إضافية</th>
                                    <th>أيام غياب</th>
                                    <th>ساعات تأخير</th>
                                    <th>ساعات إضافية</th>
                                    <th>إجمالي الراتب</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $processings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>#<?php echo e($processing->id); ?></td>
                                        <td><?php echo e($processing->type_label); ?></td>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if($processing->employee): ?>
                                                <?php echo e($processing->employee->name); ?>

                                            <?php elseif($processing->department): ?>
                                                <?php echo e($processing->department->title); ?>

                                            <?php else: ?>
                                                متعدد
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td>
                                            <?php echo e($processing->period_start->format('Y-m-d')); ?> - 
                                            <?php echo e($processing->period_end->format('Y-m-d')); ?>

                                        </td>
                                        <td><?php echo e(number_format($processing->actual_work_days, 2)); ?></td>
                                        <td><?php echo e(number_format($processing->overtime_work_days, 2)); ?></td>
                                        <td><?php echo e(number_format($processing->absent_days, 2)); ?></td>
                                        <td><?php echo e(number_format($processing->total_late_hours, 2)); ?></td>
                                        <td><?php echo e(number_format($processing->overtime_work_hours, 2)); ?></td>
                                        <td><?php echo e(number_format($processing->total_salary, 2)); ?></td>
                                        <td><?php echo $processing->status_badge; ?></td>
                                        <td><?php echo e($processing->created_at->format('Y-m-d H:i')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        wire:click="viewProcessingDetails(<?php echo e($processing->id); ?>)">
                                                    <i class="fas fa-eye"></i> التفاصيل
                                                </button>
                                                
                                                <!--[if BLOCK]><![endif]--><?php if($processing->status === 'pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            wire:click="approveProcessing(<?php echo e($processing->id); ?>)"
                                                            onclick="return confirm('هل أنت متأكد من اعتماد هذه المعالجة؟')">
                                                        <i class="fas fa-check"></i> اعتماد
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            wire:click="rejectProcessing(<?php echo e($processing->id); ?>)"
                                                            onclick="return confirm('هل أنت متأكد من رفض هذه المعالجة؟')">
                                                        <i class="fas fa-times"></i> رفض
                                                    </button>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                
                                                <!--[if BLOCK]><![endif]--><?php if($processing->status === 'pending' || $processing->status === 'rejected'): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            wire:click="deleteProcessing(<?php echo e($processing->id); ?>)"
                                                            wire:confirm="هل أنت متأكد من حذف هذه المعالجة؟ سيتم حذف جميع التفاصيل المرتبطة بها نهائياً."
                                                            title="حذف المعالجة">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">لا توجد معالجات</td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showDetails && $selectedProcessing): ?>
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header justify-content-between d-flex">
                        <h5 class="modal-title">تفاصيل المعالجة #<?php echo e($selectedProcessing->id); ?></h5>
                        <button type="button" class="btn-close m-2" wire:click="closeDetails"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>الموظف:</strong><br>
                                <?php echo e($selectedProcessing->employee?->name ?? 'متعدد'); ?>

                            </div>
                            <div class="col-md-3">
                                <strong>القسم:</strong><br>
                                <?php echo e($selectedProcessing->department?->title ?? 'متعدد'); ?>

                            </div>
                            <div class="col-md-3">
                                <strong>الفترة:</strong><br>
                                <?php echo e($selectedProcessing->period_start->format('Y-m-d')); ?> - <?php echo e($selectedProcessing->period_end->format('Y-m-d')); ?>

                            </div>
                            <div class="col-md-3">
                                <strong>الحالة:</strong><br>
                                <?php echo $selectedProcessing->status_badge; ?>

                            </div>
                        </div>

                        
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>نوع اليوم</th>
                                        <th>وقت الدخول</th>
                                        <th>وقت الخروج</th>
                                        <th>ساعات أساسية</th>
                                        <th>ساعات فعلية</th>
                                        <th>ساعات إضافية</th>
                                        <th>ساعات تأخير</th>
                                        <th>الراتب اليومي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $processingDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($detail->attendance_date->format('Y-m-d')); ?></td>
                                            <td><?php echo $detail->status_badge; ?></td>
                                            <td><?php echo $detail->working_day_badge; ?></td>
                                            <td><?php echo e($detail->formatted_check_in_time); ?></td>
                                            <td><?php echo e($detail->formatted_check_out_time); ?></td>
                                            <td><?php echo e(number_format($detail->attendance_basic_hours_count, 2)); ?></td>
                                            <td><?php echo e(number_format($detail->attendance_actual_hours_count, 2)); ?></td>
                                            <td><?php echo e(number_format($detail->attendance_overtime_hours_count, 2)); ?></td>
                                            <td><?php echo e(number_format($detail->attendance_late_hours_count, 2)); ?></td>
                                            <td><?php echo e(number_format($detail->total_due_hourly_salary, 2)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center d-flex">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetails">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <style>
        /* Modern Form Styling */
        .card {
            border-radius: 16px;
            overflow: hidden;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .icon-circle {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .progress-wrapper .progress {
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        
        .progress-bar {
            border-radius: 10px;
        }
        
        .form-group-modern {
            position: relative;
        }
        
        .form-label-modern {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .form-label-small {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        
        .form-control-wrapper {
            position: relative;
        }
        
        .form-control-modern, .form-select-modern {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 50px 14px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
        }
        
        .form-control-modern:focus, .form-select-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #fff;
        }
        
        .form-control-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            pointer-events: none;
        }
        
        .textarea-icon {
            top: 20px;
            transform: none;
        }
        
        .invalid-feedback-modern {
            display: block;
            width: 100%;
            margin-top: 8px;
            font-size: 0.85rem;
            color: #e53e3e;
            background-color: #fed7d7;
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 4px solid #e53e3e;
        }
        
        .date-range-wrapper {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }
        
        .selection-section {
            background: #f8fafc;
            padding: 24px;
            border-radius: 12px;
            margin: 0 -1rem;
        }
        
        .selection-content-container {
            position: relative;
            min-height: 120px;
        }
        
        .selection-content {
            animation: fadeInUp 0.4s ease-out;
            min-height: 120px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(248, 250, 252, 0.9);
            border-radius: 12px;
            backdrop-filter: blur(5px);
            z-index: 10;
            min-height: 120px;
        }
        
        /* Ensure form structure consistency during submission */
        .form-section {
            position: relative;
        }
        
        .form-section:has([wire\\:loading]) {
            pointer-events: none;
        }
        
        .form-section:has([wire\\:loading]) * {
            pointer-events: none;
        }
        
        /* Prevent layout shifts during form submission */
        .card-body {
            position: relative;
        }
        
        .card-body:has([wire\\:loading]) {
            overflow: hidden;
        }
        
        .loading-content {
            text-align: center;
        }
        
        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .notes-section textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .action-section {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 20px;
            border-radius: 12px;
            margin: 0 -1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Tom Select Styling */
        .ts-control {
            border: 2px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 8px 12px !important;
            background-color: #f8fafc !important;
            min-height: 52px !important;
        }
        
        .ts-control.focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
            background-color: #fff !important;
        }
        
        .ts-dropdown {
            border-radius: 12px !important;
            border: 2px solid #e2e8f0 !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .date-range-wrapper,
            .selection-section,
            .action-section {
                margin: 0;
                border-radius: 8px;
            }
            
            .form-control-modern,
            .form-select-modern {
                padding: 12px 40px 12px 12px;
            }
            
            .btn-primary {
                width: 100%;
                margin-top: 1rem;
            }
            
            .action-section .d-flex {
                flex-direction: column;
                text-align: center;
            }
            
            .action-info {
                margin-bottom: 1rem;
            }
        }
    </style>
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/livewire/hr-management/attendances/processing/attendance-processing-manager.blade.php ENDPATH**/ ?>