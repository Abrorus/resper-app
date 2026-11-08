@extends('layouts.app')

@section('title', 'Delivery Order')

@section('content')
    <h1 class="font-bold text-lg">Nama Restaurant - Pesanan</h1>
    <p class="text-center font-medium mb-8">Delivery Order</p>

    <h3 class="font-semibold mb-3 ml-24">Pesanan Masuk</h3>

    <div class="max-w-xl mx-auto bg-gray-100 p-5">
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
@endsection
