<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>American Appliance Repair</title>
<?php if(app()->environment('local')): ?>
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
<?php else: ?>
    
    <?php
        $manifestPath = public_path('build/.vite/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        
        // Collect all CSS files
        $cssFiles = [];
        if (!empty($manifest['resources/css/app.css']['file'])) {
            $cssFiles[] = $manifest['resources/css/app.css']['file'];
        }
        if (!empty($manifest['resources/js/app.js']['css'])) {
            $cssFiles = array_merge($cssFiles, $manifest['resources/js/app.js']['css']);
        }

        // Get JS file
        $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
    ?>

    
    <?php $__currentLoopData = $cssFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cssFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link rel="stylesheet" href="<?php echo e(asset('build/' . $cssFile)); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if($jsFile): ?>
        <script type="module" src="<?php echo e(asset('build/' . $jsFile)); ?>"></script>
    <?php endif; ?>
<?php endif; ?>


    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <meta name="theme-color" content="#232850">

    


    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo e(asset('images/icons/apple-icon-57x57.png')); ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo e(asset('images/icons/apple-icon-60x60.png')); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo e(asset('images/icons/apple-icon-72x72.png')); ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo e(asset('images/icons/apple-icon-76x76.png')); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo e(asset('images/icons/apple-icon-114x114.png')); ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo e(asset('images/icons/apple-icon-120x120.png')); ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo e(asset('images/icons/apple-icon-144x144.png')); ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo e(asset('images/icons/apple-icon-152x152.png')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('images/icons/apple-icon-180x180.png')); ?>">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?php echo e(asset('images/icons/android-icon-192x192.png')); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('images/icons/favicon-32x32.png')); ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo e(asset('images/icons/favicon-96x96.png')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('images/icons/favicon-16x16.png')); ?>">

</head>
<body class="flex items-center justify-center min-h-screen bg-white">
    <div id="app"></div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\american-repair-appliance\resources\views/welcome.blade.php ENDPATH**/ ?>