<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">أحدث الحسابات</div>
            <div class="card-body p-0">
                <?php
                use App\Models\AccHead;

                $lastAccounts = AccHead::with('haveParent')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();


                ?>

                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>التليفون</th>
                            <th>code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $lastAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($acc->aname); ?> -> <?php echo e($acc->haveParent?->aname); ?></td>
                            <td><?php echo e($acc->phone); ?></td>
                            <td><?php echo e($acc->code); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">logins</div>
            <div class="card-body p-0">
                <?php
                $lastLogins = \App\Models\LoginSession::with('user')
                ->orderBy('login_at', 'desc')
                ->take(5)
                ->get();
                ?>

                <table class="table table-responsive table-striped mb-0">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>IP</th>
                            <th>وقت الدخول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $lastLogins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $login): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($login->user->name ?? '—'); ?></td>
                            <td><?php echo e($login->ip_address); ?></td>
                            <td><?php echo e($login->login_at); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/dashboard/components/summary-tables.blade.php ENDPATH**/ ?>