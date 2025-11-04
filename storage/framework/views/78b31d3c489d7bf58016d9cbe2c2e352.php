<head>
    <meta charset="utf-8" />
    <title>MASAR مـسار</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="user-id" content="<?php echo e(auth()->id()); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.ico')); ?>">
    <script src="<?php echo e(asset('assets/js/lucide.js')); ?>"></script>

    <!-- Google Fonts - IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/fontawesome.css')); ?>">
    
    <link href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/bootstrap2025.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/icons.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/metisMenu.min.css')); ?>" rel="stylesheet" type="text/css" />
    <script src="<?php echo e(asset('assets/js/jq.js')); ?>"></script>
    <link href="<?php echo e(asset('assets/plugins/daterangepicker/daterangepicker.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/app-rtl.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/cake.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-overrides.css')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/bootstrap-custom-colors.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/custom-styles.css')); ?>" rel="stylesheet" type="text/css" />

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    
    <!-- User ID for Location Tracking -->
    <?php if(auth()->guard()->check()): ?>
        <meta name="user-id" content="<?php echo e(auth()->id()); ?>">
    <?php endif; ?>
    
    <!-- Livewire Styles -->
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>


    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<?php echo $__env->make('components.idintity.loader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH D:\Laragon\laragon\www\massar1.02\resources\views/admin/partials/head.blade.php ENDPATH**/ ?>