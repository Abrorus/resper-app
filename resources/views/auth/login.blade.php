<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | RESPER</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-['Poppins'] text-gray-900 antialiased">
<div class="flex min-h-screen">

    {{-- Kolom kiri: form --}}
    <div class="flex w-full flex-col px-8 py-10 sm:px-16 lg:w-1/2 lg:px-24">
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <svg class="h-6 w-6 text-[#27AE60]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 5 4 12l7 7"/><path d="M20 5l-7 7 7 7"/>
            </svg>
            <span class="text-sm font-bold tracking-wide">RESPER</span>
        </a>

        <div class="my-auto w-full max-w-sm py-12">
            <h1 class="text-2xl font-semibold">Welcome back</h1>
            <p class="mt-1 text-lg font-semibold">Please login to your account</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5" novalidate>
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
                        </svg>
                        <input id="email" name="email" type="email" value="{{ old('email') }}"
                               placeholder="Email" autocomplete="email" autofocus required
                               @class([
                                   'w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#27AE60]/20',
                                   'border-red-500' => $errors->has('email'),
                                   'border-gray-300 focus:border-[#27AE60]' => ! $errors->has('email'),
                               ])>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        </svg>
                        <input id="password" name="password" type="password"
                               placeholder="Password" autocomplete="current-password" required
                               @class([
                                   'w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#27AE60]/20',
                                   'border-red-500' => $errors->has('password'),
                                   'border-gray-300 focus:border-[#27AE60]' => ! $errors->has('password'),
                               ])>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input type="checkbox" name="remember" class="h-4 w-4 accent-[#27AE60]">
                        Remember me
                    </label>
                    <a href="{{ url('/forgot-password') }}" class="text-[#27AE60] hover:underline">Forgot password?</a>
                </div>

                <button type="submit"
                        class="w-full rounded-full bg-[#27AE60] py-2.5 text-sm font-semibold text-white transition hover:bg-[#229954] focus:outline-none focus:ring-2 focus:ring-[#27AE60]/40">
                    Sign In
                </button>

                <p class="text-xs">
                    Don't have an account?
                    <a href="{{ url('/register') }}" class="font-semibold text-[#27AE60] hover:underline">Sign Up</a>
                </p>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400">RESPER by Kelompok 6</p>
    </div>

    {{-- Kolom kanan: foto (disembunyikan di layar kecil) --}}
    <div class="hidden bg-gray-100 lg:block lg:w-1/2">
        <img src="{{ asset('images/login-hero.jpg') }}" alt="" class="h-full w-full object-cover">
    </div>
</div>
</body>
</html>