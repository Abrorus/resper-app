@extends('layouts.app')

@section('title', 'Koreksi Penjualan')

@php
    $kelasInput = 'w-full rounded-lg border border-[#dce3e0] bg-white px-3 py-2.5 outline-none focus:border-[#0b3d31] focus:ring-3 focus:ring-[#0b3d31]/12 aria-invalid:border-[#c2362f] aria-invalid:ring-3 aria-invalid:ring-[#f9e5e3]';
@endphp

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / <a href="{{ route('finance.sales.index') }}" class="hover:text-[#13211e] hover:underline">Transaksi Penjualan</a> / Koreksi</p>
    @include('keuangan._tabs')

    <div class="max-w-2xl">
        <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Koreksi penjualan</h1>
        <p class="mb-6 mt-1.5 text-[#5a6b66]">
            {{ $sale->label() }}, total Order <b class="font-semibold text-[#13211e]">Rp {{ number_format($sale->order->total, 0, ',', '.') }}</b>.
            Setiap perubahan disimpan di riwayat koreksi.
        </p>

        @include('keuangan._flash')

        <form method="POST" action="{{ route('finance.sales.update', $sale) }}" novalidate class="flex flex-col gap-5 rounded-xl border border-[#dce3e0] bg-white p-5 md:p-7">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div role="alert" class="rounded-xl bg-[#f9e5e3] px-4 py-3 text-[#a32c26] ring-1 ring-[#c2362f]/20">
                    <p class="font-semibold">Data koreksi belum bisa disimpan:</p>
                    <ul class="mt-1 list-disc pl-5">@foreach ($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <label class="flex flex-col gap-1.5">
                    <span class="text-[0.92rem] font-medium">Metode pembayaran</span>
                    <select name="payment_method" @error('payment_method') aria-invalid="true" @enderror class="{{ $kelasInput }}">
                        @foreach ($methods as $m)
                            <option value="{{ $m }}" @selected(old('payment_method', $sale->payment_method) === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex flex-col gap-1.5">
                    <span class="text-[0.92rem] font-medium">Tanggal transaksi</span>
                    <input type="date" name="transaction_date" max="{{ now()->toDateString() }}" value="{{ old('transaction_date', $sale->transaction_date->toDateString()) }}"
                           @error('transaction_date') aria-invalid="true" @enderror class="{{ $kelasInput }}">
                </label>
                <label class="flex flex-col gap-1.5 md:col-span-2">
                    <span class="text-[0.92rem] font-medium">Nominal</span>
                    <span class="flex items-center rounded-lg border border-[#dce3e0] bg-white focus-within:border-[#0b3d31] focus-within:ring-3 focus-within:ring-[#0b3d31]/12 has-aria-invalid:border-[#c2362f] has-aria-invalid:ring-3 has-aria-invalid:ring-[#f9e5e3]">
                        <span class="pl-3 font-semibold text-[#5a6b66]">Rp</span>
                        <input type="number" name="amount" inputmode="numeric" min="1" step="1" value="{{ old('amount', $sale->amount) }}"
                               @error('amount') aria-invalid="true" @enderror class="w-full bg-transparent px-2.5 py-2.5 text-[1.1rem] font-semibold tabular-nums outline-none">
                    </span>
                </label>
            </div>

            <div class="flex flex-wrap justify-between gap-2.5 border-t border-[#dce3e0] pt-5">
                <button type="button" data-hapus="{{ route('finance.sales.destroy', $sale) }}" data-label="{{ $sale->label() }}"
                        class="cursor-pointer rounded-lg px-3 py-2 text-[#c2362f] hover:bg-[#f9e5e3]">Hapus transaksi</button>
                <div class="flex gap-2.5">
                    <a href="{{ route('finance.sales.index') }}" class="rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Batal</a>
                    <button type="submit" class="cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Simpan koreksi</button>
                </div>
            </div>
        </form>
    </div>

    @include('keuangan._hapus')
@endsection
