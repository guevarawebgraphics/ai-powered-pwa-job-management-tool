<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>American Appliance Repair</title>
@if (app()->environment('local'))
    {{-- Development: Use Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    {{-- Production: Load hashed files from manifest.json --}}
    @php
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
    @endphp

    {{-- Load all CSS files --}}
    @foreach ($cssFiles as $cssFile)
        <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    @endforeach

    {{-- Load the JS file --}}
    @if ($jsFile)
        <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
    @endif
@endif


    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#232850">

    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-512x512.png') }}">

</head>
<body class="flex items-center justify-center min-h-screen bg-white">
    <div id="app"></div>
</body>
</html>
