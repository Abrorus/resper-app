<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | RESPER</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:500,600,700,800|instrument-sans:400,500,600" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#f1f4f2] font-sans text-[15px] text-[#13211e] antialiased">
<div class="min-h-screen md:grid md:grid-cols-[252px_1fr]">
    @include('partials.sidebar')

    <main id="atas" class="w-full min-w-0 px-4 pb-10 pt-5 md:px-10 md:pb-14 md:pt-7">
        @yield('content')
    </main>
</div>

@stack('modals')
@stack('scripts')
</body>
</html>