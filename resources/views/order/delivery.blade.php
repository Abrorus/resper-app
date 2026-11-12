@extends('layouts.app')

@section('title', 'Delivery Order')

@section('content')
    <h1 class="font-bold text-lg">Nama Restaurant - Pesanan</h1>
    <p class="text-center font-medium mb-6">Delivery Order</p>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Pesanan Masuk</h3>
        <form method="POST" action="{{ route('order.simulate') }}">
            @csrf
            <button class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Simulasi Pesanan Masuk</button>
        </form>
    </div>

    <div class="space-y-4 max-w-xl">
        @forelse ($orders as $order)
            <div class="bg-gray-100 p-5">
                <h3 class="font-semibold mb-1">Detail Pesanan & Check Out</h3>
                <p class="text-xs text-red-600">Nomor Pesanan : <span class="text-black">{{ $order->order_number }}</span></p>
                <p class="text-xs text-red-600">Nama Pelanggan : <span class="text-black">{{ $order->customer_name ?? '-' }}</span></p>
                <p class="text-xs text-red-600 mb-3">Platform : <span class="text-black">{{ $order->platform ?? '-' }}</span></p>

                <table class="w-full text-xs">
                    <thead><tr class="text-left"><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-1">{{ $item->menu_name }}</td>
                                <td>Rp. {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp. {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-between font-bold mt-4">
                    <span>Total</span><span>Rp. {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <div class="flex gap-2 mt-4">
                    <form method="POST" action="{{ route('order.process', $order) }}">
                        @csrf @method('PATCH')
                        <button class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Proses Pesanan</button>
                    </form>
                    <form method="POST" action="{{ route('order.destroy', $order) }}">
                        @csrf @method('DELETE')
                        <button class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Batal</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada pesanan masuk. Klik "Simulasi Pesanan Masuk" untuk mencoba.</p>
        @endforelse
    </div>
@endsection
