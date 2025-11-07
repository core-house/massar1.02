<?php

use Livewire\Volt\Component;
use App\Models\LeaveType;
use Livewire\WithPagination;

?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
<div>
                    <h2 class="mb-0">إدارة أنواع الإجازات</h2>
                    <p class="text-muted mb-0">إدارة أنواع الإجازات المختلفة في النظام</p>
                </div>
                <button type="button" class="btn btn-primary" wire:click="openModal">
                    <i class="fas fa-plus me-2"></i>إضافة نوع إجازة جديد
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="البحث في أنواع الإجازات..." 
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <!--[if BLOCK]><![endif]--><?php if(session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('message')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Leave Types Table -->
    <div class="card">
        <div class="card-body">
            <!--[if BLOCK]><![endif]--><?php if($leaveTypes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>الكود</th>
                                <th>مدفوعة</th>
                                <th>تتطلب موافقة</th>
                                <th>الحد الأقصى للطلب</th>
                                <th>معدل التراكم/شهر</th>
                                <th>حد التحويل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leaveType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($leaveType->name); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo e($leaveType->code); ?></span>
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($leaveType->is_paid): ?>
                                            <span class="badge bg-success">مدفوعة</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">غير مدفوعة</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($leaveType->requires_approval): ?>
                                            <span class="badge bg-info">نعم</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">لا</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td><?php echo e($leaveType->max_per_request_days); ?> يوم</td>
                                    <td><?php echo e($leaveType->accrual_rate_per_month); ?> يوم</td>
                                    <td><?php echo e($leaveType->carry_over_limit_days); ?> يوم</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    wire:click="edit(<?php echo e($leaveType->id); ?>)" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="delete(<?php echo e($leaveType->id); ?>)"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا النوع؟')" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد أنواع إجازات</h5>
                    <p class="text-muted">ابدأ بإضافة نوع إجازة جديد</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <!--[if BLOCK]><![endif]--><?php if($isEdit): ?>
                                تعديل نوع الإجازة
                            <?php else: ?>
                                إضافة نوع إجازة جديد
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">اسم نوع الإجازة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" wire:model="name" placeholder="مثال: إجازة سنوية">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">كود نوع الإجازة <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="code" wire:model="code" placeholder="مثال: AL">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="max_per_request_days" class="form-label">الحد الأقصى للطلب (أيام) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['max_per_request_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="max_per_request_days" wire:model="max_per_request_days" min="0">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['max_per_request_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="accrual_rate_per_month" class="form-label">معدل التراكم/شهر (أيام) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['accrual_rate_per_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="accrual_rate_per_month" wire:model="accrual_rate_per_month" min="0">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['accrual_rate_per_month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="carry_over_limit_days" class="form-label">حد التحويل (أيام) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php $__errorArgs = ['carry_over_limit_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="carry_over_limit_days" wire:model="carry_over_limit_days" min="0">
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['carry_over_limit_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_paid" wire:model="is_paid">
                                        <label class="form-check-label" for="is_paid">
                                            إجازة مدفوعة
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="requires_approval" wire:model="requires_approval">
                                        <label class="form-check-label" for="requires_approval">
                                            تتطلب موافقة
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">إلغاء</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <!--[if BLOCK]><![endif]--><?php if($isEdit): ?>
                                        تحديث
                                    <?php else: ?>
                                        حفظ
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin"></i> جاري الحفظ...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/hr-management/leaves/leave-types/manage-leave-types.blade.php ENDPATH**/ ?>