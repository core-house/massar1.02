<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.journals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <style>
        .form-group {
            margin-bottom: 1rem;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.4rem;
            display: inline-block;
        }

        .form-control {
            padding: 0.5rem 0.75rem;
            font-size: 0.95rem;
            border-radius: 0.4rem;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .card-footer {
            padding: 1.5rem 1rem;
            text-align: center;
        }

        .table thead th {

            vertical-align: middle;
            text-align: center;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table input,
        .table select {
            min-width: 100px;
        }

        .summary-box {
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.4rem;
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>

    <div>
        <div class="card mt-3">
            <div class="card-header">
                <h1 class="card-title">قيد يومية متعدد</h1>
            </div>
            <div class="card-body">

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="myForm" action="<?php echo e(route('multi-journals.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="pro_type" value="8">

                    <div class="row">
                        <div class="col-md-4">
                            <label>التاريخ</label>
                            <input type="date" name="pro_date" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label>الموظف</label>
                            <select name="emp_id" class="form-control" required>
                                <option value="">اختر موظف</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->code); ?> - <?php echo e($emp->aname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>بيان</label>
                            <input type="text" name="details" class="form-control">

                        <div class="col-md-9">
                            <div class="form-control">
                                <label for="details">بيان</label>
                                <input name="details" type="text" required class="form-control frst">
                            </div>
                        </div>
                    </div>

                    

                    

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered" id="entriesTable">
                            <thead>
                                <tr>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الحساب</th>
                                    <th>ملاحظات</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" name="debit[]" class="form-control debit" step="0.01"
                                            value="0"></td>
                                    <td><input type="number" name="credit[]" class="form-control credit" step="0.01"
                                            value="0"></td>
                                    <td>
                                        <select name="account_id[]" class="form-control" required>
                                            <option value="">اختر حساب</option>
                                            <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($acc->id); ?>"><?php echo e($acc->code); ?> -
                                                    <?php echo e($acc->aname); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="note[]" class="form-control"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm removeRow">حذف</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-secondary mt-2" id="addRow">+ إضافة سطر</button>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <label>ملاحظات عامة</label>
                            <input type="text" name="info" class="form-control">
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="summary-box">
                                اجمالي مدين: <span id="debitTotal">00.00</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box">
                                اجمالي دائن: <span id="creditTotal">00.00</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-box">
                                الفرق: <span id="diffTotal">00.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mt-4">
                        <button type="submit" class="btn btn-primary">حفظ</button>
                        <button type="reset" class="btn btn-danger">إلغاء</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/multi-journals/create.blade.php ENDPATH**/ ?>