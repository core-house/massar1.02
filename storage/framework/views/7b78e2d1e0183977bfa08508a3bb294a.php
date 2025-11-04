<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\AccHead;
use App\Models\JournalDetail;
use Carbon\Carbon;
use Modules\Settings\Models\PublicSetting;

?>

<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title font-family-cairo fw-bold">الميزانية العمومية</h4>
            </div>
        </div>
    </div>

    <!-- Balance Sheet Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-family-cairo fw-bold">اسم الشركة:</label>
                                <input type="text" wire:model="companyName" class="form-control"
                                    placeholder="أدخل اسم الشركة">
                            </div>
                        </div>
                        
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button wire:click="refreshBalanceSheet" class="btn btn-primary font-family-cairo">
                                <i class="fas fa-sync-alt"></i> تحديث الميزانية
                            </button>
                            <button wire:click="exportBalanceSheet" class="btn btn-success font-family-cairo ms-2">
                                <i class="fas fa-download"></i> تصدير الميزانية
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Sheet Content -->
    <div class="row">
        <div class="col-9 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title font-family-cairo fw-bold">الميزانية العمومية -
                        <?php echo e($companyName ?: 'اسم الشركة'); ?></h5>
                    <p class="text-muted font-family-cairo">تاريخ الميزانية:
                        <?php echo e(\Carbon\Carbon::parse($balanceSheetDate)->format('Y-m-d')); ?></p>
                    <p class="text-muted font-family-cairo">(جميع المبالغ بالعملة المحلية)</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th class="font-family-cairo fw-bold text-center text-white" style="width: 60%">البند</th>
                                    <th class="font-family-cairo fw-bold text-center text-white" style="width: 40%">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- الأصول (Assets) -->
                                <tr class="table-primary">
                                    <td colspan="2" class="font-family-cairo fw-bold fs-5">الأصول (Assets)</td>
                                </tr>
                                
                                <!-- الأصول المتداولة -->
                                <tr class="table-info">
                                    <td class="font-family-cairo fw-bold">الأصول المتداولة (Current Assets)</td>
                                    <td></td>
                                </tr>
                                
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $currentAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="font-family-cairo fw-bold" style="padding-right: 30px;"><?php echo e($asset['name']); ?>

                                        </td>
                                        <td class="text-end font-family-cairo fw-bold"><?php echo e(number_format($asset['balance'], 2)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي الأصول المتداولة</td>
                                    <td class="text-end font-family-cairo fw-bold">
                                        <?php echo e(number_format($currentAssetsTotal, 2)); ?></td>
                                </tr>
                                
                                <!-- الأصول غير المتداولة -->
                                <tr class="table-info">
                                    <td class="font-family-cairo fw-bold">الأصول غير المتداولة (Non-Current Assets)</td>
                                    <td></td>
                                </tr>
                                
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $nonCurrentAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="font-family-cairo" style="padding-right: 30px;"><?php echo e($asset['name']); ?>

                                        </td>
                                        <td class="text-end font-family-cairo"><?php echo e(number_format($asset['balance'], 2)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي الأصول غير المتداولة</td>
                                    <td class="text-end font-family-cairo fw-bold">
                                        <?php echo e(number_format($nonCurrentAssetsTotal, 2)); ?></td>
                                </tr>
                                
                                <tr class="table-success">
                                    <td class="font-family-cairo fw-bold fs-5">إجمالي الأصول</td>
                                    <td class="text-end font-family-cairo fw-bold fs-5">
                                        <?php echo e(number_format($totalAssets, 2)); ?></td>
                                </tr>
                                
                                <!-- الخصوم وحقوق الملكية -->
                                <tr class="table-primary">
                                    <td colspan="2" class="font-family-cairo fw-bold fs-5">الخصوم وحقوق الملكية
                                        (Liabilities & Equity)</td>
                                </tr>
                                
                                <!-- الخصوم المتداولة -->
                                <tr class="table-info">
                                    <td class="font-family-cairo fw-bold">الخصوم المتداولة (Current Liabilities)</td>
                                    <td></td>
                                </tr>
                                
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $currentLiabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="font-family-cairo fw-bold" style="padding-right: 30px;">
                                            <?php echo e($liability['name']); ?></td>
                                        <td class="text-end font-family-cairo fw-bold">
                                            <?php echo e(number_format($liability['balance'], 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي الخصوم المتداولة</td>
                                    <td class="text-end font-family-cairo fw-bold">
                                        <?php echo e(number_format($currentLiabilitiesTotal, 2)); ?></td>
                                </tr>
                                
                                <!-- الخصوم غير المتداولة -->
                                <tr class="table-info">
                                    <td class="font-family-cairo fw-bold">الخصوم غير المتداولة (Non-Current Liabilities)
                                    </td>
                                    <td></td>
                                </tr>
                                
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $nonCurrentLiabilities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $liability): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="font-family-cairo fw-bold" style="padding-right: 30px;">
                                            <?php echo e($liability['name']); ?></td>
                                        <td class="text-end font-family-cairo fw-bold">
                                            <?php echo e(number_format($liability['balance'], 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي الخصوم غير المتداولة</td>
                                    <td class="text-end font-family-cairo fw-bold">
                                        <?php echo e(number_format($nonCurrentLiabilitiesTotal, 2)); ?></td>
                                </tr>
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي الخصوم</td>
                                    <td class="text-end font-family-cairo fw-bold">
                                        <?php echo e(number_format($totalLiabilities, 2)); ?></td>
                                </tr>
                                
                                <!-- حقوق الملكية -->
                                <tr class="table-info">
                                    <td class="font-family-cairo fw-bold">حقوق الملكية (Equity)</td>
                                    <td></td>
                                </tr>
                                
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $equity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equityItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="font-family-cairo fw-bold" style="padding-right: 30px;">
                                            <?php echo e($equityItem['name']); ?></td>
                                        <td class="text-end font-family-cairo fw-bold">
                                            <?php echo e(number_format($equityItem['balance'], 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                
                                <tr class="table-warning">
                                    <td class="font-family-cairo fw-bold">إجمالي حقوق الملكية</td>
                                    <td class="text-end font-family-cairo fw-bold"><?php echo e(number_format($totalEquity, 2)); ?>

                                    </td>
                                </tr>
                                
                                <tr class="table-success">
                                    <td class="font-family-cairo fw-bold fs-5">إجمالي الخصوم وحقوق الملكية</td>
                                    <td class="text-end font-family-cairo fw-bold fs-5">
                                        <?php echo e(number_format($totalLiabilities + $totalEquity, 2)); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Balance Sheet Equation Check -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div
                                class="alert <?php echo e($totalAssets == $totalLiabilities + $totalEquity ? 'alert-success' : 'alert-danger'); ?>">
                                <h6 class="font-family-cairo fw-bold">
                                    معادلة الميزانية العمومية:
                                    إجمالي الأصول (<?php echo e(number_format($totalAssets, 2)); ?>)
                                    <?php echo e($totalAssets == $totalLiabilities + $totalEquity ? '=' : '≠'); ?>

                                    إجمالي الخصوم + حقوق الملكية
                                    (<?php echo e(number_format($totalLiabilities + $totalEquity, 2)); ?>)
                                </h6>
                                <!--[if BLOCK]><![endif]--><?php if($totalAssets != $totalLiabilities + $totalEquity): ?>
                                    <p class="font-family-cairo text-danger">
                                        تحذير: الميزانية غير متوازنة. الفرق:
                                        <?php echo e(number_format($totalAssets - ($totalLiabilities + $totalEquity), 2)); ?>

                                    </p>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .font-family-cairo {
        font-family: 'Cairo', sans-serif;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .text-end {
        text-align: end !important;
    }

    .table-responsive {
        direction: rtl;
    }

    .table {
        direction: rtl;
    }

    .table th,
    .table td {
        text-align: right;
    }

    .table th.text-center,
    .table td.text-center {
        text-align: center !important;
    }

    .table th.text-end,
    .table td.text-end {
        text-align: left !important;
    }
    </style>
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/accounts/reports/manage-balance-sheet.blade.php ENDPATH**/ ?>