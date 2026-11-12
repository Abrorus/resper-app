@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / <a href="{{ route('finance.expenses.index') }}" class="hover:text-[#13211e] hover:underline">Transaksi Pengeluaran</a> / Catat</p>
    @include('keuangan._tabs')

    <div class="max-w-2xl">
        <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Catat pengeluaran</h1>
        <p class="mb-6 mt-1.5 text-[#5a6b66]">Isi data pengeluaran. Setelah disimpan, laporan keuangan langsung diperbarui.</p>

        <form method="POST" action="{{ route('finance.expenses.store') }}" novalidate class="flex flex-col gap-5 rounded-xl border border-[#dce3e0] bg-white p-5 md:p-7">
            @csrf
            @include('keuangan.expenses._form')
            <div class="flex justify-end gap-2.5 border-t border-[#dce3e0] pt-5">
                <a href="{{ route('finance.expenses.index') }}" class="rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Batal</a>
                <button type="submit" class="cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Simpan pengeluaran</button>
            </div>
        </form>
    </div>
@endsection
