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
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        ?>
    
        <?php if($cssFile): ?>
            <link rel="stylesheet" href="<?php echo e(asset('build/' . $cssFile)); ?>">
        <?php endif; ?>
        <?php if($jsFile): ?>
            <script type="module" src="<?php echo e(asset('build/' . $jsFile)); ?>"></script>
        <?php endif; ?>
    <?php endif; ?>

    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <meta name="theme-color" content="#232850">

    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo e(asset('images/icons/icon-192x192.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('images/icons/icon-512x512.png')); ?>">

</head>
<body class="flex items-center justify-center min-h-screen bg-white">
    <div id="app"></div>
</body>
</html>
<?php /**PATH /home/applljeq/public_html/TheGuild/staging/ara/resources/views/welcome.blade.php ENDPATH**/ ?>