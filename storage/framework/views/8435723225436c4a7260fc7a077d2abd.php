<?php $__env->startSection('sidebar'); ?>
    <?php echo $__env->make('components.sidebar.vouchers', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">إحصائيات القيود اليومية 📊</h2>
            </div>
        </div>

        <!-- الكروت -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card shadow-sm h-100 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-family-cairo fw-bold mb-2">
                                    إجمالي القيود اليومية
                                </h6>
                                <h2 class="font-family-cairo fw-bold mb-0 text-primary">
                                    <?php echo e(number_format($overallTotal->overall_value, 2)); ?>

                                </h2>
                                <small class="text-muted font-family-cairo">
                                    <?php echo e(number_format($overallTotal->overall_count)); ?> قيد
                                </small>
                            </div>
                            <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                                <i class="las la-chart-pie"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php $__currentLoopData = $sortedStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeId => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($stats): ?>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card shadow-sm h-100 border-start border-<?php echo e($stats['color']); ?> border-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted font-family-cairo fw-bold mb-2">
                                            <?php echo e($stats['title']); ?>

                                        </h6>
                                        <h2 class="font-family-cairo fw-bold mb-0 text-<?php echo e($stats['color']); ?>">
                                            <?php echo e(number_format($stats['value'], 2)); ?>

                                        </h2>
                                        <small class="text-muted font-family-cairo">
                                            <?php echo e(number_format($stats['count'])); ?> قيد
                                        </small>
                                    </div>
                                    <div class="text-<?php echo e($stats['color']); ?>" style="font-size: 3rem; opacity: 0.3;">
                                        <i class="las <?php echo e($stats['icon']); ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- الشارتس -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <h3 class="mb-3">توزيع القيود حسب النوع</h3>
                <canvas id="typePieChart" height="150"></canvas>
            </div>
            <div class="col-lg-6 mb-4">
                <h3 class="mb-3">توزيع القيم حسب الحسابات</h3>
                <canvas id="accountBarChart" height="150"></canvas>
            </div>
        </div>

        <!-- إحصائيات حسب نوع القيد -->
        <h3 class="mt-5">إحصائيات حسب نوع القيد</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>نوع القيد</th>
                        <th>عدد القيود</th>
                        <th>إجمالي القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $sortedStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeId => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($stats): ?>
                            <tr>
                                <td><?php echo e($typeId); ?></td>
                                <td><?php echo e($stats['title']); ?></td>
                                <td><?php echo e(number_format($stats['count'])); ?></td>
                                <td><?php echo e(number_format($stats['value'], 2)); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                    <tr>
                        <td colspan="2" class="text-right">الإجمالي الكلي:</td>
                        <td><?php echo e(number_format($overallTotal->overall_count)); ?></td>
                        <td><?php echo e(number_format($overallTotal->overall_value, 2)); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- إحصائيات حسب الحسابات -->
        <h3 class="mt-5">إحصائيات حسب الحسابات</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم الحساب</th>
                        <th>إجمالي المدين</th>
                        <th>إجمالي الدائن</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $accountStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($stat->id); ?></td>
                            <td><?php echo e($stat->account_name); ?></td>
                            <td><?php echo e(number_format($stat->debit_total, 2)); ?></td>
                            <td><?php echo e(number_format($stat->credit_total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- إحصائيات حسب الموظفين -->
        <h3 class="mt-5">إحصائيات حسب الموظفين</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم الموظف</th>
                        <th>عدد القيود</th>
                        <th>إجمالي القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $employeeStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($stat->id); ?></td>
                            <td><?php echo e($stat->employee_name); ?></td>
                            <td><?php echo e(number_format($stat->count)); ?></td>
                            <td><?php echo e(number_format($stat->value, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- إحصائيات حسب مراكز التكلفة -->
        <h3 class="mt-5">إحصائيات حسب مراكز التكلفة</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>اسم مركز التكلفة</th>
                        <th>إجمالي القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $costCenterStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($stat->id); ?></td>
                            <td><?php echo e($stat->cost_center_name); ?></td>
                            <td><?php echo e(number_format($stat->value, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- تضمين Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // مخطط دائري: توزيع القيود حسب النوع
        const typePieChart = new Chart(document.getElementById('typePieChart'), {
            type: 'pie',
            data: {
                labels: [
                    <?php $__currentLoopData = $sortedStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($stats): ?>
                            '<?php echo e($stats['title']); ?>',
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [{
                    data: [
                        <?php $__currentLoopData = $sortedStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($stats): ?>
                                <?php echo e($stats['value']); ?>,
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                    backgroundColor: [
                        <?php $__currentLoopData = $sortedStatistics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($stats): ?>
                                'rgba(<?php echo e($stats['color'] == 'success' ? '40, 167, 69' : ($stats['color'] == 'danger' ? '220, 53, 69' : '0, 123, 255')); ?>, 0.5)',
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'توزيع القيود حسب النوع'
                    }
                }
            }
        });

        // مخطط أعمدة: توزيع القيم حسب الحسابات
        // مخطط أعمدة: توزيع القيم حسب الحسابات
        const accountBarChart = new Chart(document.getElementById('accountBarChart'), {
            type: 'bar',
            data: {
                labels: [
                    <?php $__currentLoopData = $accountStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        '<?php echo e($loop->index + 1); ?>: <?php echo e($stat->account_name); ?>',
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                datasets: [{
                        label: 'إجمالي المدين',
                        data: [
                            <?php $__currentLoopData = $accountStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($stat->debit_total); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ],
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'إجمالي الدائن',
                        data: [
                            <?php $__currentLoopData = $accountStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($stat->credit_total); ?>,
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ],
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'القيمة'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'الحساب'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'توزيع القيم حسب الحسابات'
                    }
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/journals/statistics.blade.php ENDPATH**/ ?>