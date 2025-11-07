<?php

use Livewire\Volt\Component;
use App\Models\AccHead;
use Illuminate\Support\Facades\DB;
use App\Services\AccountService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

?>

<div style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="row">
        <!--[if BLOCK]><![endif]--><?php if(session()->has('success')): ?>
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <?php echo e(session('success')); ?>

            </div>
        <?php elseif(session()->has('error')): ?>
            <div class="alert alert-danger" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <form wire:submit="updateStartBalance" wire:target="updateStartBalance" wire:loading.attr="disabled">
                <?php echo csrf_field(); ?>
                <style>
                    .custom-table-hover tbody tr:hover {
                        background-color: #f5e9d7 !important;
                        /* لون مختلف عند المرور */
                    }
                </style>

                <?php if (isset($component)) { $__componentOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6b7091aaeeb1e8e2000046e4bdf85bc7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-export-actions','data' => ['tableId' => 'updateStartBalance-table','filename' => 'updateStartBalance-table','excelLabel' => 'تصدير Excel','pdfLabel' => 'تصدير PDF','printLabel' => 'طباعة']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-export-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['table-id' => 'updateStartBalance-table','filename' => 'updateStartBalance-table','excel-label' => 'تصدير Excel','pdf-label' => 'تصدير PDF','print-label' => 'طباعة']); ?>
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

                <table id="updateStartBalance-table"
                    class="table table-bordered table-sm table-striped custom-table-hover">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 10%" class="font-family-cairo fw-bold font-14">الكود</th>
                            <th style="width: 20%" class="font-family-cairo fw-bold font-14">الاسم</th>
                            <th style="width: 15%" class="font-family-cairo fw-bold font-14">رصيد اول المده الحالي</th>
                            <th style="width: 15%" class="font-family-cairo fw-bold font-14">رصيد اول المده الجديد</th>
                        </tr>
                    </thead>
                    <tbody id="items_table_body">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $formAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formAccount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-item-id="<?php echo e($formAccount['id']); ?>">
                                <td>
                                    <p class="font-family-cairo fw-bold font-16 text-center"><?php echo e($formAccount['code']); ?>

                                    </p>
                                </td>
                                <td>
                                    <p class="font-family-cairo fw-bold font-16 text-center"><?php echo e($formAccount['name']); ?>

                                        - <a
                                            href="<?php echo e(route('account-movement', ['accountId' => $formAccount['id']])); ?>">
                                            <i class="las la-eye fa-lg" title="عرض حركات الحساب"></i>
                                        </a></p>
                                </td>
                                <td>
                                    <p
                                        class="font-family-cairo fw-bold font-16 text-center <?php if($formAccount['current_start_balance'] < 0): ?> text-danger <?php endif; ?>">
                                        <?php echo e(number_format($formAccount['current_start_balance'] ?? 0, 2)); ?></p>
                                </td>
                                <td>
                                    <!--[if BLOCK]><![endif]--><?php if(!Str::startsWith($formAccount['code'], '3101') && !Str::startsWith($formAccount['code'], '1104')): ?>
                                        <input type="number" step="0.01"
                                            wire:model.blur="formAccounts.<?php echo e($formAccount['id']); ?>.new_start_balance"
                                            class="form-control form-control-sm new-balance-input font-family-cairo fw-bold font-16 <?php if(($formAccounts[$formAccount['id']]['new_start_balance'] ?? 0) < 0): ?> text-danger <?php endif; ?>"
                                            placeholder="رصيد اول المده الجديد" style="padding:2px;height:30px;"
                                            x-on:keydown.enter.prevent>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary m-3" wire:click="$refresh"
                    wire:target="updateStartBalance" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="updateStartBalance">
                        تحديث
                    </span>
                    <span wire:loading wire:target="updateStartBalance">
                        جاري التحديث...
                    </span>
                </button>
            </form>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form').forEach(function(form) {
                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName.toLowerCase() !== 'textarea') {
                        e.preventDefault();
                        const inputs = Array.from(form.querySelectorAll(
                            'input:not([type=hidden]):not([readonly]), select, textarea'
                        ));
                        const idx = inputs.indexOf(e.target);
                        if (idx > -1 && idx < inputs.length - 1) {
                            // انتقل للحقل التالي
                            inputs[idx + 1].focus();
                        } else if (idx === inputs.length - 1) {
                            // إذا كان في آخر حقل، انتقل إلى زر التحديث أو الحفظ
                            const submitBtn = form.querySelector(
                                'button[type="submit"], input[type="submit"]');
                            if (submitBtn) {
                                submitBtn.focus();
                                // عند الضغط على Enter مرة أخرى على الزر، قم بالتحديث أو الحفظ
                                submitBtn.addEventListener('keydown', function handler(ev) {
                                    if (ev.key === 'Enter' || ev.keyCode === 13) {
                                        ev.preventDefault();
                                        submitBtn.click();
                                    }
                                    // إزالة الحدث بعد أول استخدام لمنع التكرار
                                    submitBtn.removeEventListener('keydown', handler);
                                });
                            }
                        }
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/accounts/startBalance/manage-start-balance.blade.php ENDPATH**/ ?>