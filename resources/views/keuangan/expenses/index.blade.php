@extends('layouts.app')

@section('title', 'Transaksi Pengeluaran')

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / Transaksi Pengeluaran</p>
    @include('keuangan._tabs')

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Transaksi Pengeluaran</h1>
            <p class="mt-1.5 text-[#5a6b66]">Biaya operasional restoran yang dicatat oleh Kasir.</p>
        </div>
        <a href="{{ route('finance.expenses.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            Catat pengeluaran
        </a>
    </div>

    @include('keuangan._flash')

    <section class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6 md:pb-5" aria-labelledby="judul-daftar">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 id="judul-daftar" class="font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Daftar pengeluaran</h2>
                <p class="mt-0.5 text-[0.92rem] text-[#5a6b66]">
                    {{ $jumlah }} transaksi{{ $cari !== '' || $kategori !== '' ? ' sesuai filter' : '' }}, total
                    <b class="font-semibold text-[#c2362f]">Rp {{ number_format($total, 0, ',', '.') }}</b>
                </p>
            </div>
            <form method="GET" action="{{ route('finance.expenses.index') }}" class="flex w-full flex-wrap items-center gap-2 md:w-auto">
                <label class="flex min-w-0 flex-1 items-center gap-2 rounded-lg border border-[#dce3e0] px-3 py-2 text-[#5a6b66] focus-within:border-[#0b3d31] focus-within:ring-3 focus-within:ring-[#0b3d31]/12 md:w-60 md:flex-none">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="search" name="cari" value="{{ $cari }}" placeholder="Cari keterangan" aria-label="Cari keterangan" class="w-full bg-transparent text-[#13211e] outline-none">
                </label>
                <select name="kategori" aria-label="Filter kategori" class="rounded-lg border border-[#dce3e0] bg-white px-3 py-2 outline-none focus:border-[#0b3d31]">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $k)
                        <option value="{{ $k }}" @selected($kategori === $k)>{{ $k }}</option>
                    @endforeach
                </select>
                <button type="submit" class="cursor-pointer rounded-lg border border-[#0b3d31] px-3.5 py-2 font-semibold text-[#0b3d31] hover:bg-[#0b3d31] hover:text-white">Terapkan</button>
                @if ($cari !== '' || $kategori !== '')
                    <a href="{{ route('finance.expenses.index') }}" class="px-1 text-[0.92rem] text-[#5a6b66] underline hover:text-[#13211e]">Hapus filter</a>
                @endif
            </form>
        </div>

        @if ($expenses->isEmpty())
            <div class="grid place-items-center gap-3 rounded-lg border border-dashed border-[#dce3e0] px-6 py-12 text-center text-[#5a6b66]">
                @if ($cari !== '' || $kategori !== '')
                    <p>Tidak ada pengeluaran yang cocok dengan filter. Ubah kata kunci atau pilih kategori lain.</p>
                @else
                    <p>Belum ada transaksi pengeluaran. Catat pengeluaran pertama agar laporan keuangan terisi.</p>
                    <a href="{{ route('finance.expenses.create') }}" class="font-semibold text-[#12795a] hover:underline">Catat pengeluaran</a>
                @endif
            </div>
        @else
            <table class="block w-full border-collapse md:table">
                <thead class="hidden md:table-header-group">
                    <tr class="border-b border-[#dce3e0] text-left text-[0.88rem] text-[#5a6b66]">
                        <th class="p-3 font-medium">Tanggal</th>
                        <th class="p-3 font-medium">Keterangan</th>
                        <th class="p-3 font-medium">Dicatat oleh</th>
                        <th class="p-3 text-right font-medium">Nominal</th>
                        <th class="p-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group">
                    @foreach ($expenses as $e)
                        <tr class="grid grid-cols-[1fr_auto] gap-x-3 gap-y-1.5 border-b border-[#dce3e0] py-3.5 hover:bg-[#f8faf9] md:table-row md:py-0">
                            <td class="col-start-1 row-start-2 text-[0.85rem] tabular-nums text-[#5a6b66] md:table-cell md:p-3 md:text-[15px] md:text-[#13211e]">{{ $e->transaction_date->format('d/m/Y') }}</td>
                            <td class="col-start-1 row-start-1 md:table-cell md:p-3">
                                <b class="font-semibold">{{ $e->label() }}</b>
                                <small class="block text-[0.85rem] text-[#5a6b66]">Kategori {{ strtolower($e->category) }}</small>
                            </td>
                            <td class="hidden text-[#5a6b66] md:table-cell md:p-3">{{ $e->user?->name ?? 'Tidak diketahui' }}</td>
                            <td class="col-start-2 row-start-1 whitespace-nowrap text-right font-semibold tabular-nums text-[#c2362f] md:table-cell md:p-3">- Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                            <td class="col-start-2 row-start-2 md:table-cell md:w-px md:p-3">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('finance.expenses.edit', $e) }}" class="whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] hover:border-[#13211e]">Koreksi</a>
                                    <button type="button" data-hapus="{{ route('finance.expenses.destroy', $e) }}" data-label="{{ $e->label() }} ({{ $e->transaction_date->format('d/m/Y') }})"
                                            class="cursor-pointer whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] text-[#c2362f] hover:border-[#c2362f]">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $expenses->links() }}</div>
        @endif
    </section>

    @include('keuangan._hapus')
@endsection
