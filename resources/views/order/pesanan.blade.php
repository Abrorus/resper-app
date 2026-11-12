@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
<div x-data="pesanan(@js($menus))">
    <h1 class="font-bold text-lg">Nama Restaurant - Pesanan</h1>
    <p class="text-center font-medium border-b-2 border-blue-500 pb-2 mb-4">Pilih Menu & Check Out</p>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="grid grid-cols-2 gap-6">
        {{-- Kiri: Pilih Menu --}}
        <div class="border rounded-3xl p-4">
            <h3 class="font-semibold mb-2">Pilih Menu</h3>
            <label class="text-xs">Kategori</label>
            <select x-model="kategori" class="w-full border rounded-lg p-2 mb-4">
                <template x-for="k in kategoriList" :key="k">
                    <option :value="k" x-text="k"></option>
                </template>
            </select>

            <template x-for="m in menus.filter(m => m.category === kategori)" :key="m.id">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-20 h-14 bg-gray-200 rounded"></div>
                    <div class="flex-1">
                        <p class="text-xs" x-text="m.name"></p>
                        <span class="bg-blue-200 text-xs px-2 rounded-full" x-text="rupiah(m.price)"></span>
                    </div>
                    <button type="button" x-on:click="tambah(m.id)" class="w-6 h-6 rounded-full bg-gray-300">+</button>
                    <button type="button" x-on:click="kurang(m.id)" class="text-yellow-500">🗑</button>
                </div>
            </template>
        </div>

        {{-- Kanan: Detail Pesanan --}}
        <form method="POST" action="{{ route('order.store') }}" class="bg-gray-100 p-5 h-fit">
            @csrf
            <h3 class="font-semibold mb-3">Detail Pesanan & Check Out</h3>

            <div class="text-xs space-y-2 mb-4">
                <p class="text-red-600">Nomor Pesanan : <span class="text-gray-500">(otomatis)</span></p>
                <label class="block text-red-600">Nama Pelanggan :
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="border rounded px-2 py-1 text-black ml-1">
                </label>
                <label class="block text-red-600">Jumlah Tamu :
                    <input type="number" min="1" name="guest_count" value="{{ old('guest_count') }}" class="border rounded px-2 py-1 text-black ml-1 w-20">
                </label>
                <label class="block text-red-600">Nomor Meja :
                    <input type="text" name="table_number" value="{{ old('table_number') }}" class="border rounded px-2 py-1 text-black ml-1 w-20">
                </label>
            </div>

            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left"><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <template x-for="(item, i) in items" :key="item.id">
                        <tr>
                            <td class="py-1" x-text="item.name"></td>
                            <td x-text="rupiah(item.price)"></td>
                            <td x-text="item.qty"></td>
                            <td x-text="rupiah(item.subtotal)"></td>
                            <input type="hidden" :name="`items[${i}][menu_id]`" :value="item.id">
                            <input type="hidden" :name="`items[${i}][quantity]`" :value="item.qty">
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="items.length === 0" class="text-xs text-gray-500 py-2">Belum ada item. Klik + pada menu.</p>

            <div class="flex justify-between font-bold mt-6">
                <span>Total</span><span x-text="rupiah(total)"></span>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" x-bind:disabled="items.length === 0"
                        class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs disabled:opacity-40">Proses Pesanan</button>
                <button type="button" x-on:click="cart = {}"
                        class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Batal</button>
            </div>
        </form>
    </div>

    {{-- Riwayat pesanan (READ + UPDATE + DELETE) --}}
    <h3 class="font-semibold mt-8 mb-2">Riwayat Pesanan</h3>
    <table class="w-full text-xs border">
        <thead class="bg-gray-100 text-left">
            <tr><th class="p-2">No. Pesanan</th><th>Pelanggan</th><th>Meja</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-t">
                    <td class="p-2">{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name ?? '-' }}</td>
                    <td>{{ $order->table_number ?? '-' }}</td>
                    <td>Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td class="flex gap-2 py-1">
                        @if ($order->status === 'pending')
                            <form method="POST" action="{{ route('order.process', $order) }}">
                                @csrf @method('PATCH')
                                <button class="text-blue-600">Proses</button>
                            </form>
                            <form method="POST" action="{{ route('order.destroy', $order) }}">
                                @csrf @method('DELETE')
                                <button class="text-red-600">Batal</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-3 text-gray-500">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pesanan', (menus) => ({
            menus,
            cart: {},
            kategori: menus.length ? menus[0].category : '',
            get kategoriList() { return [...new Set(this.menus.map(m => m.category))]; },
            get items() {
                return this.menus.filter(m => this.cart[m.id]).map(m => ({
                    ...m, qty: this.cart[m.id], subtotal: m.price * this.cart[m.id],
                }));
            },
            get total() { return this.items.reduce((s, i) => s + i.subtotal, 0); },
            tambah(id) { this.cart[id] = (this.cart[id] || 0) + 1; },
            kurang(id) {
                if (this.cart[id] > 1) this.cart[id]--; else delete this.cart[id];
            },
            rupiah(n) { return 'Rp. ' + Number(n).toLocaleString('id-ID'); },
        }));
    });
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
@endpush
