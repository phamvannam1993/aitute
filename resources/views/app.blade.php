<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" translate="no">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/assets/img/ai3goc/logo/img.svg" type="image/x-icon">

    <meta property="og:video" content="{{Session::get('video_url') ? Session::get('video_url') : '' }}">
    <meta property="og:video:type" content="video/mp4">
    <meta property="og:video:width" content="1280">
    <meta property="og:video:height" content="720">
    <meta property="og:image" content="{{Session::get('thumbnail') ? Session::get('thumbnail') : '' }}">
	
    <meta name="googlebot" content="notranslate">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Import Lexend Deca font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://aiwow.gotechjsc.com/static/plugin.js"></script>

    <script async src=https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('GOOGLE_ANALYTICS') }}');
    </script>


    <style>
        @font-face {
            font-family: 'fontello';
            src: url('/assets/vendor/fontello/font/fontello.eot?45382306');
            src: url('/assets/vendor/fontello/font/fontello.eot?45382306#iefix') format('embedded-opentype'),
                url('/assets/vendor/fontello/font/fontello.woff?45382306') format('woff'),
                url('/assets/vendor/fontello/font/fontello.ttf?45382306') format('truetype'),
                url('/assets/vendor/fontello/font/fontello.svg?45382306#fontello') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        .demo-icon {
            font-family: "fontello";
            font-style: normal;
            font-weight: normal;
            speak: never;

            display: inline-block;
            text-decoration: inherit;
            width: 1em;
            margin-right: .2em;
            text-align: center;
            font-variant: normal;
            text-transform: none;
            line-height: 1em;
            margin-left: .2em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
