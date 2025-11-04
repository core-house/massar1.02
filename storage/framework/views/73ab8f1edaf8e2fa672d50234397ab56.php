<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['branches', 'selected' => null, 'model' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['branches', 'selected' => null, 'model' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($branches->count() > 1): ?>
    <div class="mb-3 col-lg-2">
        <label for="branch_id"><?php echo e(__('الفرع')); ?></label>
        <select class="form-control" id="branch_id" name="branch_id"
            <?php if($model): ?> wire:model="<?php echo e($model); ?>" <?php endif; ?>>
            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($branch->id); ?>" <?php echo e(old('branch_id', $selected) == $branch->id ? 'selected' : ''); ?>>
                    <?php echo e($branch->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="text-danger"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
<?php elseif($branches->count() === 1): ?>
    <input type="hidden" name="branch_id" value="<?php echo e($branches->first()->id); ?>"
        <?php if($model): ?> wire:model="<?php echo e($model); ?>" <?php endif; ?>>
    
<?php endif; ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\Modules/Branches\Resources/views/components/branch-select.blade.php ENDPATH**/ ?>