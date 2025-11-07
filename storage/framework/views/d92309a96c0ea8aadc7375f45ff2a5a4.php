<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('statistics.index')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.statistics')); ?>

    </a>
</li>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض العملااء')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('clients.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.clients')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض مصدر الفرص')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('chance-sources.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.chance_sources')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض جهات اتصال الشركات')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('client-contacts.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.client_contacts')); ?>

        </a>
    </li>
<?php endif; ?>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('client.categories.index')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('Clients Categories')); ?>

    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('client-types.index')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('Clients Types')); ?>

    </a>
</li>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض حالات الفرص')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('lead-status.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.lead_statuses')); ?>

        </a>
    </li>
<?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الفرص')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('leads.board')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.leads')); ?>


        </a>
    </li>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('عرض الفرص')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('tasks.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.tasks')); ?>


        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('tasks.types.index')); ?>">
            <i class="ti-control-record"></i><?php echo e(__('navigation.task_types')); ?>


        </a>
    </li>
<?php endif; ?>
<li class="nav-item">
    <a class="nav-link" href="<?php echo e(route('activities.index')); ?>">
        <i class="ti-control-record"></i><?php echo e(__('navigation.activities')); ?>

    </a>
</li>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/components/sidebar/crm.blade.php ENDPATH**/ ?>