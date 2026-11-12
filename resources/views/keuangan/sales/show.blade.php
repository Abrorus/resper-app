@extends('layouts.app')

@section('title', 'Struk ' . $sale->label())

@php $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp

@push('styles')
<style>
    @media print {
        aside, .no-print { display: none !important; }
        body { background: #fff !important; }
        main { padding: 0 !important; }
        #struk { border: 0 !important; box-shadow: none !important; margin: 0 auto !important; }
    }
</style>
@endpush

@section('content')
    <div class="no-print">
        <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / <a href="{{ route('finance.sales.index') }}" class="hover:text-[#13211e] hover:underline">Transaksi Penjualan</a> / Struk</p>
        @include('keuangan._tabs')
        @include('keuangan._flash')
    </div>

    <article id="struk" class="mx-auto mt-2 max-w-sm rounded-xl border border-[#dce3e0] bg-white px-6 py-7 shadow-[0_18px_40px_-28px_rgba(11,61,49,.45)]">
        <header class="text-center">
            <p class="font-['Bricolage_Grotesque',sans-serif] text-[1.4rem] font-extrabold tracking-wide text-[#0b3d31]">RESPER</p>
            <p class="text-[0.85rem] text-[#5a6b66]">Struk transaksi penjualan</p>
        </header>

        <dl class="mt-5 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 border-t border-dashed border-[#dce3e0] pt-4 text-[0.9rem]">
            <dt class="text-[#5a6b66]">Order</dt><dd class="text-right font-semibold">#{{ $sale->order->order_number }}</dd>
            <dt class="text-[#5a6b66]">Tanggal</dt><dd class="text-right tabular-nums">{{ $sale->transaction_date->format('d/m/Y') }}, {{ $sale->created_at?->format('H.i') }}</dd>
            <dt class="text-[#5a6b66]">Pelanggan</dt><dd class="text-right">{{ $sale->order->customer_name ?? '-' }}</dd>
            <dt class="text-[#5a6b66]">Kasir</dt><dd class="text-right">{{ $sale->user?->name ?? '-' }}</dd>
        </dl>

        <ul class="mt-4 flex flex-col gap-1.5 border-t border-dashed border-[#dce3e0] pt-4 text-[0.92rem]">
            @foreach ($sale->order->items as $item)
                <li class="flex justify-between gap-3">
                    <span>{{ $item->menu_name }}<small class="block text-[#5a6b66]">{{ $item->quantity }} x {{ $rp($item->price) }}</small></span>
                    <span class="tabular-nums">{{ $rp($item->price * $item->quantity) }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 flex items-baseline justify-between border-t border-dashed border-[#dce3e0] pt-4">
            <span class="font-semibold">Total</span>
            <span class="font-['Bricolage_Grotesque',sans-serif] text-[1.5rem] font-extrabold tabular-nums">{{ $rp($sale->amount) }}</span>
        </div>
        <p class="mt-1 text-right text-[0.9rem] text-[#5a6b66]">Dibayar dengan {{ $sale->payment_method }}</p>

        <p class="mt-6 text-center text-[0.85rem] text-[#5a6b66]">Terima kasih atas kunjungan Anda.</p>
    </article>

    <div class="no-print mx-auto mt-5 flex max-w-sm justify-center gap-2.5">
        <a href="{{ route('finance.sales.index') }}" class="rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Kembali</a>
        <button type="button" onclick="window.print()" class="cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Cetak struk</button>
    </div>
@endsection
