<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | RESPER</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $inputCls = fn (string $field) => 'w-full rounded-lg border py-2.5 pl-10 pr-3 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-[#27AE60]/20 '
        . ($errors->has($field) ? 'border-red-500' : 'border-gray-300 focus:border-[#27AE60]');
    $icons = [
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
        'lock' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
    ];
    $fields = [
        ['name', 'Nama', 'text', 'Nama lengkap', 'name', 'user'],
        ['email', 'Email', 'email', 'Email', 'email', 'mail'],
        ['phone', 'Nomor Telepon', 'tel', '08xxxxxxxxxx', 'tel', 'phone'],
        ['password', 'Password', 'password', 'Minimal 8 karakter', 'new-password', 'lock'],
        ['password_confirmation', 'Konfirmasi Password', 'password', 'Ulangi password', 'new-password', 'lock'],
    ];
@endphp
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
            <h1 class="text-2xl font-semibold">Create an account</h1>
            <p class="mt-1 text-lg font-semibold">Daftarkan akun baru Anda</p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-4" novalidate>
                @csrf

                @foreach ($fields as [$name, $label, $type, $placeholder, $autocomplete, $icon])
                    <div>
                        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium">
                            {{ $label }} <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$icon] !!}</svg>
                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
                                   value="{{ $type === 'password' ? '' : old($name) }}"
                                   placeholder="{{ $placeholder }}" autocomplete="{{ $autocomplete }}" required
                                   @if ($loop->first) autofocus @endif
                                   class="{{ $inputCls($name) }}">
                        </div>
                        @error($name)
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <button type="submit"
                        class="mt-2 w-full rounded-full bg-[#27AE60] py-2.5 text-sm font-semibold text-white transition hover:bg-[#229954] focus:outline-none focus:ring-2 focus:ring-[#27AE60]/40">
                    Daftar
                </button>

                <p class="text-xs">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-[#27AE60] hover:underline">Sign In</a>
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
