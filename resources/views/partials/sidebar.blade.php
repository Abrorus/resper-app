@php
    use Illuminate\Support\Facades\Route;

    $menu = [
        ['label' => 'Akun', 'route' => 'akun.index', 'pola' => 'akun.*',
         'ikon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>'],
        ['label' => 'Pemesanan', 'route' => 'order.pesanan', 'pola' => 'order.*',
         'ikon' => '<path d="M6 3h12v18l-3-2-3 2-3-2-3 2Z"/><path d="M9 8h6M9 12h6"/>',
         'sub' => [
             ['label' => 'Pesanan', 'route' => 'order.pesanan'],
             ['label' => 'Delivery Order', 'route' => 'order.delivery'],
         ]],
        ['label' => 'Inventaris', 'route' => 'inventory.index', 'pola' => 'inventory.*',
         'ikon' => '<path d="M12 3 3 7.5l9 4.5 9-4.5Z"/><path d="M3 7.5V17l9 4.5 9-4.5V7.5M12 12v9.5"/>'],
        ['label' => 'Staff', 'route' => 'staff.index', 'pola' => 'staff.*',
         'ikon' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
         'sub' => [
             ['label' => 'Data Staff', 'route' => 'staff.index'],
             ['label' => 'Jadwal Staff', 'route' => 'staff.schedule'],
         ]],
        ['label' => 'Keuangan', 'route' => 'finance.report', 'pola' => 'finance.*',
         'ikon' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h3"/>',
         'sub' => [
             ['label' => 'Transaksi Penjualan', 'route' => 'finance.report', 'aksi' => 'penjualan'],
             ['label' => 'Transaksi Pengeluaran', 'route' => 'finance.report', 'aksi' => 'pengeluaran'],
             ['label' => 'Laporan Keuangan', 'route' => 'finance.report'],
             ['label' => 'Koreksi Transaksi', 'route' => 'finance.report', 'aksi' => 'koreksi'],
         ]],
    ];

    $tautan = fn ($nama, $aksi = null) => Route::has($nama) ? route($nama) . ($aksi ? '#' . $aksi : '') : '#';
    $namaUser = auth()->user()?->name ?? 'Manajer Restoran';
@endphp

<aside class="flex min-w-0 items-center gap-4 overflow-x-auto bg-[#0b3d31] px-4 py-3 text-[#b5ccc4] md:sticky md:top-0 md:h-screen md:flex-col md:items-stretch md:gap-7 md:px-4 md:py-6" aria-label="Navigasi">
    <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2 px-2 text-white">
        <svg class="h-7 w-7 text-[#27AE60]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 5 4 12l7 7"/><path d="M20 5l-7 7 7 7"/></svg>
        <span class="text-xl font-bold tracking-wide">RESPER</span>
    </a>

    <nav class="flex gap-0.5 md:flex-col">
        @foreach ($menu as $item)
            @php
                $aktif = request()->routeIs($item['pola']);
                $tersedia = Route::has($item['route']);
            @endphp
            <a href="{{ $tautan($item['route']) }}"
               @unless ($tersedia) aria-disabled="true" title="Modul belum digabung ke main" @endunless
               class="flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-[#12503f] hover:text-white aria-disabled:pointer-events-none aria-disabled:opacity-45 {{ $aktif ? 'font-semibold text-white' : '' }}">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['ikon'] !!}</svg>{{ $item['label'] }}
            </a>

            @if ($aktif && ! empty($item['sub']))
                <div class="mb-1.5 ml-5 mt-1 hidden flex-col border-l border-white/15 pl-5 md:flex">
                    @foreach ($item['sub'] as $sub)
                        <a href="{{ $tautan($sub['route'], $sub['aksi'] ?? null) }}"
                           @isset($sub['aksi']) data-aksi="{{ $sub['aksi'] }}" @endisset
                           @if (! isset($sub['aksi']) && request()->routeIs($sub['route'])) aria-current="page" @endif
                           class="rounded-lg px-2.5 py-2 text-[0.92rem] hover:bg-[#12503f] hover:text-white aria-[current=page]:bg-[#e6a417]/15 aria-[current=page]:font-semibold aria-[current=page]:text-white aria-[current=page]:shadow-[inset_3px_0_0_#e6a417]">{{ $sub['label'] }}</a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>

    <div class="mt-auto hidden items-center gap-3 rounded-xl bg-white/5 p-3 md:flex">
    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-[#e9efe9] font-bold text-[#0b3d31]" aria-hidden="true">{{ mb_strtoupper(mb_substr($namaUser, 0, 1)) }}</div>
    <div class="min-w-0 flex-1">
        <b class="block truncate font-semibold text-white">{{ $namaUser }}</b>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="cursor-pointer text-[0.82rem] hover:text-white hover:underline">Keluar</button>
        </form>
    </div>
</div>
</aside>