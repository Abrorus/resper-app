@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <h1 class="font-bold text-lg">Nama Restaurant - Pesanan</h1>
    <p class="text-center font-medium border-b-2 border-blue-500 pb-2 mb-4">Pilih Menu & Check Out</p>

    <div class="grid grid-cols-2 gap-6">
        {{-- Kiri: Pilih Menu --}}
        <div class="border rounded-3xl p-4">
            <h3 class="font-semibold mb-2">Pilih Menu</h3>
            <label class="text-xs">Kategori</label>
            <select class="w-full border rounded-lg p-2 mb-4">
                <option>Makanan Utama</option>
                <option>Minuman</option>
                <option>Snack</option>
            </select>

            @foreach ([
                ['Nasi Goreng Spesial', 'Rp. 24.000'],
                ['Steak Tenderloin', 'Rp. 115.000'],
                ['Soto Ayam', 'Rp. 15.000'],
                ['Penyetan Ayam Spesial', 'Rp. 18.000'],
            ] as $menu)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-20 h-14 bg-gray-200 rounded"></div>
                    <div class="flex-1">
                        <p class="text-xs">{{ $menu[0] }}</p>
                        <span class="bg-blue-200 text-xs px-2 rounded-full">{{ $menu[1] }}</span>
                    </div>
                    <button class="w-6 h-6 rounded-full bg-gray-300">+</button>
                    <button class="text-yellow-500">🗑</button>
                </div>
            @endforeach
        </div>

        {{-- Kanan: Detail Pesanan --}}
        <div class="bg-gray-100 p-5 h-fit">
            <h3 class="font-semibold mb-3">Detail Pesanan & Check Out</h3>
            <div class="text-red-600 text-xs space-y-1 mb-4">
                <p>Nomor Pesanan :</p>
                <p>Nama Pelanggan :</p>
                <p>Jumlah Tamu :</p>
                <p>Nomor Meja :</p>
            </div>

            <table class="w-full text-xs">
                <thead>
                    <tr class="text-left"><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Total</th></tr>
                </thead>
                <tbody>
                    <tr><td class="py-1">Nasi Goreng Spesial</td><td>Rp. 24.000</td><td>2</td><td>Rp. 48.000</td></tr>
                    <tr><td class="py-1">Jus Jeruk</td><td>Rp. 8.000</td><td>2</td><td>Rp. 16.000</td></tr>
                    <tr><td class="py-1">Kentang Goreng</td><td>Rp. 10.000</td><td>1</td><td>Rp. 10.000</td></tr>
                </tbody>
            </table>

            <div class="flex justify-between font-bold mt-6">
                <span>Total</span><span>Rp. 74.000</span>
            </div>

            <div class="flex gap-2 mt-6">
                <button class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Proses Pesanan</button>
                <button class="bg-slate-800 text-white px-3 py-1 rounded-full text-xs">Batal</button>
            </div>
        </div>
    </div>
@endsection
