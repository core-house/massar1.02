<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.breadcrumb', [
        'title' => __('Journals'),
        'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('Journals')]],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <div class="card">

        <div class="card-body">
            <div class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" id="filterDateFrom" class="form-control">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" id="filterDateTo" class="form-control">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label">رقم القيد</label>
                        <input type="text" id="filterJournalId" class="form-control" placeholder="مثال: 1024">
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">اسم الحساب</label>
                        <input type="text" id="filterAccount" class="form-control" placeholder="ابحث باسم الحساب">
                    </div>
                    <div class="col-sm-2">
                        <label class="form-label">نوع العملية</label>
                        <input type="text" id="filterType" class="form-control" placeholder="مثال: سند قبض">
                    </div>
                    <div class="col-sm-1">
                        <label class="form-label">الحركة</label>
                        <select id="filterDC" class="form-select">
                            <option value="">الكل</option>
                            <option value="debit">مدين>0</option>
                            <option value="credit">دائن>0</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-bordered table-hover table-sm  mb-0" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">

                        <tr class="journal_tr text-center">
                            <th>م</th>
                            <th>رقم القيد</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>اسم الحساب</th>
                            <th>بيان</th>
                            <th>نوع العملية</th>
                            <th>التاريخ</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $journalHeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $head): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $__currentLoopData = $head->dets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="journal-row" data-journal-id="<?php echo e($head->journal_id); ?>" data-type="<?php echo e($head->oper->type->ptext); ?>" data-date="<?php echo e($head->date); ?>" data-accname="<?php echo e($detail->accHead->aname ?? ''); ?>" data-debit="<?php echo e((float) $detail->debit); ?>" data-credit="<?php echo e((float) $detail->credit); ?>">
                                    <?php if($j == 0): ?>
                                        <td  class="font-family-cairo fw-bold font-14 text-center" rowspan="<?php echo e($head->dets->count()); ?>"><?php echo e($i + 1); ?></td>
                                        <td  class="font-family-cairo fw-bold font-14 text-center" rowspan="<?php echo e($head->dets->count()); ?>"><?php echo e($head->journal_id); ?></td>
                                    <?php endif; ?>

                                    <td  class="font-family-cairo fw-bold font-14 text-center"><?php echo e($detail->debit); ?></td>
                                    <td  class="font-family-cairo fw-bold font-14 text-center"><?php echo e($detail->credit); ?></td>
                                    <td  class="font-family-cairo fw-bold font-14 text-center"><?php echo e($detail->accHead->aname ?? '-'); ?></td>
                                    <?php if($j == 0): ?>
                                        <td  class="font-family-cairo fw-bold font-14 text-center" rowspan="<?php echo e($head->dets->count()); ?>"><?php echo e($head->details); ?></td>
                                        <td  class="font-family-cairo fw-bold font-14 text-center" rowspan="<?php echo e($head->dets->count()); ?>"><?php echo e($head->oper->type->ptext); ?></td>
                                        <td  class="font-family-cairo fw-bold font-14 text-center" rowspan="<?php echo e($head->dets->count()); ?>"><?php echo e($head->date); ?></td>
                                    <?php endif; ?>
                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="alert alert-info py-3 mb-0" style="font-size: 1.2rem; font-weight: 500;">
                                        <i class="las la-info-circle me-2"></i>
                                        لا توجد بيانات
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>
            </div>
            <div class="mt-2 small text-muted" id="rowsCount"></div>
        </div>
    </div>
    <script>
        (function(){
            const rows = () => Array.from(document.querySelectorAll('.journal-row'));
            const els = {
                from: document.getElementById('filterDateFrom'),
                to: document.getElementById('filterDateTo'),
                jid: document.getElementById('filterJournalId'),
                acc: document.getElementById('filterAccount'),
                type: document.getElementById('filterType'),
                dc: document.getElementById('filterDC'),
                cnt: document.getElementById('rowsCount')
            };
            function inRange(dateStr, fromStr, toStr){
                if (!dateStr) return false;
                const d = new Date(dateStr);
                if (fromStr){
                    const f = new Date(fromStr);
                    if (d < f) return false;
                }
                if (toStr){
                    const t = new Date(toStr);
                    if (d > t) return false;
                }
                return true;
            }
            function applyFilters(){
                const fFrom = els.from.value;
                const fTo = els.to.value;
                const fJid = els.jid.value.trim().toLowerCase();
                const fAcc = els.acc.value.trim().toLowerCase();
                const fType = els.type.value.trim().toLowerCase();
                const fDC = els.dc.value;
                let visible = 0;
                rows().forEach(tr => {
                    const jid = (tr.getAttribute('data-journal-id') || '').toLowerCase();
                    const acc = (tr.getAttribute('data-accname') || '').toLowerCase();
                    const typ = (tr.getAttribute('data-type') || '').toLowerCase();
                    const dat = tr.getAttribute('data-date') || '';
                    const debit = parseFloat(tr.getAttribute('data-debit') || '0');
                    const credit = parseFloat(tr.getAttribute('data-credit') || '0');

                    let ok = true;
                    if (fFrom || fTo) ok = ok && inRange(dat, fFrom, fTo);
                    if (fJid) ok = ok && jid.includes(fJid);
                    if (fAcc) ok = ok && acc.includes(fAcc);
                    if (fType) ok = ok && typ.includes(fType);
                    if (fDC === 'debit') ok = ok && debit > 0;
                    if (fDC === 'credit') ok = ok && credit > 0;

                    tr.style.display = ok ? '' : 'none';
                    if (ok) visible++;
                });
                if (els.cnt){ els.cnt.textContent = `عدد الأسطر الظاهرة: ${visible}`; }
            }
            ['change','keyup'].forEach(ev => {
                ['from','to','jid','acc','type','dc'].forEach(k => {
                    els[k].addEventListener(ev, applyFilters);
                });
            });
            applyFilters();
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/journals/journal-summery.blade.php ENDPATH**/ ?>