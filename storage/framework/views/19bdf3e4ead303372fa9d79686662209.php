<?php

use Livewire\Volt\Component;
use App\Models\Item;
use App\Models\OperationItems;
use App\Models\OperHead;
use App\Enums\OperationTypeEnum;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

?>

<div class="card" style="direction: rtl; font-family: 'Cairo', sans-serif;">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span><?php echo e(__('الأصناف الأكثر مبيعاً')); ?></span>
        <div class="d-flex gap-2 align-items-center">
            <small class="text-muted"><?php echo e($periodLabel); ?></small>
            <select wire:model.live="selectedPeriod" class="form-select form-select-sm" style="width: auto; font-size: 0.8rem;">
                <option value="week"><?php echo e(__('أسبوع')); ?></option>
                <option value="month"><?php echo e(__('شهر')); ?></option>
                <option value="quarter"><?php echo e(__('ربع سنة')); ?></option>
                <option value="year"><?php echo e(__('سنة')); ?></option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <!--[if BLOCK]><![endif]--><?php if($topSellingItems->count() > 0): ?>
            <canvas id="topSellingItemsChart" height="300"></canvas>
        <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                <p><?php echo e(__('لا توجد بيانات مبيعات في الفترة المحددة')); ?></p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:init', () => {
    let chart = null;
    
    function initChart() {
        const ctx = document.getElementById('topSellingItemsChart');
        if (!ctx) return;
        
        // الحصول على البيانات من Livewire
        const topSellingItems = <?php echo json_encode($topSellingItems, 15, 512) ?>;
        
        if (topSellingItems.length === 0) return;
        
        // حساب النسب المئوية
        const totalQuantity = topSellingItems.reduce((sum, item) => sum + item.quantity, 0);
        topSellingItems.forEach(item => {
            item.percentage = ((item.quantity / totalQuantity) * 100).toFixed(1);
        });
        
        const labels = topSellingItems.map(item => item.name);
        const quantities = topSellingItems.map(item => item.quantity);
        // revenues will be null, so we can skip it or show N/A in tooltip
        // const revenues = topSellingItems.map(item => item.total_revenue);

        // تدمير الرسم البياني السابق إذا كان موجوداً
        if (chart) {
            chart.destroy();
        }
        
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: "<?php echo e(__('الكمية المباعة')); ?>",
                    data: quantities,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        rtl: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const item = topSellingItems[context.dataIndex];
                                let revenueText = "<?php echo e(__('غير متوفر')); ?>";
                                if (item.total_revenue !== null && item.total_revenue !== undefined) {
                                    revenueText = `${item.total_revenue.toLocaleString()} <?php echo e(__('ريال')); ?>`;
                                }
                                return [
                                    `<?php echo e(__('الكمية')); ?>: ${context.parsed.y}`,
                                    `<?php echo e(__('الإيرادات')); ?>: ${revenueText}`,
                                    `<?php echo e(__('النسبة')); ?>: ${item.percentage}%`,
                                    `<?php echo e(__('عدد الطلبات')); ?>: ${item.order_count}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                family: "'Cairo', sans-serif",
                                size: 12
                            },
                            color: '#666',
                            padding: 8,
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        },
                        title: {
                            display: true,
                            text: "<?php echo e(__('الكمية المباعة')); ?>",
                            font: {
                                family: "'Cairo', sans-serif",
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#333'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Cairo', sans-serif",
                                size: 11
                            },
                            color: '#666',
                            maxRotation: 45,
                            minRotation: 0,
                            callback: function(value, index) {
                                const label = this.getLabelForValue(value);
                                return label.length > 15 ? label.substring(0, 15) + '...' : label;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    // تهيئة الرسم البياني عند تحميل الصفحة
    initChart();
    
    // تحديث الرسم البياني عند تغيير البيانات
    Livewire.on('top-selling-items-updated', () => {
        setTimeout(initChart, 100);
    });
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views\livewire/dashboard/top-selling-items-chart.blade.php ENDPATH**/ ?>