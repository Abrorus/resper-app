@extends('layouts.app')

@section('title', 'Restaurant Inventory')

@php
    $p = [
        'box' => '<path d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
        'eye' => '<path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'pencil' => '<path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>',
        'plus' => '<path d="M12 4.5v15m7.5-7.5h-15"/>',
        'trash' => '<path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>',
        'search' => '<path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>',
        'x' => '<path d="M6 18L18 6M6 6l12 12"/>',
        'left' => '<path d="M15.75 19.5L8.25 12l7.5-7.5"/>',
        'right' => '<path d="M8.25 4.5l7.5 7.5-7.5 7.5"/>',
    ];
    $icon = fn (string $n, string $c = 'h-5 w-5') => new \Illuminate\Support\HtmlString('<svg class="'.$c.'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" aria-hidden="true">'.$p[$n].'</svg>');

    $forms = ['create' => 'Create Inventory', 'update' => 'Update Inventory'];
    $fields = [
        'name' => ['Name Inventory', 'text'],
        'code' => ['ID', 'text'],
        'total' => ['Total Inventory', 'number'],
        'date' => ['Date', 'date'],
        'manager_name' => ['Name staff / Manager', 'text'],
        'position' => ['Position', 'text'],
    ];
    $openModal = session('modal');
    $openId = session('modal_id');
    $inputCls = 'h-11 w-full rounded-xl border border-gray-200 bg-white px-3.5 text-sm outline-none transition focus:border-[#27AE60] focus:ring-4 focus:ring-[#27AE60]/15';
@endphp

@section('content')
    <div id="inventory-page" data-mode="read" class="group/page">
        {{-- Title + mode tabs --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold sm:text-2xl">Restaurant Inventory</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola data stok bahan restoran.</p>
            </div>
            <div class="flex items-center gap-1 rounded-2xl bg-white p-1 shadow-sm ring-1 ring-gray-100">
                @php $tab = 'mode flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50 data-[active=true]:bg-[#27AE60] data-[active=true]:text-white data-[active=true]:shadow-sm sm:px-4 sm:text-sm'; @endphp
                <button type="button" class="{{ $tab }}" data-mode="read" data-active="true">{{ $icon('eye', 'h-4 w-4') }} Read Inventory</button>
                <button type="button" class="{{ $tab }}" data-mode="update">{{ $icon('pencil', 'h-4 w-4') }} Update</button>
                <button type="button" class="{{ $tab }}" id="btn-add">{{ $icon('plus', 'h-4 w-4') }} Add</button>
                <button type="button" class="{{ $tab }}" data-mode="delete">{{ $icon('trash', 'h-4 w-4') }} Delete</button>
            </div>
        </div>

        @if (session('success'))<div class="mt-5 rounded-xl bg-[#27AE60]/10 px-4 py-3 text-sm text-[#1d6b3e] ring-1 ring-[#27AE60]/20">{{ session('success') }}</div>@endif
        @if (session('error') && ! $openModal)<div class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-100">{{ session('error') }}</div>@endif

        {{-- Toolbar --}}
        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 rounded-2xl bg-white px-4 py-2.5 shadow-sm ring-1 ring-gray-100">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-[#27AE60]/10 text-[#27AE60]">{{ $icon('box', 'h-5 w-5') }}</span>
                <div class="leading-tight">
                    <div class="text-lg font-semibold">{{ $totalInventory }}</div>
                    <div class="text-xs text-gray-500">Total Inventory</div>
                </div>
            </div>

            <form method="GET" action="{{ route('inventory.index') }}" class="relative w-full sm:w-80">
                <input type="text" name="search" value="{{ $search }}" placeholder="Type to search"
                       class="h-11 w-full rounded-2xl border-0 bg-white pl-4 pr-11 text-sm shadow-sm ring-1 ring-gray-100 outline-none transition focus:ring-2 focus:ring-[#27AE60]/40">
                <button type="submit" aria-label="Search" class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-gray-500 transition hover:bg-[#27AE60]/10 hover:text-[#27AE60]">{{ $icon('search', 'h-5 w-5') }}</button>
            </form>

            <span class="text-sm text-gray-500">Showing <b class="text-gray-800">{{ $inventories->count() }}</b> from <b class="text-gray-800">{{ $inventories->total() }}</b> Inventory</span>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead>
                        <tr class="bg-[#27AE60]/5 text-xs font-medium uppercase tracking-wide text-gray-500">
                            <th class="w-14 px-5 py-4 text-left">No</th>
                            <th class="px-3 py-4 text-left">Name</th>
                            <th class="px-3 py-4 text-center">ID</th>
                            <th class="px-3 py-4 text-center">Total Inventory</th>
                            <th class="px-3 py-4 text-center">Last Update</th>
                            <th class="px-3 py-4 text-center">Manage By</th>
                            <th class="w-24 px-3 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse ($inventories as $item)
                        <tr class="transition hover:bg-[#27AE60]/[0.03]">
                            <td class="px-5 py-5 text-gray-500">{{ $inventories->firstItem() + $loop->index }}.</td>
                            <td class="px-3 py-5 font-medium">{{ $item->name }}</td>
                            <td class="px-3 py-5 text-center text-gray-600">{{ $item->code }}</td>
                            <td class="px-3 py-5 text-center"><span class="rounded-full bg-[#27AE60]/10 px-3 py-1 text-xs font-semibold text-[#1d8a4c]">{{ $item->total }}</span></td>
                            <td class="px-3 py-5 text-center text-gray-600">{{ $item->date->format('d/m/Y') }}</td>
                            <td class="px-3 py-5 text-center text-gray-600">{{ $item->manager_name }}</td>
                            <td class="px-3 py-5 text-center">
                                <button type="button" aria-label="Update" data-action="{{ route('inventory.show', $item) }}"
                                    class="icon-edit hidden h-9 w-9 place-items-center rounded-xl text-[#1d8a4c] transition hover:bg-[#27AE60]/10 group-data-[mode=update]/page:inline-grid">{{ $icon('pencil', 'h-5 w-5') }}</button>
                                <button type="button" aria-label="Delete" data-action="{{ route('inventory.destroy', $item) }}" data-name="{{ $item->name }}"
                                    class="icon-del hidden h-9 w-9 place-items-center rounded-xl text-red-500 transition hover:bg-red-50 group-data-[mode=delete]/page:inline-grid">{{ $icon('trash', 'h-5 w-5') }}</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-14 text-center text-gray-400">Belum ada data inventaris.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($inventories->lastPage() > 1)
        <nav class="mt-6 flex items-center justify-end gap-1.5 text-sm text-gray-500" aria-label="Pagination">
            <a href="{{ $inventories->previousPageUrl() }}" aria-label="Previous"
               class="grid h-9 w-9 place-items-center rounded-full transition hover:bg-white {{ $inventories->onFirstPage() ? 'pointer-events-none opacity-30' : '' }}">{{ $icon('left', 'h-4 w-4') }}</a>
            @foreach ($inventories->getUrlRange(1, $inventories->lastPage()) as $page => $url)
                @if ($page == 1 || $page == $inventories->lastPage() || abs($page - $inventories->currentPage()) <= 1)
                    <a href="{{ $url }}" class="grid h-9 w-9 place-items-center rounded-full transition {{ $page == $inventories->currentPage() ? 'bg-[#27AE60] font-semibold text-white shadow-sm' : 'hover:bg-white' }}">{{ $page }}</a>
                @elseif (abs($page - $inventories->currentPage()) == 2)
                    <span class="px-1">&hellip;</span>
                @endif
            @endforeach
            <a href="{{ $inventories->nextPageUrl() }}" aria-label="Next"
               class="grid h-9 w-9 place-items-center rounded-full transition hover:bg-white {{ $inventories->hasMorePages() ? '' : 'pointer-events-none opacity-30' }}">{{ $icon('right', 'h-4 w-4') }}</a>
        </nav>
        @endif
    </div>
@endsection

@push('modals')
    {{-- Create & Update modals --}}
    @foreach ($forms as $key => $title)
    <div id="modal-{{ $key }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/30 p-4 backdrop-blur-sm">
        <form id="form-{{ $key }}" method="POST" action="{{ $key === 'create' ? route('inventory.store') : '#' }}" novalidate
              class="max-h-[95vh] w-full max-w-md overflow-y-auto rounded-3xl bg-white pb-6 shadow-2xl">
            @csrf
            @if ($key === 'update')
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
                <div class="flex gap-2">
                    <button type="button" data-close class="rounded-lg border border-red-300 px-4 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">CANCEL</button>
                    <button type="submit" class="rounded-lg bg-[#27AE60] px-4 py-1.5 text-xs font-medium text-white shadow-sm transition hover:bg-[#1f9a52]">SAVE</button>
                </div>
            </div>
            @if (session('error') && $openModal === $key)<div class="mx-6 mt-4 rounded-xl bg-red-50 px-3 py-2 text-xs text-red-700">{{ session('error') }}</div>@endif
            <div data-client-error hidden class="mx-6 mt-4 rounded-xl bg-red-50 px-3 py-2 text-xs text-red-700"></div>
            <div class="space-y-4 px-6 pt-4">
                @foreach ($fields as $name => $meta)
                    @php [$label, $type] = $meta; @endphp
                    <div>
                        <label for="{{ $key }}-{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $label }}</label>
                        <input id="{{ $key }}-{{ $name }}" type="{{ $type }}" name="{{ $name }}" @if ($type === 'number') min="0" @endif
                               value="{{ $openModal === $key ? old($name) : '' }}" class="{{ $inputCls }}">
                        @error($name, $key)<div class="field-error mt-1 text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </div>
        </form>
    </div>
    @endforeach

    {{-- Delete confirmation --}}
    <div id="modal-delete" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/30 p-4 backdrop-blur-sm">
        <form id="form-delete" method="POST" class="relative w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl">
            @csrf @method('DELETE')
            <input type="hidden" name="page" value="{{ request('page') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="button" data-close aria-label="Tutup" class="absolute right-4 top-4 grid h-8 w-8 place-items-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">{{ $icon('x', 'h-5 w-5') }}</button>
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-red-50 text-red-500">{{ $icon('trash', 'h-7 w-7') }}</span>
            <h3 class="mt-4 text-lg font-semibold">Konfirmasi Hapus</h3>
            <p class="mt-2 text-sm text-gray-500">Yakin ingin menghapus <strong id="delete-name" class="text-gray-800"></strong>?</p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" data-close class="rounded-lg border border-gray-300 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-50">CANCEL</button>
                <button type="submit" class="rounded-lg bg-red-500 px-5 py-2 text-xs font-medium text-white shadow-sm transition hover:bg-red-600">YES, DELETE</button>
            </div>
        </form>
    </div>
@endpush

@push('scripts')
<script>
    const $ = (s, r = document) => r.querySelector(s);
    const pageRoot = $('#inventory-page');
    const open = id => { const m = $('#modal-' + id); m.classList.remove('hidden'); m.classList.add('flex'); };
    const closeAll = () => document.querySelectorAll('[id^="modal-"]').forEach(o => { o.classList.add('hidden'); o.classList.remove('flex'); });

    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', closeAll));
    document.querySelectorAll('[id^="modal-"]').forEach(o => o.addEventListener('click', e => { if (e.target === o) closeAll(); }));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

    // Mode switching: Read / Update / Delete
    document.querySelectorAll('.mode[data-mode]').forEach(btn => btn.addEventListener('click', () => {
        pageRoot.dataset.mode = btn.dataset.mode;
        document.querySelectorAll('.mode').forEach(m => m.dataset.active = (m === btn) ? 'true' : 'false');
    }));

    // Add: tampilkan pop-up Create Inventory
    $('#btn-add').addEventListener('click', () => { resetForm($('#form-create')); open('create'); });

    // validateInput(): validasi kelengkapan & format sebelum Save (self-message pada pop-up)
    function validateInput(form) {
        const box = $('[data-client-error]', form);
        const labels = { name: 'Name Inventory', code: 'ID', total: 'Total Inventory', date: 'Date', manager_name: 'Name staff / Manager', position: 'Position' };
        const problems = [];
        for (const [name, label] of Object.entries(labels)) {
            const v = form.elements[name].value.trim();
            if (v === '') problems.push(`${label} wajib diisi.`);
            else if (name === 'total' && !/^\d+$/.test(v)) problems.push(`${label} harus berupa angka bulat >= 0.`);
        }
        box.hidden = problems.length === 0;
        box.textContent = problems.join(' ');
        return problems.length === 0;
    }
    function resetForm(form) { form.reset(); $('[data-client-error]', form).hidden = true; form.querySelectorAll('.field-error').forEach(e => e.remove()); }
    ['create', 'update'].forEach(k => $('#form-' + k).addEventListener('submit', e => { if (!validateInput(e.target)) e.preventDefault(); }));

    // Update: ambil data stok terbaru dari database, lalu tampilkan pop-up yang sudah terisi
    document.querySelectorAll('.icon-edit').forEach(btn => btn.addEventListener('click', async () => {
        const form = $('#form-update');
        try {
            const res = await fetch(btn.dataset.action, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error();
            const data = await res.json();
            resetForm(form);
            form.action = btn.dataset.action;
            Object.entries(data).forEach(([k, v]) => { const el = form.elements[k]; if (el) el.value = v; });
            open('update');
        } catch {
            alert('Gagal mengambil data inventaris. Silakan coba lagi.');
        }
    }));

    // Delete: tampilkan konfirmasi hapus
    document.querySelectorAll('.icon-del').forEach(btn => btn.addEventListener('click', () => {
        $('#form-delete').action = btn.dataset.action;
        $('#delete-name').textContent = btn.dataset.name;
        open('delete');
    }));

    // Buka kembali pop-up setelah validasi / penyimpanan gagal
    @if ($openModal === 'create')
        open('create');
    @elseif ($openModal === 'update' && $openId)
        $('#form-update').action = "{{ route('inventory.update', $openId) }}";
        open('update');
    @elseif ($errors->create->any())
        open('create');
    @elseif ($errors->update->any())
        open('update');
    @endif
</script>
@endpush
