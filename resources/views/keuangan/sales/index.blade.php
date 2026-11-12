@extends('layouts.app')

@section('title', 'Transaksi Penjualan')

@php $rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.'); @endphp

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="{{ route('finance.report') }}" class="hover:text-[#13211e] hover:underline">Keuangan</a> / Transaksi Penjualan</p>
    @include('keuangan._tabs')

    <div class="mb-6">
        <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Transaksi Penjualan</h1>
        <p class="mt-1.5 text-[#5a6b66]">Pilih Order yang sudah dibayar pelanggan, lalu catat metode pembayarannya. Penjualan hari ini <b class="font-semibold text-[#12795a]">{{ $rp($totalHariIni) }}</b>.</p>
    </div>

    @include('keuangan._flash')

    {{-- Order belum tercatat --}}
    <section class="mb-6" aria-labelledby="judul-order">
        <h2 id="judul-order" class="mb-3 font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Order belum tercatat <span class="font-medium text-[#5a6b66]">({{ $pendingOrders->count() }})</span></h2>

        @if ($pendingOrders->isEmpty())
            <div class="rounded-xl border border-dashed border-[#dce3e0] bg-white px-6 py-10 text-center text-[#5a6b66]">
                Tidak ada Order yang perlu dicatat. Order baru dari modul Pemesanan akan muncul di sini.
            </div>
        @else
            <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                @foreach ($pendingOrders as $order)
                    @php $aktif = (int) old('order_id') === $order->id && $errors->sales->any(); @endphp
                    <form method="POST" action="{{ route('finance.sales.store') }}" novalidate
                          class="flex flex-col rounded-xl border bg-white p-5 {{ $aktif ? 'border-[#c2362f] ring-3 ring-[#f9e5e3]' : 'border-[#dce3e0]' }}">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <b class="font-['Bricolage_Grotesque',sans-serif] text-[1.15rem] font-bold">#{{ $order->order_number }}</b>
                                <small class="block text-[0.85rem] text-[#5a6b66]">
                                    {{ $order->customer_name ?? 'Pelanggan' }}{{ $order->table_number ? ', meja ' . $order->table_number : '' }}{{ $order->platform ? ', ' . $order->platform : '' }}, {{ $order->created_at?->format('H.i') }} WIB
                                </small>
                            </div>
                            <span class="whitespace-nowrap font-['Bricolage_Grotesque',sans-serif] text-[1.15rem] font-bold tabular-nums">{{ $rp($order->total) }}</span>
                        </div>

                        <ul class="my-3.5 flex flex-col gap-1 border-y border-dashed border-[#dce3e0] py-3 text-[0.9rem]">
                            @forelse ($order->items as $item)
                                <li class="flex justify-between gap-3"><span>{{ $item->quantity }}x {{ $item->menu_name }}</span><span class="tabular-nums text-[#5a6b66]">{{ $rp($item->price * $item->quantity) }}</span></li>
                            @empty
                                <li class="text-[#5a6b66]">Rincian menu tidak tersedia.</li>
                            @endforelse
                        </ul>

                        <fieldset class="mb-3">
                            <legend class="mb-1.5 text-[0.9rem] font-medium">Metode pembayaran</legend>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($methods as $m)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $m }}" class="peer sr-only" @checked($aktif && old('payment_method') === $m)>
                                        <span class="inline-block rounded-full border border-[#dce3e0] px-3 py-1.5 text-[0.9rem] hover:border-[#13211e] peer-checked:border-[#0b3d31] peer-checked:bg-[#0b3d31] peer-checked:font-semibold peer-checked:text-white peer-focus-visible:ring-3 peer-focus-visible:ring-[#0b3d31]/25">{{ $m }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if ($aktif && $errors->sales->has('payment_method'))<p class="mt-1.5 text-[0.85rem] text-[#c2362f]">{{ $errors->sales->first('payment_method') }}</p>@endif
                        </fieldset>

                        <label class="mb-4 flex flex-col gap-1.5">
                            <span class="text-[0.9rem] font-medium">Nominal diterima</span>
                            <span class="flex items-center rounded-lg border bg-white focus-within:border-[#0b3d31] focus-within:ring-3 focus-within:ring-[#0b3d31]/12 {{ $aktif && $errors->sales->has('amount') ? 'border-[#c2362f]' : 'border-[#dce3e0]' }}">
                                <span class="pl-3 font-semibold text-[#5a6b66]">Rp</span>
                                <input type="number" name="amount" inputmode="numeric" min="1" step="1" value="{{ $aktif ? old('amount') : $order->total }}" class="w-full bg-transparent px-2.5 py-2 font-semibold tabular-nums outline-none">
                            </span>
                            @if ($aktif && $errors->sales->has('amount'))<span class="text-[0.85rem] text-[#c2362f]">{{ $errors->sales->first('amount') }}</span>@endif
                        </label>

                        <button type="submit" class="mt-auto cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Catat penjualan</button>
                    </form>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Riwayat penjualan --}}
    <section class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6 md:pb-5" aria-labelledby="judul-riwayat">
        <h2 id="judul-riwayat" class="font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Riwayat transaksi penjualan</h2>
        <p class="mb-3 mt-0.5 text-[0.92rem] text-[#5a6b66]">Transaksi yang salah input dapat dikoreksi atau dihapus. Order dari transaksi yang dihapus kembali ke daftar di atas.</p>

        @if ($sales->isEmpty())
            <p class="rounded-lg border border-dashed border-[#dce3e0] px-6 py-10 text-center text-[#5a6b66]">Belum ada transaksi penjualan yang tercatat.</p>
        @else
            <table class="block w-full border-collapse md:table">
                <thead class="hidden md:table-header-group">
                    <tr class="border-b border-[#dce3e0] text-left text-[0.88rem] text-[#5a6b66]">
                        <th class="p-3 font-medium">Tanggal</th>
                        <th class="p-3 font-medium">Order</th>
                        <th class="p-3 font-medium">Kasir</th>
                        <th class="p-3 text-right font-medium">Nominal</th>
                        <th class="p-3"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody class="block md:table-row-group">
                    @foreach ($sales as $s)
                        <tr class="grid grid-cols-[1fr_auto] gap-x-3 gap-y-1.5 border-b border-[#dce3e0] py-3.5 hover:bg-[#f8faf9] md:table-row md:py-0">
                            <td class="col-start-1 row-start-2 text-[0.85rem] tabular-nums text-[#5a6b66] md:table-cell md:p-3 md:text-[15px] md:text-[#13211e]">{{ $s->transaction_date->format('d/m/Y') }} <small class="text-[#5a6b66] md:block">{{ $s->created_at?->format('H.i') }} WIB</small></td>
                            <td class="col-start-1 row-start-1 md:table-cell md:p-3">
                                <b class="font-semibold">{{ $s->label() }}</b>
                                <small class="block text-[0.85rem] text-[#5a6b66]">Dibayar dengan {{ $s->payment_method }}</small>
                            </td>
                            <td class="hidden text-[#5a6b66] md:table-cell md:p-3">{{ $s->user?->name ?? 'Tidak diketahui' }}</td>
                            <td class="col-start-2 row-start-1 whitespace-nowrap text-right font-semibold tabular-nums text-[#12795a] md:table-cell md:p-3">+ {{ $rp($s->amount) }}</td>
                            <td class="col-start-2 row-start-2 md:table-cell md:w-px md:p-3">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('finance.sales.show', $s) }}" class="whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] hover:border-[#13211e]">Struk</a>
                                    <a href="{{ route('finance.sales.edit', $s) }}" class="whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] hover:border-[#13211e]">Koreksi</a>
                                    <button type="button" data-hapus="{{ route('finance.sales.destroy', $s) }}" data-label="{{ $s->label() }}"
                                            class="cursor-pointer whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] text-[#c2362f] hover:border-[#c2362f]">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $sales->onEachSide(1)->links() }}</div>
        @endif
    </section>

    @include('keuangan._hapus')
@endsection
