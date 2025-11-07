
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo e($balance ? 'تعديل رصيد الإجازة' : 'إضافة رصيد إجازة جديد'); ?></h3>
                        <div class="card-tools">
                            <a href="<?php echo e(route('leaves.balances.index')); ?>" class="btn btn-secondary font-family-cairo fw-bold">
                                <i class="fas fa-arrow-left"></i>
                                العودة للقائمة
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form wire:submit="save">
                            <!-- رسائل الخطأ العامة -->
                            <!--[if BLOCK]><![endif]--><?php if($errors->has('general')): ?>
                                <div class="alert alert-danger font-family-cairo fw-bold">
                                    <?php echo e($errors->first('general')); ?>

                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                            <div class="row">
                                <!-- الموظف -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_id" class="form-label">الموظف <span class="text-danger">*</span></label>
                                        <select wire:model="employee_id" id="employee_id" class="form-select <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> font-family-cairo fw-bold font-14">
                                            <option value="">اختر الموظف</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback font-family-cairo fw-bold"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- نوع الإجازة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="leave_type_id" class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                                        <select wire:model="leave_type_id" id="leave_type_id" class="form-select <?php $__errorArgs = ['leave_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> font-family-cairo fw-bold font-14">
                                            <option value="">اختر نوع الإجازة</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['leave_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback font-family-cairo fw-bold"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- السنة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="year" class="form-label">السنة <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="year" 
                                               id="year"
                                               min="2020" 
                                               max="2030" 
                                               class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- الرصيد الافتتاحي -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opening_balance_days" class="form-label">الرصيد الافتتاحي (أيام) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="opening_balance_days" 
                                               id="opening_balance_days"
                                               step="0.5" 
                                               min="0" 
                                               class="form-control <?php $__errorArgs = ['opening_balance_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['opening_balance_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- الأيام المتراكمة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="accrued_days" class="form-label">الأيام المتراكمة <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="accrued_days" 
                                               id="accrued_days"
                                               step="0.5" 
                                               min="0" 
                                               class="form-control <?php $__errorArgs = ['accrued_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['accrued_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- الأيام المستخدمة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="used_days" class="form-label">الأيام المستخدمة <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="used_days" 
                                               id="used_days"
                                               step="0.5" 
                                               min="0" 
                                               class="form-control <?php $__errorArgs = ['used_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['used_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- الأيام المعلقة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pending_days" class="form-label">الأيام المعلقة <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="pending_days" 
                                               id="pending_days"
                                               step="0.5" 
                                               min="0" 
                                               class="form-control <?php $__errorArgs = ['pending_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pending_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>

                                <!-- الأيام المحولة -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="carried_over_days" class="form-label">الأيام المحولة <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               wire:model="carried_over_days" 
                                               id="carried_over_days"
                                               step="0.5" 
                                               min="0" 
                                               class="form-control <?php $__errorArgs = ['carried_over_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['carried_over_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>

                            <!-- ملاحظات -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">ملاحظات</label>
                                        <textarea wire:model="notes" 
                                                  id="notes"
                                                  rows="3" 
                                                  class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                  placeholder="أضف ملاحظات إضافية..."></textarea>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </div>

                            <!-- ملخص الرصيد -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">ملخص الرصيد</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <div class="border-end">
                                                        <h3 class="text-primary"><?php echo e(number_format($this->remaining_days, 1)); ?></h3>
                                                        <p class="text-muted mb-0">الأيام المتبقية</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border-end">
                                                        <h3 class="text-success"><?php echo e(number_format($this->opening_balance_days + $this->accrued_days + $this->carried_over_days, 1)); ?></h3>
                                                        <p class="text-muted mb-0">إجمالي الرصيد</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <h3 class="text-danger"><?php echo e(number_format($this->used_days + $this->pending_days, 1)); ?></h3>
                                                    <p class="text-muted mb-0">إجمالي المستخدم</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الإجراءات -->
                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <a href="<?php echo e(route('leaves.balances.index')); ?>" class="btn btn-secondary">
                                        إلغاء
                                    </a>
                                    <button type="submit" 
                                            class="btn btn-primary"
                                            wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            <?php echo e($balance ? 'تحديث' : 'حفظ'); ?>

                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin"></i>
                                            جاري الحفظ...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/livewire/hr-management/leaves/leave-balances/create-edit.blade.php ENDPATH**/ ?>