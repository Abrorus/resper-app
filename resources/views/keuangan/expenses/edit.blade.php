@extends('layouts.app')

@section('title', 'Koreksi Pengeluaran')

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / <a href="{{ route('finance.expenses.index') }}" class="hover:text-[#13211e] hover:underline">Transaksi Pengeluaran</a> / Koreksi</p>
    @include('keuangan._tabs')

    <div class="max-w-2xl">
        <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Koreksi pengeluaran</h1>
        <p class="mb-6 mt-1.5 text-[#5a6b66]">
            {{ $expense->label() }}, dicatat {{ $expense->created_at?->translatedFormat('d M Y, H.i') }} oleh {{ $expense->user?->name ?? 'pengguna' }}.
            Setiap perubahan disimpan di riwayat koreksi.
        </p>

        @include('keuangan._flash')

        <form method="POST" action="{{ route('finance.expenses.update', $expense) }}" novalidate class="flex flex-col gap-5 rounded-xl border border-[#dce3e0] bg-white p-5 md:p-7">
            @csrf
            @method('PUT')
            @include('keuangan.expenses._form')
            <div class="flex flex-wrap justify-between gap-2.5 border-t border-[#dce3e0] pt-5">
                <button type="button" data-hapus="{{ route('finance.expenses.destroy', $expense) }}" data-label="{{ $expense->label() }} ({{ $expense->transaction_date->format('d/m/Y') }})"
                        class="cursor-pointer rounded-lg px-3 py-2 text-[#c2362f] hover:bg-[#f9e5e3]">Hapus transaksi</button>
                <div class="flex gap-2.5">
                    <a href="{{ route('finance.expenses.index') }}" class="rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Batal</a>
                    <button type="submit" class="cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Simpan koreksi</button>
                </div>
            </div>
        </form>
    </div>

    @include('keuangan._hapus')
@endsection
