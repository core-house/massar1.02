<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\AccHead;
use App\Models\OperHead;
use App\Models\JournalDetail;

?>

<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title font-family-cairo fw-bold">تقرير حركه حساب</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="font-family-cairo fw-bold">فلاتر البحث</h4>
            <!--[if BLOCK]><![endif]--><?php if($accountId): ?>
                <div class="d-flex align-items-center">
                    <span class="font-family-cairo fw-bold me-2">الرصيد الحالي للحساب <?php echo e($accountName); ?>:</span>
                    <span
                        class="font-family-cairo fw-bold font-16 <?php if($this->runningBalance < 0): ?> bg-soft-danger <?php else: ?> bg-soft-primary <?php endif; ?>"><?php echo e(number_format($this->runningBalance , 2)); ?></span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="account" class="form-label font-family-cairo fw-bold">الحساب</label>
                        <div class="dropdown" wire:click.outside="hideDropdown">
                            <input type="text" class="form-control font-family-cairo fw-bold"
                                placeholder="ابحث عن حساب..." wire:model.live.debounce.300ms="searchTerm"
                                wire:keydown.arrow-down.prevent="arrowDown" wire:keydown.arrow-up.prevent="arrowUp"
                                wire:keydown.enter.prevent="selectHighlightedItem" wire:focus="showResults"
                                onclick="this.select()">
                            <!--[if BLOCK]><![endif]--><?php if($showDropdown && $this->searchResults->isNotEmpty()): ?>
                                <ul class="dropdown-menu show" style="width: 100%;">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->searchResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <a class="font-family-cairo fw-bold dropdown-item <?php echo e($highlightedIndex === $index ? 'active' : ''); ?>"
                                                href="#"
                                                wire:click.prevent="selectAccount(<?php echo e($account->id); ?>, '<?php echo e($account->aname); ?>')">
                                                <?php echo e($account->aname); ?>

                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </ul>
                            <?php elseif($showDropdown && strlen($searchTerm) >= 2 && $searchTerm !== $accountName): ?>
                                <ul class="dropdown-menu show" style="width: 100%;">
                                    <li><span class="dropdown-item-text font-family-cairo fw-bold text-danger">لا يوجد
                                            نتائج لهذا البحث</span></li>
                                </ul>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="fromDate" class="form-label font-family-cairo fw-bold">من تاريخ</label>
                        <input type="date" wire:model.live="fromDate" id="fromDate"
                            class="form-control font-family-cairo fw-bold">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label for="toDate" class="form-label font-family-cairo fw-bold">إلى تاريخ</label>
                        <input type="date" wire:model.live="toDate" id="toDate"
                            class="form-control font-family-cairo fw-bold">
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!--[if BLOCK]><![endif]--><?php if($accountId): ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th class="font-family-cairo fw-bold">التاريخ</th>
                                <th class="font-family-cairo fw-bold">مصدر العملية</th>
                                <th class="font-family-cairo fw-bold">نوع الحركة</th>
                                <th class="font-family-cairo fw-bold">الرصيد قبل الحركة</th>
                                <th class="font-family-cairo fw-bold">المبلغ</th>
                                <th class="font-family-cairo fw-bold">الرصيد بعد الحركة</th>
                                <th class="font-family-cairo fw-bold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $balanceBefore = JournalDetail::where('account_id', $this->accountId)->where('crtime', '<', $this->fromDate)->sum('debit') - JournalDetail::where('account_id', $this->accountId)->where('crtime', '<', $this->fromDate)->sum('credit');

                                $balanceAfter = 0;
                            ?>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="font-family-cairo fw-bold"><?php echo e($movement->crtime); ?>

                                    </td>
                                    <td class="font-family-cairo fw-bold">
                                        <?php echo e($movement->op_id); ?>#_<?php echo e($this->getArabicReferenceName(OperHead::find($movement->op_id)->pro_type )); ?>

                                    </td>
                                    <td class="font-family-cairo fw-bold">
                                        <?php echo e($movement->debit > 0 ? 'مدين' : 'دائن'); ?>

                                    </td>
                                    <td class="font-family-cairo fw-bold">
                                        <?php echo e(number_format($balanceBefore, 2)); ?>

                                    </td>
                                    <td class="font-family-cairo fw-bold">
                                        <?php echo e($movement->debit > 0 ? number_format($movement->debit, 2) : number_format($movement->credit, 2)); ?>

                                    </td>
                                    <?php
                                        $balanceAfter = $balanceBefore + ($movement->debit > 0 ? $movement->debit : $movement->credit);
                                    ?>
                                    <td class="font-family-cairo fw-bold">
                                        <?php echo e(number_format($balanceAfter, 2)); ?>

                                        
                                    </td>
                                    <td class="font-family-cairo fw-bold">
                                        <?php
                                            $operation = OperHead::find($movement->op_id);
                                        ?>
                                        <!-- sales and purchase and return sales and return purchase -->
                                        <!--[if BLOCK]><![endif]--><?php if($operation && ($operation->pro_type == 10 || $operation->pro_type == 11 || $operation->pro_type == 12 || $operation->pro_type == 13)): ?>
                                            <a href="<?php echo e(route('invoice.view', $movement->op_id)); ?>" class="btn btn-xs btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                                <?php
                                    $balanceBefore = $balanceAfter;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="12" class="text-center font-family-cairo fw-bold">لا يوجد حركات
                                        للمعايير المحددة.</td>
                                </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    <?php echo e($movements->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <?php $__env->startPush('scripts'); ?>
        <script>
            document.addEventListener('livewire:initialized', () => {
                const modalElement = document.getElementById('referenceModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);

                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').on('show-reference-modal', () => {
                        modal.show();
                    });

                    modalElement.addEventListener('hidden.bs.modal', () => {
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('closeModal');
                    })
                }
            });
        </script>
    <?php $__env->stopPush(); ?>
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/accounts/reports/account-movement.blade.php ENDPATH**/ ?>