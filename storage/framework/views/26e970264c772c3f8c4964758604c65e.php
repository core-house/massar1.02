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

   
    .card {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .row + .row {
        margin-top: 1rem;
    }

    .table thead th {
       
        vertical-align: middle;
        text-align: center;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .table input, .table select {
        min-width: 100px;
    }
</style>

<div class="">
    <div class="card mt-3">
        <div class="card-header">
            <h1 class="card-title">قيد يومية</h1>
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

            <form id="myForm" action="<?php echo e(route('journals.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="pro_type" value="7">

                
                <div class="row">
                    <div class="col-md-3">
                        <label>التاريخ</label>
                        <input type="date" name="pro_date" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>">
                    </div>

                    <div class="col-md-3">
                        <label>الرقم الدفتري</label>
                        <input type="text" name="pro_num" class="form-control" placeholder="EX:7645">
                    </div>

                    <div class="col-md-3">
                        <label>الموظف</label>
                        <select name="emp_id" class="form-control" required>
                            <option value="">اختر موظف</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->code); ?> - <?php echo e($emp->aname); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>مركز التكلفة</label>
                        <select name="cost_center" class="form-control" required>
                            <option value="">اختر مركز تكلفة</option>
                            <?php $__currentLoopData = $cost_centers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cost->id); ?>"><?php echo e($cost->cname); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <label>بيان</label>
                        <input type="text" name="details" class="form-control">
                    </div>
                </div>

                
                <div class="table-responsive mt-4">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th width="15%">مدين</th>
                                <th width="15%">دائن</th>
                                <th width="30%">الحساب</th>
                                <th width="40%">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="number" name="debit" class="form-control debit" id="debit" value="0.00" step="0.01" required>
                                </td>
                                <td></td>
                                <td>
                                    <select name="acc1" class="form-control" required>
                                        <option value="">اختر حساب</option>
                                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($acc->id); ?>"><?php echo e($acc->code); ?> - <?php echo e($acc->aname); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td><input type="text" name="info2" class="form-control"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="number" name="credit" class="form-control credit" id="credit" value="0.00" step="0.01">
                                </td>
                                <td>
                                    <select name="acc2" class="form-control" required>
                                        <option value="">اختر حساب</option>
                                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($acc->id); ?>"><?php echo e($acc->code); ?> - <?php echo e($acc->aname); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </td>
                                <td><input type="text" name="info3" class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row my-4">
                    <div class="col">
                        <label>ملاحظات عامة</label>
                        <input type="text" name="info" class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary m-1">حفظ</button>
                    <button type="reset" class="btn btn-danger m-1">إلغاء</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById("myForm").addEventListener("submit", function(e) {
        const debit = +document.getElementById("debit").value;
        const credit = +document.getElementById("credit").value;

        if (debit !== credit) {
            e.preventDefault();
            alert("يجب أن تكون القيمة المدينة مساوية للقيمة الدائنة.");
        }
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/journals/create.blade.php ENDPATH**/ ?>