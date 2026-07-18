<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="font-sans text-secondary antialiased">
        <div class="min-vh-100 d-flex d-flex-column sm:justify-content-center align-items-center pt-6 sm:pt-0 bg-light">
            <div>
                <a href="/">
                    <x-application-logo style="height: 96px;" class="w-24 sm:w-28 h-auto max- object-fit-contain mx-auto transition-" />
                </a>
            </div>

            <div class="w-100 sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
