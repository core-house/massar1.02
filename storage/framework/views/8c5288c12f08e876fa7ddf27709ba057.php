<?php

use Livewire\Volt\Component;
use App\Models\Shift;
use Livewire\WithPagination;

?>

<div class="" style="direction: rtl; font-family: 'Cairo', sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('إضافة الورديات')): ?>
            <button class="btn btn-primary" wire:click="create">
                <i class="las la-plus"></i> <?php echo e(__('Add Shift')); ?>

            </button>
        <?php endif; ?>
        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                    x-on:click="show = false"></button>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <div class="mb-3 col-md-4">
            <input type="text" class="form-control" style="font-family: 'Cairo', sans-serif;"
                placeholder="<?php echo e(__('Search by notes...')); ?>" wire:model.live="search">
        </div>
    </div>


    <div class="card ">

        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">

                <?php if (isset($component)) { $__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-export-actions','data' => ['tableId' => 'shifts-table','filename' => 'shifts-table','excelLabel' => 'تصدير Excel','pdfLabel' => 'تصدير PDF','printLabel' => 'طباعة']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-export-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['table-id' => 'shifts-table','filename' => 'shifts-table','excel-label' => 'تصدير Excel','pdf-label' => 'تصدير PDF','print-label' => 'طباعة']); ?>
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

                <table id="shifts-table" class="table text-center table-striped mb-0 overflow-x-auto">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Name')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Start Time')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Beginning Check In')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Ending Check In')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Allowed Late Minutes')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('End Time')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Beginning Check Out')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Ending Check Out')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Allowed Early Leave Minutes')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Shift Type')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Days')); ?></th>
                            <th class="font-family-cairo fw-bold"><?php echo e(__('Notes')); ?></th>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['حذف الورديات', 'تعديل الورديات'])): ?>
                                <th class="font-family-cairo fw-bold"><?php echo e(__('Actions')); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->name); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->start_time); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->beginning_check_in ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->ending_check_in ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->allowed_late_minutes ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->end_time); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->beginning_check_out ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->ending_check_out ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->allowed_early_leave_minutes ?? '-'); ?></td>
                                <td class="font-family-cairo fw-bold">
                                    <?php echo e($shiftTypes[$shift->shift_type] ?? $shift->shift_type); ?></td>
                                <td class="font-family-cairo fw-bold">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = json_decode($shift->days, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-info"><?php echo e($weekDays[$day] ?? $day); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                                <td class="font-family-cairo fw-bold"><?php echo e($shift->notes); ?></td>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['حذف الورديات', 'تعديل الورديات'])): ?>
                                    <td class="font-family-cairo fw-bold">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('تعديل الورديات')): ?>
                                            <button class="btn btn-md btn-success me-1" wire:click="edit(<?php echo e($shift->id); ?>)">
                                                <i class="las la-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('حذف الورديات')): ?>
                                            <button class="btn btn-md btn-danger" wire:click="delete(<?php echo e($shift->id); ?>)"
                                                onclick="return confirm('<?php echo e(__('Are you sure you want to delete this shift?')); ?>')">
                                                <i class="las la-trash"></i>
                                            </button>
                                        <?php endif; ?>

                                    </td>
                                <?php endif; ?>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="alert alert-info py-3 mb-0"
                                        style="font-size: 1.2rem; font-weight: 500;">
                                        <i class="las la-info-circle me-2"></i>
                                        لا توجد بيانات
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->

    <div class="modal fade <?php if($showModal): ?> show d-block <?php endif; ?>" tabindex="-1"
        style="background: rgba(0,0,0,0.5);" <?php if($showModal): ?> aria-modal="true" role="dialog" <?php endif; ?>>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e($isEdit ? __('Edit Shift') : __('Add Shift')); ?></h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Name')); ?></label>
                            <input type="text" class="form-control" wire:model.defer="name" required>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Start Time')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="start_time" required>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Beginning Check In')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="beginning_check_in">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['beginning_check_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Ending Check In')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="ending_check_in">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ending_check_in'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Allowed Late Minutes')); ?></label>
                            <input type="number" class="form-control" wire:model.defer="allowed_late_minutes" min="0" placeholder="<?php echo e(__('Minutes allowed after check-in start time')); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['allowed_late_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <small class="form-text text-muted"><?php echo e(__('Time allowed in minutes after check-in start time before counting as late')); ?></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('End Time')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="end_time" required>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Beginning Check Out')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="beginning_check_out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['beginning_check_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Ending Check Out')); ?></label>
                            <input type="time" class="form-control" wire:model.defer="ending_check_out">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ending_check_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Allowed Early Leave Minutes')); ?></label>
                            <input type="number" class="form-control" wire:model.defer="allowed_early_leave_minutes" min="0" placeholder="<?php echo e(__('Minutes allowed before check-out end time')); ?>">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['allowed_early_leave_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            <small class="form-text text-muted"><?php echo e(__('Time allowed in minutes before check-out end time before counting as early leave')); ?></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Shift Type')); ?></label>
                            <select class="form-select" wire:model.defer="shift_type" required>
                                <option value=""><?php echo e(__('Select shift type')); ?></option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $shiftTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['shift_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Days')); ?></label>
                            <div class="d-flex flex-wrap gap-2">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" id="day_<?php echo e($key); ?>"
                                            value="<?php echo e($key); ?>" wire:model.defer="days">

                                        <label class="form-check-label"
                                            for="day_<?php echo e($key); ?>"><?php echo e($label); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo e(__('Notes')); ?></label>
                            <textarea class="form-control" wire:model.defer="notes"></textarea>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showModal', false)"><?php echo e(__('Cancel')); ?></button>

                        <button type="submit"
                            class="btn btn-primary"><?php echo e($isEdit ? __('Update') : __('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/hr-management/shifts/manage-shifts.blade.php ENDPATH**/ ?>