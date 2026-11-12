@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
        <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]"><a href="#atas" id="crumb-keuangan" class="hover:text-[#13211e] hover:underline">Keuangan</a> / Laporan Keuangan</p>
        @include('keuangan._tabs')
        @include('keuangan._flash')

        {{-- Kepala halaman dan filter periode --}}
        <div class="mb-7 flex flex-col items-start gap-5">
            <div>
                <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Laporan Keuangan</h1>
                <p id="sub-judul" class="mt-1.5 text-[#5a6b66]">Kondisi kas restoran.</p>
            </div>
            <form id="form-filter" class="flex flex-col items-start gap-2" novalidate>
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="inline-flex rounded-full bg-[#e3e9e6] p-[3px]" role="group" aria-label="Periode cepat">
                        <button type="button" data-preset="1" aria-pressed="false" class="cursor-pointer rounded-full px-3.5 py-1.5 text-[#5a6b66] aria-pressed:bg-white aria-pressed:font-semibold aria-pressed:text-[#13211e] aria-pressed:shadow-sm">Hari ini</button>
                        <button type="button" data-preset="7" aria-pressed="true" class="cursor-pointer rounded-full px-3.5 py-1.5 text-[#5a6b66] aria-pressed:bg-white aria-pressed:font-semibold aria-pressed:text-[#13211e] aria-pressed:shadow-sm">7 hari</button>
                        <button type="button" data-preset="30" aria-pressed="false" class="cursor-pointer rounded-full px-3.5 py-1.5 text-[#5a6b66] aria-pressed:bg-white aria-pressed:font-semibold aria-pressed:text-[#13211e] aria-pressed:shadow-sm">30 hari</button>
                    </div>
                    <div id="range" class="flex flex-wrap items-center gap-1.5 rounded-lg border border-[#dce3e0] bg-white py-1 pl-3 pr-1.5 text-[#5a6b66] data-[invalid=true]:border-[#c2362f] data-[invalid=true]:ring-3 data-[invalid=true]:ring-[#f9e5e3]">
                        <input type="date" id="tgl-mulai" aria-label="Tanggal mulai" max="{{ $hariIni }}" class="bg-transparent p-1 text-[#13211e] outline-none">
                        <span>sampai</span>
                        <input type="date" id="tgl-akhir" aria-label="Tanggal akhir" max="{{ $hariIni }}" class="bg-transparent p-1 text-[#13211e] outline-none">
                    </div>
                    <button type="submit" class="cursor-pointer rounded-lg bg-[#0b3d31] px-4.5 py-2.5 font-semibold text-white hover:bg-[#12503f]">Terapkan filter</button>
                    <button type="button" id="btn-reset" class="cursor-pointer rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Atur ulang</button>
                </div>
                <p id="filter-error" role="alert" hidden class="text-[0.9rem] font-medium text-[#c2362f]"></p>
            </form>
        </div>

        {{-- Ringkasan utama --}}
        <section class="mb-5 grid overflow-hidden rounded-2xl border border-[#dce3e0] bg-white lg:grid-cols-[1.35fr_1fr]" aria-label="Ringkasan periode">
            <div class="relative px-5 py-6 md:px-9 md:py-8 lg:after:absolute lg:after:inset-y-8 lg:after:right-0 lg:after:w-px lg:after:bg-[#dce3e0]">
                <p id="hero-label" class="text-[#5a6b66]">Selisih bersih</p>
                <p id="hero-value" class="mb-3.5 mt-1.5 font-['Bricolage_Grotesque',sans-serif] text-[clamp(2.8rem,6vw,5.4rem)] font-extrabold leading-[.95] tracking-[-0.035em] tabular-nums text-[#0b3d31] wrap-anywhere data-[minus=true]:text-[#c2362f]"></p>
                <p id="hero-compare" class="flex flex-wrap items-center gap-2.5 text-[#5a6b66]"></p>
                <div id="split" role="img" class="mt-6">
                    <div class="flex h-3 gap-[3px] rounded-full bg-[#f1f4f2]">
                        <span id="split-in" class="rounded-l-full rounded-r-[3px] bg-[#12795a] transition-[width] duration-500"></span>
                        <span id="split-out" class="rounded-l-[3px] rounded-r-full bg-[#c2362f] transition-[width] duration-500"></span>
                    </div>
                    <div class="mt-2 flex justify-between text-[0.88rem] text-[#5a6b66]"><span id="split-note-in"></span><span id="split-note-out"></span></div>
                </div>
            </div>
            <div class="grid border-t border-[#dce3e0] lg:grid-rows-3 lg:border-t-0">
                <div class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 border-b border-[#dce3e0] px-5 py-4 md:px-8">
                    <span class="flex items-center gap-2 text-[#5a6b66]"><i class="inline-block h-2.5 w-2.5 rounded-[3px] bg-[#12795a]"></i>Pemasukan</span>
                    <span id="m-masuk-delta" class="col-start-2 row-span-2 row-start-2"></span>
                    <span id="m-masuk" class="col-start-1 font-['Bricolage_Grotesque',sans-serif] text-[1.6rem] font-bold tabular-nums"></span>
                    <span id="m-masuk-lalu" class="col-start-1 text-[0.85rem] text-[#5a6b66]"></span>
                </div>
                <div class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 border-b border-[#dce3e0] px-5 py-4 md:px-8">
                    <span class="flex items-center gap-2 text-[#5a6b66]"><i class="inline-block h-2.5 w-2.5 rounded-[3px] bg-[#c2362f]"></i>Pengeluaran</span>
                    <span id="m-keluar-delta" class="col-start-2 row-span-2 row-start-2"></span>
                    <span id="m-keluar" class="col-start-1 font-['Bricolage_Grotesque',sans-serif] text-[1.6rem] font-bold tabular-nums"></span>
                    <span id="m-keluar-lalu" class="col-start-1 text-[0.85rem] text-[#5a6b66]"></span>
                </div>
                <div class="grid grid-cols-[1fr_auto] items-center gap-x-4 gap-y-1 px-5 py-4 md:px-8">
                    <span class="flex items-center gap-2 text-[#5a6b66]"><i class="inline-block h-2.5 w-2.5 rounded-[3px] bg-[#e6a417]"></i>Margin bersih</span>
                    <span id="m-margin-delta" class="col-start-2 row-span-2 row-start-2"></span>
                    <span id="m-margin" class="col-start-1 font-['Bricolage_Grotesque',sans-serif] text-[1.6rem] font-bold tabular-nums"></span>
                    <span id="m-margin-lalu" class="col-start-1 text-[0.85rem] text-[#5a6b66]"></span>
                </div>
            </div>
        </section>

        {{-- Grafik --}}
        <div class="mb-5 grid gap-5 lg:grid-cols-[7fr_5fr]">
            <section class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6 md:pb-5 md:pt-6" aria-labelledby="judul-air-terjun">
                <div class="mb-3 flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 id="judul-air-terjun" class="font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Dari pemasukan ke selisih</h2>
                        <p class="mt-0.5 text-[0.92rem] text-[#5a6b66]">Ke mana uang restoran keluar selama periode ini.</p>
                    </div>
                    <div class="flex gap-4 text-[0.85rem] text-[#5a6b66]">
                        <span class="inline-flex items-center gap-1.5"><i class="inline-block h-2.5 w-2.5 rounded-[3px] bg-[#12795a]"></i>Masuk</span>
                        <span class="inline-flex items-center gap-1.5"><i class="inline-block h-2.5 w-2.5 rounded-[3px] bg-[#c2362f]"></i>Keluar</span>
                    </div>
                </div>
                <div id="air-terjun" class="grafik relative min-h-[220px]"></div>
            </section>
            <section class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6 md:pb-5 md:pt-6" aria-labelledby="judul-berjalan">
                <div class="mb-3">
                    <h2 id="judul-berjalan" class="font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Selisih berjalan</h2>
                    <p id="sub-berjalan" class="mt-0.5 text-[0.92rem] text-[#5a6b66]">Akumulasi pemasukan dikurangi pengeluaran.</p>
                </div>
                <div id="berjalan" class="grafik relative min-h-[220px]"></div>
            </section>
        </div>

        {{-- Riwayat transaksi --}}
        <section id="riwayat" tabindex="-1" class="rounded-xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 outline-none md:px-6 md:pb-5 md:pt-6" aria-labelledby="judul-riwayat">
            <div class="mb-3 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 id="judul-riwayat" class="font-['Bricolage_Grotesque',sans-serif] text-xl font-semibold">Riwayat transaksi</h2>
                    <p class="mt-0.5 text-[0.92rem] text-[#5a6b66]">Transaksi yang salah input dapat dikoreksi oleh Manajer.</p>
                </div>
                <button type="button" id="btn-csv" class="cursor-pointer rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e] disabled:cursor-not-allowed disabled:opacity-45">Unduh CSV</button>
            </div>

            <div class="mb-2 mt-1 flex flex-wrap items-center justify-between gap-3">
                <div class="flex max-w-full gap-1 overflow-x-auto" role="tablist" aria-label="Jenis transaksi">
                    <button type="button" role="tab" aria-selected="true" data-jenis="semua" class="shrink-0 cursor-pointer whitespace-nowrap rounded-lg px-2 py-2 text-[0.9rem] text-[#5a6b66] hover:bg-[#f1f4f2] aria-selected:bg-[#f1f4f2] aria-selected:font-semibold aria-selected:text-[#13211e] md:px-3 md:text-[15px]">Semua<span id="c-semua" class="ml-1.5 text-[0.8rem] font-medium text-[#5a6b66]"></span></button>
                    <button type="button" role="tab" aria-selected="false" data-jenis="Penjualan" class="shrink-0 cursor-pointer whitespace-nowrap rounded-lg px-2 py-2 text-[0.9rem] text-[#5a6b66] hover:bg-[#f1f4f2] aria-selected:bg-[#f1f4f2] aria-selected:font-semibold aria-selected:text-[#13211e] md:px-3 md:text-[15px]">Penjualan<span id="c-jual" class="ml-1.5 text-[0.8rem] font-medium text-[#5a6b66]"></span></button>
                    <button type="button" role="tab" aria-selected="false" data-jenis="Pengeluaran" class="shrink-0 cursor-pointer whitespace-nowrap rounded-lg px-2 py-2 text-[0.9rem] text-[#5a6b66] hover:bg-[#f1f4f2] aria-selected:bg-[#f1f4f2] aria-selected:font-semibold aria-selected:text-[#13211e] md:px-3 md:text-[15px]">Pengeluaran<span id="c-keluar" class="ml-1.5 text-[0.8rem] font-medium text-[#5a6b66]"></span></button>
                </div>
                <label class="flex w-full items-center gap-2 rounded-lg border border-[#dce3e0] px-3 py-2 text-[#5a6b66] focus-within:border-[#0b3d31] focus-within:ring-3 focus-within:ring-[#0b3d31]/12 md:w-auto md:min-w-60">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input type="search" id="cari" placeholder="Cari transaksi" aria-label="Cari transaksi" class="w-full bg-transparent text-[#13211e] outline-none">
                </label>
            </div>

            <table class="block w-full border-collapse md:table">
                <thead class="hidden md:table-header-group">
                    <tr class="border-b border-[#dce3e0] text-left text-[0.88rem] text-[#5a6b66]">
                        <th class="p-3 font-medium">Waktu</th>
                        <th class="p-3 font-medium">Keterangan</th>
                        <th class="p-3 font-medium">Jenis</th>
                        <th class="p-3 text-right font-medium">Nominal</th>
                        <th class="p-3" aria-label="Aksi"></th>
                    </tr>
                </thead>
                <tbody id="baris" class="block md:table-row-group"></tbody>
            </table>

            <div class="mt-3.5 flex flex-wrap items-center justify-between gap-3 text-[0.9rem] text-[#5a6b66]">
                <span id="pager-info"></span>
                <div class="flex gap-2">
                    <button type="button" id="hal-sebelum" class="cursor-pointer rounded-md border border-[#dce3e0] bg-white px-3 py-1.5 text-[0.88rem] text-[#13211e] hover:border-[#13211e] disabled:cursor-not-allowed disabled:opacity-45">Sebelumnya</button>
                    <button type="button" id="hal-berikut" class="cursor-pointer rounded-md border border-[#dce3e0] bg-white px-3 py-1.5 text-[0.88rem] text-[#13211e] hover:border-[#13211e] disabled:cursor-not-allowed disabled:opacity-45">Berikutnya</button>
                </div>
            </div>

            <details id="log" class="mt-4 border-t border-[#dce3e0] pt-3.5">
                <summary class="cursor-pointer font-semibold">Log koreksi <span class="font-medium text-[#5a6b66]">({{ $jumlahLog }})</span></summary>
                @forelse ($logs as $log)
                    @if ($loop->first)<ul class="mt-3 flex flex-col gap-2.5">@endif
                    <li class="rounded-lg bg-[#f1f4f2] px-3.5 py-2.5">
                        <b>{{ $log->transaction_type }} {{ $log->label }}</b>
                        <small class="block text-[#5a6b66]">{{ $log->action === 'hapus' ? 'Dihapus' : 'Dikoreksi' }} {{ $log->created_at->translatedFormat('d M Y, H.i') }} oleh {{ $log->user?->name ?? 'Pengguna' }}</small>
                        @foreach ($log->changes() as $perubahan)<div>{{ $perubahan }}</div>@endforeach
                    </li>
                    @if ($loop->last)</ul>@endif
                @empty
                    <p class="mt-3 text-[#5a6b66]">Belum ada koreksi. Pilih tombol Koreksi pada transaksi yang ingin diperbaiki.</p>
                @endforelse
                @if ($jumlahLog > 0)
                    <a href="{{ route('finance.corrections.index') }}" class="mt-3 inline-block font-semibold text-[#12795a] hover:underline">Lihat semua riwayat koreksi</a>
                @endif
            </details>
        </section>

        <p class="mt-6 text-[0.85rem] text-[#5a6b66]">
            @if ($periodeData)
                Data dari database untuk transaksi {{ $periodeData }}. Laporan diperbarui setiap kali transaksi dicatat atau dikoreksi.
            @else
                Belum ada transaksi di database. Catat transaksi penjualan atau pengeluaran terlebih dahulu.
            @endif
        </p>
@endsection

@push('modals')
<div id="tip" hidden class="pointer-events-none fixed z-50 -translate-x-1/2 -translate-y-[calc(100%+12px)] whitespace-nowrap rounded-md bg-[#13211e] px-2.5 py-1.5 text-[0.85rem] text-white"></div>
<div id="toasts" aria-live="polite" class="fixed inset-x-4 bottom-5 z-[60] flex flex-col items-stretch gap-2 md:left-auto md:right-5 md:items-end"></div>
@endpush

@push('scripts')
<script>
(function () {
    const data = @json($transaksi);
    const HARI_INI = @json($hariIni);
    const PER_HALAMAN = 8;
    const NS = 'http://www.w3.org/2000/svg';
    const W = { up: '#12795a', down: '#c2362f', chrome: '#0b3d31', line: '#dce3e0', muted: '#5a6b66', kunyit: '#e6a417', ink: '#13211e' };
    const $ = s => document.querySelector(s);

    // Kelas Tailwind yang dipakai pada elemen buatan JavaScript
    const KELAS = {
        delta: 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-[0.88rem] font-semibold',
        good: 'bg-[#e2f1ea] text-[#12795a]',
        bad: 'bg-[#f9e5e3] text-[#c2362f]',
        flat: 'bg-[#f1f4f2] text-[#5a6b66]',
        kosong: 'grid min-h-[220px] place-items-center rounded-lg border border-dashed border-[#dce3e0] p-6 text-center text-[#5a6b66]',
        baris: 'grid grid-cols-[1fr_auto] items-center gap-x-3 gap-y-1.5 border-b border-[#dce3e0] py-3.5 transition-colors duration-1000 hover:bg-[#f8faf9] md:table-row md:py-0',
        sorot: 'bg-[#fbf1d9]',
        selWaktu: 'col-start-1 row-start-2 text-[0.85rem] text-[#5a6b66] tabular-nums md:table-cell md:border-b md:border-[#dce3e0] md:p-3 md:text-[15px] md:text-[#13211e]',
        selKet: 'col-start-1 row-start-1 md:table-cell md:border-b md:border-[#dce3e0] md:p-3',
        selJenis: 'hidden md:table-cell md:border-b md:border-[#dce3e0] md:p-3',
        selNominal: 'col-start-2 row-start-1 whitespace-nowrap text-right text-[1.02rem] font-semibold tabular-nums md:table-cell md:border-b md:border-[#dce3e0] md:p-3',
        selAksi: 'col-start-2 row-start-2 text-right md:table-cell md:w-px md:border-b md:border-[#dce3e0] md:p-3',
        masuk: 'text-[#12795a]',
        keluar: 'text-[#c2362f]',
        pillMasuk: 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-[#e2f1ea] px-2.5 py-0.5 text-[0.85rem] font-medium text-[#12795a] before:h-[7px] before:w-[7px] before:rounded-full before:bg-current',
        pillKeluar: 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-[#f9e5e3] px-2.5 py-0.5 text-[0.85rem] font-medium text-[#c2362f] before:h-[7px] before:w-[7px] before:rounded-full before:bg-current',
        tagKoreksi: 'ml-2 inline-block rounded-full bg-[#fbf1d9] px-2 py-px align-[2px] text-[0.78rem] font-semibold text-[#8a5d00]',
        tombolKecil: 'inline-block cursor-pointer whitespace-nowrap rounded-md border border-[#dce3e0] bg-white px-3 py-1 text-[0.88rem] hover:border-[#13211e]',
        sel: 'block md:table-cell',
        toast: 'max-w-[360px] rounded-xl bg-[#13211e] px-4 py-3 text-white shadow-xl transition duration-300 starting:translate-y-2 starting:opacity-0',
        batang: 'cursor-pointer hover:brightness-110',
    };

    // ---------- Utilitas ----------
    const rp = n => (n < 0 ? '-Rp ' : 'Rp ') + Math.abs(Math.round(n)).toLocaleString('id-ID');
    const toDate = s => new Date(s + 'T00:00:00Z');
    const toIso = d => d.toISOString().slice(0, 10);
    const tambahHari = (s, n) => { const d = toDate(s); d.setUTCDate(d.getUTCDate() + n); return toIso(d); };
    const selisihHari = (a, b) => Math.round((toDate(b) - toDate(a)) / 86400000);
    const tglPanjang = s => toDate(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'UTC' });
    const tglPendek = s => s.slice(8, 10) + '/' + s.slice(5, 7) + '/' + s.slice(0, 4);
    const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    function ringkas(v) {
        const a = Math.abs(v);
        const s = a >= 1000000
            ? (a / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt'
            : (a / 1000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
        return (v < 0 ? '-' : '') + s;
    }
    function el(tag, attrs, text) {
        const node = document.createElementNS(NS, tag);
        for (const k in attrs) node.setAttribute(k, attrs[k]);
        if (text !== undefined) node.textContent = text;
        return node;
    }
    function toast(pesan) {
        const t = document.createElement('div');
        t.className = KELAS.toast;
        t.textContent = pesan;
        $('#toasts').appendChild(t);
        setTimeout(() => t.remove(), 3600);
    }
    const panahNaik = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M7 17 17 7M9 7h8v8"/></svg>';
    const panahTurun = '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M7 7l10 10M17 9v8H9"/></svg>';
    const chip = (jenis, isi) => '<span class="' + KELAS.delta + ' ' + KELAS[jenis] + '">' + isi + '</span>';

    // ---------- Status halaman ----------
    const state = { mulai: tambahHari(HARI_INI, -6), akhir: HARI_INI, preset: 7, jenis: 'semua', cari: '', halaman: 1, sorotId: null };

    function labelPeriode(m, a) { return m === a ? tglPanjang(m) : tglPanjang(m) + ' sampai ' + tglPanjang(a); }
    function hitung(mulai, akhir) {
        const rows = data.filter(t => t.tanggal >= mulai && t.tanggal <= akhir);
        let masuk = 0, keluar = 0;
        const kat = {};
        rows.forEach(t => {
            if (t.jenis === 'Penjualan') masuk += t.nominal;
            else { keluar += t.nominal; kat[t.sub] = (kat[t.sub] || 0) + t.nominal; }
        });
        return { rows, masuk, keluar, selisih: masuk - keluar, kat };
    }
    function deltaHtml(kini, lalu, naikBaik) {
        if (!lalu) return chip('flat', 'Tanpa pembanding');
        const p = (kini - lalu) / Math.abs(lalu) * 100;
        if (Math.abs(p) < 0.05) return chip('flat', 'Tetap');
        const baik = (p > 0) === naikBaik;
        const teks = (p > 0 ? '+' : '') + p.toLocaleString('id-ID', { maximumFractionDigits: 1 }) + '%';
        return chip(baik ? 'good' : 'bad', (p > 0 ? panahNaik : panahTurun) + teks);
    }

    // ---------- Ringkasan ----------
    function gambarRingkasan(kini, lalu, panjang) {
        $('#sub-judul').textContent = 'Kondisi kas restoran pada ' + labelPeriode(state.mulai, state.akhir) + '.';
        $('#hero-label').textContent = kini.selisih >= 0 ? 'Selisih bersih (surplus)' : 'Selisih bersih (defisit)';
        const hv = $('#hero-value');
        hv.innerHTML = '<small class="mr-[0.2em] align-[0.9em] text-[0.36em] font-semibold tracking-normal text-[#5a6b66]">' + (kini.selisih < 0 ? '-Rp' : 'Rp') + '</small>' + Math.abs(kini.selisih).toLocaleString('id-ID');
        hv.dataset.minus = String(kini.selisih < 0);

        const pembanding = panjang === 1 ? 'hari sebelumnya' : panjang + ' hari sebelumnya';
        $('#hero-compare').innerHTML = lalu.rows.length
            ? deltaHtml(kini.selisih, lalu.selisih, true) + '<span>dibanding ' + pembanding + ' (' + rp(lalu.selisih) + ')</span>'
            : chip('flat', 'Tanpa pembanding') + '<span>Belum ada data untuk ' + pembanding + '.</span>';

        const total = kini.masuk + kini.keluar;
        const pIn = total ? kini.masuk / total * 100 : 0;
        const pOut = total ? 100 - pIn : 0;
        $('#split-in').style.width = pIn + '%';
        $('#split-out').style.width = pOut + '%';
        $('#split-note-in').textContent = 'Pemasukan ' + Math.round(pIn) + '%';
        $('#split-note-out').textContent = 'Pengeluaran ' + Math.round(pOut) + '%';
        $('#split').setAttribute('aria-label', 'Dari total arus kas, ' + Math.round(pIn) + ' persen pemasukan dan ' + Math.round(pOut) + ' persen pengeluaran');

        $('#m-masuk').textContent = rp(kini.masuk);
        $('#m-masuk-lalu').textContent = 'Sebelumnya ' + rp(lalu.masuk);
        $('#m-masuk-delta').innerHTML = deltaHtml(kini.masuk, lalu.masuk, true);
        $('#m-keluar').textContent = rp(kini.keluar);
        $('#m-keluar-lalu').textContent = 'Sebelumnya ' + rp(lalu.keluar);
        $('#m-keluar-delta').innerHTML = deltaHtml(kini.keluar, lalu.keluar, false);

        const margin = kini.masuk ? Math.round(kini.selisih / kini.masuk * 100) : null;
        const marginLalu = lalu.masuk ? Math.round(lalu.selisih / lalu.masuk * 100) : null;
        $('#m-margin').textContent = margin === null ? 'Belum ada' : margin + '%';
        $('#m-margin-lalu').textContent = 'Sebelumnya ' + (marginLalu === null ? 'belum ada' : marginLalu + '%');
        if (margin === null || marginLalu === null) $('#m-margin-delta').innerHTML = chip('flat', 'Tanpa pembanding');
        else {
            const d = margin - marginLalu;
            $('#m-margin-delta').innerHTML = d === 0 ? chip('flat', 'Tetap') : chip(d > 0 ? 'good' : 'bad', (d > 0 ? '+' : '') + d + ' poin');
        }
    }

    // ---------- Grafik air terjun ----------
    function gambarAirTerjun(kini) {
        const host = $('#air-terjun');
        host.innerHTML = '';
        if (!kini.rows.length) {
            host.innerHTML = '<p class="' + KELAS.kosong + '">Tidak ada transaksi pada periode ini, sehingga grafik kosong.</p>';
            return;
        }
        const items = [{ label: 'Pemasukan', nilai: kini.masuk, jenis: 'awal' }]
            .concat(Object.keys(kini.kat).sort((a, b) => kini.kat[b] - kini.kat[a]).map(k => ({ label: k, nilai: -kini.kat[k], jenis: 'kurang' })))
            .concat([{ label: kini.selisih >= 0 ? 'Selisih' : 'Defisit', nilai: kini.selisih, jenis: 'akhir' }]);
        let level = 0;
        items.forEach(it => {
            if (it.jenis === 'awal') { it.a = 0; it.b = it.nilai; level = it.b; }
            else if (it.jenis === 'kurang') { it.a = level; it.b = level + it.nilai; level = it.b; }
            else { it.a = 0; it.b = it.nilai; }
        });
        const vals = items.flatMap(it => [it.a, it.b]);
        const max = Math.max(0, ...vals), min = Math.min(0, ...vals);
        const Wd = 640, H = 276, top = 34, base = 246;
        const y = v => top + (max - v) / ((max - min) || 1) * (base - top);
        const slot = (Wd - 20) / items.length, bw = Math.min(64, slot * 0.6);
        const svg = el('svg', { viewBox: '0 0 ' + Wd + ' ' + H, class: 'block h-auto max-h-[300px] w-full overflow-visible md:max-h-none', role: 'img', 'aria-label': 'Grafik air terjun dari pemasukan ' + rp(kini.masuk) + ' dikurangi pengeluaran per kategori menjadi selisih ' + rp(kini.selisih) });
        svg.appendChild(el('line', { x1: 0, x2: Wd, y1: y(0), y2: y(0), stroke: W.line }));

        items.forEach((it, i) => {
            const x = 10 + i * slot + (slot - bw) / 2;
            const yTop = y(Math.max(it.a, it.b));
            const h = Math.max(Math.abs(y(it.a) - y(it.b)), 2);
            const fill = it.jenis === 'awal' ? W.up : it.jenis === 'kurang' ? W.down : (it.nilai >= 0 ? W.chrome : W.down);
            svg.appendChild(el('rect', { x: x, y: yTop, width: bw, height: h, rx: 4, fill: fill, class: KELAS.batang, 'data-tip': it.label + ': ' + rp(Math.abs(it.nilai)) }));
            const teks = it.jenis === 'kurang' ? ringkas(it.nilai) : (it.nilai < 0 ? '-Rp ' : 'Rp ') + ringkas(Math.abs(it.nilai));
            svg.appendChild(el('text', { x: x + bw / 2, y: yTop - 9, 'text-anchor': 'middle', 'font-size': 13, 'font-weight': 600, fill: it.jenis === 'kurang' ? W.down : W.ink }, teks));
            svg.appendChild(el('text', { x: x + bw / 2, y: base + 22, 'text-anchor': 'middle', 'font-size': 12.5, fill: W.muted }, it.label));
            if (it.jenis !== 'akhir' && i < items.length - 1) {
                svg.appendChild(el('line', { x1: x + bw, x2: x + slot, y1: y(it.b), y2: y(it.b), stroke: '#9fb0aa', 'stroke-dasharray': '3 3' }));
            }
        });
        host.appendChild(svg);
    }

    // ---------- Grafik selisih berjalan ----------
    function langkahRapi(min, max) {
        const rentang = (max - min) || 1;
        const kasar = rentang / 4;
        const mag = Math.pow(10, Math.floor(Math.log10(kasar)));
        const step = [1, 2, 2.5, 5, 10].map(m => m * mag).find(s => s >= kasar);
        const lo = Math.floor(min / step) * step, hi = Math.ceil(max / step) * step;
        const ticks = [];
        for (let v = lo; v <= hi + step / 2; v += step) ticks.push(Math.round(v));
        return ticks;
    }
    function gambarBerjalan(kini, panjang) {
        const host = $('#berjalan');
        host.innerHTML = '';
        if (!kini.rows.length) {
            host.innerHTML = '<p class="' + KELAS.kosong + '">Belum ada pergerakan kas pada periode ini.</p>';
            return;
        }
        let titik = [], acc = 0;
        if (panjang > 1) {
            $('#sub-berjalan').textContent = 'Akumulasi pemasukan dikurangi pengeluaran per hari.';
            const perHari = {};
            kini.rows.forEach(t => { perHari[t.tanggal] = (perHari[t.tanggal] || 0) + (t.jenis === 'Penjualan' ? t.nominal : -t.nominal); });
            for (let i = 0; i < panjang; i++) {
                const d = tambahHari(state.mulai, i);
                acc += perHari[d] || 0;
                titik.push({ label: String(+d.slice(8, 10)), tip: tglPanjang(d), nilai: acc });
            }
        } else {
            $('#sub-berjalan').textContent = 'Akumulasi pemasukan dikurangi pengeluaran per transaksi hari ini.';
            titik.push({ label: 'Buka', tip: 'Awal hari', nilai: 0 });
            kini.rows.slice().sort((a, b) => a.jam.localeCompare(b.jam)).forEach(t => {
                acc += t.jenis === 'Penjualan' ? t.nominal : -t.nominal;
                titik.push({ label: t.jam, tip: t.jam + ' WIB, ' + t.ket, nilai: acc });
            });
        }
        const vals = titik.map(t => t.nilai);
        const ticks = langkahRapi(Math.min(0, ...vals), Math.max(0, ...vals));
        const lo = ticks[0], hi = ticks[ticks.length - 1];
        const Wd = 400, H = 276, left = 48, right = 384, top = 30, base = 246;
        const x = i => titik.length === 1 ? (left + right) / 2 : left + i * ((right - left) / (titik.length - 1));
        const y = v => base - (v - lo) / ((hi - lo) || 1) * (base - top);
        const svg = el('svg', { viewBox: '0 0 ' + Wd + ' ' + H, class: 'block h-auto max-h-[300px] w-full overflow-visible md:max-h-none', role: 'img', 'aria-label': 'Grafik garis selisih berjalan, berakhir di ' + rp(acc) });
        const defs = el('defs', {});
        const grad = el('linearGradient', { id: 'isi-garis', x1: 0, x2: 0, y1: 0, y2: 1 });
        grad.appendChild(el('stop', { offset: '0%', 'stop-color': W.up, 'stop-opacity': .22 }));
        grad.appendChild(el('stop', { offset: '100%', 'stop-color': W.up, 'stop-opacity': 0 }));
        defs.appendChild(grad);
        svg.appendChild(defs);

        ticks.forEach(v => {
            svg.appendChild(el('line', { x1: left, x2: right, y1: y(v), y2: y(v), stroke: W.line, 'stroke-dasharray': v === 0 ? '' : '2 4' }));
            svg.appendChild(el('text', { x: left - 8, y: y(v) + 4, 'text-anchor': 'end', 'font-size': 12, fill: W.muted }, v === 0 ? '0' : ringkas(v)));
        });
        const pts = titik.map((t, i) => [x(i), y(t.nilai)]);
        const garis = pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' ');
        svg.appendChild(el('path', { d: garis + ' L' + pts[pts.length - 1][0] + ' ' + y(lo) + ' L' + pts[0][0] + ' ' + y(lo) + ' Z', fill: 'url(#isi-garis)' }));
        svg.appendChild(el('path', { d: garis, fill: 'none', stroke: W.up, 'stroke-width': 2.5, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }));

        const jarakLabel = Math.ceil(titik.length / 8);
        titik.forEach((t, i) => {
            const akhir = i === titik.length - 1;
            if (titik.length <= 14 || akhir) {
                svg.appendChild(el('circle', { cx: pts[i][0], cy: pts[i][1], r: akhir ? 6 : 3.5, fill: akhir ? W.kunyit : '#fff', stroke: akhir ? '#fff' : W.up, 'stroke-width': akhir ? 3 : 2 }));
            }
            svg.appendChild(el('circle', { cx: pts[i][0], cy: pts[i][1], r: 10, fill: 'transparent', class: 'cursor-pointer', 'data-tip': t.tip + ': ' + rp(t.nilai) }));
            if (i % jarakLabel === 0 || akhir) {
                svg.appendChild(el('text', { x: pts[i][0], y: base + 22, 'text-anchor': 'middle', 'font-size': 11.5, fill: W.muted }, t.label));
            }
        });
        const ujung = pts[pts.length - 1];
        svg.appendChild(el('text', { x: ujung[0], y: ujung[1] - 14, 'text-anchor': 'end', 'font-size': 13, 'font-weight': 600, fill: W.ink }, (acc < 0 ? '-Rp ' : 'Rp ') + ringkas(Math.abs(acc))));
        host.appendChild(svg);
    }

    // ---------- Tabel riwayat ----------
    function barisTersaring(kini) {
        const q = state.cari.trim().toLowerCase();
        return kini.rows.filter(t =>
            (state.jenis === 'semua' || t.jenis === state.jenis) &&
            (!q || (t.ket + ' ' + t.sub + ' ' + tglPendek(t.tanggal)).toLowerCase().includes(q)));
    }
    function gambarTabel(kini) {
        $('#c-semua').textContent = kini.rows.length;
        $('#c-jual').textContent = kini.rows.filter(t => t.jenis === 'Penjualan').length;
        $('#c-keluar').textContent = kini.rows.filter(t => t.jenis === 'Pengeluaran').length;

        const rows = barisTersaring(kini);
        const jumlahHal = Math.max(1, Math.ceil(rows.length / PER_HALAMAN));
        state.halaman = Math.min(state.halaman, jumlahHal);
        const awal = (state.halaman - 1) * PER_HALAMAN;
        const tampil = rows.slice(awal, awal + PER_HALAMAN);

        if (!rows.length) {
            const pesan = kini.rows.length
                ? 'Tidak ada transaksi yang cocok. Ubah kata kunci atau pilih tab lain.'
                : 'Tidak ada transaksi pada periode ini. Pilih periode lain atau tekan Atur ulang.';
            $('#baris').innerHTML = '<tr class="block md:table-row"><td colspan="5" class="block px-3 py-9 text-center text-[#5a6b66] md:table-cell">' + pesan + '</td></tr>';
        } else {
            $('#baris').innerHTML = tampil.map(t => {
                const masuk = t.jenis === 'Penjualan';
                return '<tr data-id="' + t.id + '" class="' + KELAS.baris + (t.id === state.sorotId ? ' ' + KELAS.sorot : '') + '">' +
                    '<td class="' + KELAS.selWaktu + '"><b class="mr-1.5 font-normal md:mr-0 md:block md:font-medium">' + tglPendek(t.tanggal) + '</b><small class="text-[0.85rem] text-[#5a6b66]">' + t.jam + ' WIB</small></td>' +
                    '<td class="' + KELAS.selKet + '"><b class="font-semibold">' + esc(t.ket) + '</b>' + (t.dikoreksi ? '<span class="' + KELAS.tagKoreksi + '">Dikoreksi</span>' : '') +
                    '<small class="block text-[0.85rem] text-[#5a6b66]">' + (masuk ? 'Dibayar dengan ' + esc(t.sub) : 'Kategori ' + esc(t.sub.toLowerCase())) + '</small></td>' +
                    '<td class="' + KELAS.selJenis + '"><span class="' + (masuk ? KELAS.pillMasuk : KELAS.pillKeluar) + '">' + t.jenis + '</span></td>' +
                    '<td class="' + KELAS.selNominal + ' ' + (masuk ? KELAS.masuk : KELAS.keluar) + '">' + (masuk ? '+ ' : '- ') + rp(t.nominal) + '</td>' +
                    '<td class="' + KELAS.selAksi + '"><a href="' + esc(t.url) + '" class="' + KELAS.tombolKecil + '" aria-label="Koreksi ' + esc(t.ket) + '">Koreksi</a></td>' +
                    '</tr>';
            }).join('');
        }
        if (state.sorotId) {
            const r = document.querySelector('#baris tr[data-id="' + state.sorotId + '"]');
            if (r) setTimeout(() => r.classList.remove(KELAS.sorot), 600);
        }
        state.sorotId = null;
        $('#pager-info').textContent = rows.length
            ? 'Menampilkan ' + (awal + 1) + ' sampai ' + (awal + tampil.length) + ' dari ' + rows.length + ' transaksi'
            : 'Tidak ada transaksi untuk ditampilkan';
        $('#hal-sebelum').disabled = state.halaman <= 1;
        $('#hal-berikut').disabled = state.halaman >= jumlahHal;
        $('#btn-csv').disabled = !rows.length;
    }

    // ---------- Render utama ----------
    function render() {
        const panjang = selisihHari(state.mulai, state.akhir) + 1;
        const kini = hitung(state.mulai, state.akhir);
        const lalu = hitung(tambahHari(state.mulai, -panjang), tambahHari(state.mulai, -1));
        $('#tgl-mulai').value = state.mulai;
        $('#tgl-akhir').value = state.akhir;
        document.querySelectorAll('[data-preset]').forEach(b =>
            b.setAttribute('aria-pressed', String(Number(b.dataset.preset) === state.preset)));
        gambarRingkasan(kini, lalu, panjang);
        gambarAirTerjun(kini);
        gambarBerjalan(kini, panjang);
        gambarTabel(kini);
    }

    // ---------- Filter periode ----------
    function tampilkanErrorFilter(pesan) {
        const e = $('#filter-error');
        e.textContent = pesan || '';
        e.hidden = !pesan;
        $('#range').dataset.invalid = String(!!pesan);
        $('#tgl-mulai').setAttribute('aria-invalid', String(!!pesan));
        $('#tgl-akhir').setAttribute('aria-invalid', String(!!pesan));
    }
    function pakaiPreset(hari) {
        state.preset = hari;
        state.akhir = HARI_INI;
        state.mulai = tambahHari(HARI_INI, -(hari - 1));
        state.halaman = 1;
        tampilkanErrorFilter('');
        render();
    }
    document.querySelectorAll('[data-preset]').forEach(b =>
        b.addEventListener('click', () => pakaiPreset(Number(b.dataset.preset))));

    $('#form-filter').addEventListener('submit', e => {
        e.preventDefault();
        const m = $('#tgl-mulai').value, a = $('#tgl-akhir').value;
        if (!m || !a) return tampilkanErrorFilter('Isi tanggal mulai dan tanggal akhir terlebih dahulu.');
        if (m > a) return tampilkanErrorFilter('Rentang tanggal tidak valid. Tanggal mulai harus sebelum atau sama dengan tanggal akhir.');
        if (a > HARI_INI) return tampilkanErrorFilter('Rentang tanggal tidak valid. Tanggal akhir tidak boleh melewati ' + tglPanjang(HARI_INI) + '.');
        tampilkanErrorFilter('');
        state.mulai = m;
        state.akhir = a;
        const panjang = selisihHari(m, a) + 1;
        state.preset = (a === HARI_INI && [1, 7, 30].includes(panjang)) ? panjang : null;
        state.halaman = 1;
        render();
        const n = hitung(m, a).rows.length;
        toast(n ? 'Filter diterapkan: ' + n + ' transaksi pada ' + labelPeriode(m, a) + '.' : 'Tidak ada transaksi pada periode yang dipilih.');
    });
    $('#btn-reset').addEventListener('click', () => {
        state.cari = '';
        $('#cari').value = '';
        aturTab('semua');
        pakaiPreset(7);
        toast('Filter diatur ulang ke 7 hari terakhir.');
    });

    // ---------- Tab, pencarian, halaman ----------
    function aturTab(jenis) {
        state.jenis = jenis;
        document.querySelectorAll('[data-jenis]').forEach(t => t.setAttribute('aria-selected', String(t.dataset.jenis === jenis)));
    }
    document.querySelectorAll('[data-jenis]').forEach(tab => tab.addEventListener('click', () => {
        aturTab(tab.dataset.jenis);
        state.halaman = 1;
        gambarTabel(hitung(state.mulai, state.akhir));
    }));
    $('#cari').addEventListener('input', e => {
        state.cari = e.target.value;
        state.halaman = 1;
        gambarTabel(hitung(state.mulai, state.akhir));
    });
    $('#hal-sebelum').addEventListener('click', () => { state.halaman--; gambarTabel(hitung(state.mulai, state.akhir)); });
    $('#hal-berikut').addEventListener('click', () => { state.halaman++; gambarTabel(hitung(state.mulai, state.akhir)); });

    // ---------- Unduh CSV ----------
    $('#btn-csv').addEventListener('click', () => {
        const rows = barisTersaring(hitung(state.mulai, state.akhir));
        if (!rows.length) return;
        const kutip = v => '"' + String(v).replace(/"/g, '""') + '"';
        const isi = [['Tanggal', 'Jam', 'Jenis', 'Keterangan', 'Metode atau kategori', 'Nominal']]
            .concat(rows.map(t => [tglPendek(t.tanggal), t.jam, t.jenis, t.ket, t.sub, (t.jenis === 'Penjualan' ? 1 : -1) * t.nominal]))
            .map(r => r.map(kutip).join(';')).join('\r\n');
        const blob = new Blob(['\ufeff' + isi], { type: 'text/csv;charset=utf-8' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'laporan-keuangan_' + state.mulai + '_' + state.akhir + '.csv';
        document.body.appendChild(a);
        a.click();
        a.remove();
        setTimeout(() => URL.revokeObjectURL(a.href), 1000);
        toast('File CSV berisi ' + rows.length + ' transaksi diunduh.');
    });

    $('#crumb-keuangan').addEventListener('click', e => { e.preventDefault(); window.scrollTo({ top: 0 }); });

    // ---------- Tooltip grafik ----------
    const tip = $('#tip');
    document.querySelectorAll('.grafik').forEach(c => {
        c.addEventListener('mousemove', e => {
            const t = e.target.closest('[data-tip]');
            if (!t) { tip.hidden = true; return; }
            tip.textContent = t.getAttribute('data-tip');
            tip.style.left = e.clientX + 'px';
            tip.style.top = e.clientY + 'px';
            tip.hidden = false;
        });
        c.addEventListener('mouseleave', () => { tip.hidden = true; });
    });
    window.addEventListener('scroll', () => { tip.hidden = true; }, { passive: true });

    render();
})();
</script>
@endpush