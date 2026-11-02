<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - RESPER</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-sm">

    {{-- Navbar --}}
    <header class="flex items-center justify-between px-6 py-3 border-b">
        <div></div>
        <span class="font-bold text-lg">RESPER</span>
        <span class="text-xs">Nama User</span>
    </header>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="w-52 border-r p-4">
            <h2 class="font-bold text-lg mb-6">DASHBOARD</h2>
            <nav class="space-y-2">
                <a href="#" class="block px-3 py-2">Homepage</a>
                <a href="#" class="block px-3 py-2">Inventory</a>
                <a href="#" class="block px-3 py-2">Sales</a>
                <a href="#" class="block px-3 py-2">Staff</a>

                <div class="px-3 py-2 rounded bg-gray-200 font-medium">Order</div>
                <a href="/order/pesanan"
                   class="block ml-6 px-3 py-1 text-xs rounded {{ request()->is('order/pesanan') ? 'bg-gray-200 font-bold' : '' }}">
                    Pesanan
                </a>
                <a href="/order/delivery"
                   class="block ml-6 px-3 py-1 text-xs rounded {{ request()->is('order/delivery') ? 'bg-gray-200 font-bold' : '' }}">
                    Delivery Order
                </a>

                <a href="#" class="block px-3 py-2">Schedule</a>
            </nav>
        </aside>

        {{-- Isi halaman --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
