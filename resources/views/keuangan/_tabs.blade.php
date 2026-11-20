{{-- Navigasi antarfitur Keuangan untuk layar kecil (di layar lebar sudah ada di sidebar) --}}
<nav class="-mx-4 mb-5 flex gap-1 overflow-x-auto px-4 md:hidden" aria-label="Menu Keuangan">
    @foreach ([['finance.sales.index', 'finance.sales.*', 'Penjualan', 'catat-transaksi'], ['finance.expenses.index', 'finance.expenses.*', 'Pengeluaran', 'catat-transaksi'], ['finance.report', 'finance.report', 'Laporan', 'lihat-laporan-keuangan'], ['finance.corrections.index', 'finance.corrections.*', 'Koreksi', 'koreksi-transaksi']] as [$rute, $pola, $label, $izin])
        @continue(! auth()->user()?->can($izin))
        <a href="{{ route($rute) }}" @if (request()->routeIs($pola)) aria-current="page" @endif
           class="shrink-0 rounded-full border border-[#dce3e0] bg-white px-3.5 py-1.5 text-[0.9rem] text-[#5a6b66] aria-[current=page]:border-[#0b3d31] aria-[current=page]:bg-[#0b3d31] aria-[current=page]:font-semibold aria-[current=page]:text-white">{{ $label }}</a>
    @endforeach
</nav>
