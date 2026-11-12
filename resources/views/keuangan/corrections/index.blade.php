@extends('layouts.app')

@section('title', 'Koreksi Transaksi')

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / Koreksi Transaksi</p>
    @include('keuangan._tabs')

    <div class="mb-6">
        <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Koreksi Transaksi</h1>
        <p class="mt-1.5 max-w-2xl text-[#5a6b66]">
            Riwayat setiap perubahan dan penghapusan transaksi. Untuk mengoreksi, buka
            <a href="{{ route('finance.sales.index') }}" class="font-semibold text-[#12795a] hover:underline">Transaksi Penjualan</a>,
            <a href="{{ route('finance.expenses.index') }}" class="font-semibold text-[#12795a] hover:underline">Transaksi Pengeluaran</a>, atau
            <a href="{{ route('finance.report') }}" class="font-semibold text-[#12795a] hover:underline">Laporan Keuangan</a>, lalu pilih Koreksi pada transaksi yang salah.
        </p>
    </div>

    @include('keuangan._flash')

    <section class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-4 md:px-6 md:pb-5" aria-label="Riwayat koreksi">
        <div class="mb-3 flex gap-1 overflow-x-auto">
            @foreach (['' => 'Semua', 'Penjualan' => 'Penjualan', 'Pengeluaran' => 'Pengeluaran'] as $nilai => $label)
                <a href="{{ route('finance.corrections.index', $nilai ? ['jenis' => $nilai] : []) }}" @if ($jenis === $nilai) aria-current="page" @endif
                   class="shrink-0 rounded-lg px-3 py-2 text-[#5a6b66] hover:bg-[#f1f4f2] aria-[current=page]:bg-[#f1f4f2] aria-[current=page]:font-semibold aria-[current=page]:text-[#13211e]">{{ $label }}</a>
            @endforeach
        </div>

        @forelse ($logs as $log)
            @if ($loop->first)<ol class="flex flex-col">@endif
            <li class="grid gap-x-5 gap-y-1 border-t border-[#dce3e0] py-4 md:grid-cols-[170px_1fr]">
                <div class="text-[0.88rem] text-[#5a6b66]">
                    <time datetime="{{ $log->created_at->toIso8601String() }}" class="block font-medium text-[#13211e]">{{ $log->created_at->translatedFormat('d M Y') }}</time>
                    {{ $log->created_at->format('H.i') }} WIB, {{ $log->user?->name ?? 'Pengguna' }}
                </div>
                <div>
                    <p class="flex flex-wrap items-center gap-2">
                        <b class="font-semibold">{{ $log->transaction_type }} {{ $log->label }}</b>
                        <span class="rounded-full px-2 py-px text-[0.78rem] font-semibold {{ $log->action === 'hapus' ? 'bg-[#f9e5e3] text-[#a32c26]' : 'bg-[#fbf1d9] text-[#8a5d00]' }}">{{ $log->action === 'hapus' ? 'Dihapus' : 'Diubah' }}</span>
                    </p>
                    <ul class="mt-1 text-[0.95rem]">
                        @foreach ($log->changes() as $perubahan)<li>{{ $perubahan }}</li>@endforeach
                    </ul>
                </div>
            </li>
            @if ($loop->last)</ol>@endif
        @empty
            <p class="rounded-lg border border-dashed border-[#dce3e0] px-6 py-10 text-center text-[#5a6b66]">
                {{ $jenis ? 'Belum ada koreksi untuk transaksi ' . strtolower($jenis) . '.' : 'Belum ada koreksi transaksi. Semua transaksi masih sesuai input awal.' }}
            </p>
        @endforelse

        <div class="mt-4">{{ $logs->links() }}</div>
    </section>
@endsection
