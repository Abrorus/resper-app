<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Inventory - RESPER</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700" rel="stylesheet">
    <style>
        :root { --green: #1fa95b; --green-dark: #168a49; --red: #d61f2c; --line: #d9d9d9; --text: #1b1b1b; --muted: #6b7280; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Poppins', sans-serif; color: var(--text); background: #fff; }
        a { color: inherit; text-decoration: none; }
        button { font-family: inherit; cursor: pointer; }

        .topbar { height: 56px; display: flex; align-items: center; justify-content: center; position: relative; box-shadow: 0 1px 4px rgba(0,0,0,.25); }
        .brand { font-weight: 700; font-size: 22px; letter-spacing: .5px; display: flex; align-items: center; gap: 8px; }
        .brand-mark { width: 28px; height: 28px; border-radius: 8px; background: var(--green); display: inline-block; }
        .user { position: absolute; right: 24px; display: flex; align-items: center; gap: 10px; font-size: 11px; font-weight: 600; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: #dff5e8; border: 1px solid #b6e6c9; display: grid; place-items: center; color: var(--green); }

        .layout { display: flex; min-height: calc(100vh - 56px); }
        .sidebar { width: 175px; border-right: 3px solid #e1e1e1; padding: 28px 8px; flex-shrink: 0; }
        .sidebar h2 { margin: 0 0 40px 10px; font-size: 16px; font-weight: 700; }
        .nav a { display: block; padding: 10px 14px; margin-bottom: 8px; border-radius: 10px; font-size: 13px; color: #444; }
        .nav a.active { background: #e8e8e8; font-weight: 500; }

        .main { flex: 1; min-width: 0; padding: clamp(16px, 2.5vw, 40px) clamp(16px, 4vw, 64px); }
        .table-wrap { overflow-x: auto; }
        .brand img { height: 30px; width: auto; display: block; }
        .modal-error { margin: 14px 20px 0; padding: 8px 12px; border-radius: 8px; background: #fde7e9; color: #9b1420; font-size: 12px; }
        .modal-x { background: none; border: none; font-size: 20px; line-height: 1; color: #555; position: absolute; top: 14px; right: 18px; }
        .modal.sm { position: relative; }
        @media (max-width: 760px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; border-right: none; border-bottom: 3px solid #e1e1e1; padding: 12px; }
            .sidebar h2 { margin-bottom: 10px; }
            .nav { display: flex; flex-wrap: wrap; gap: 6px; }
            .nav a { margin: 0; }
            .user span:not(.avatar) { display: none; }
        }
        .head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .head h1 { margin: 0; font-size: 17px; font-weight: 600; }
        .modes { display: flex; gap: 22px; }
        .mode { background: none; border: none; padding: 2px 0; font-size: 12px; color: #444; border-bottom: 1px solid transparent; }
        .mode.active { border-bottom-color: #444; }

        .toolbar { display: flex; align-items: center; justify-content: space-between; margin: 34px 0 40px; font-size: 12px; gap: 16px; flex-wrap: wrap; }
        .search { display: flex; border: 1px solid #aaa; border-radius: 6px; padding: 4px 10px; width: 270px; }
        .search input { border: none; outline: none; flex: 1; font-size: 11px; font-family: inherit; }
        .search button { border: none; background: none; padding: 0; color: #444; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { font-weight: 400; padding: 12px 6px; text-align: center; }
        td { padding: 26px 6px; text-align: center; border-top: 1px solid #ccc; }
        th:first-child, td:first-child { text-align: left; width: 40px; }
        .col-action { width: 44px; border: none !important; padding-left: 0; padding-right: 0; }
        td.col-action { border-top: none; }
        .empty { padding: 40px; color: var(--muted); }

        .icon-btn { background: none; border: none; padding: 4px; display: none; }
        .icon-btn.del { color: var(--red); }
        .icon-btn.edit { color: var(--green-dark); }
        body[data-mode="delete"] .icon-btn.del, body[data-mode="update"] .icon-btn.edit { display: inline-block; }
        body[data-mode="read"] .col-action { visibility: hidden; }

        .pager { display: flex; justify-content: flex-end; align-items: center; gap: 18px; margin-top: 28px; font-size: 13px; color: var(--muted); }
        .pager .page { width: 24px; height: 24px; border-radius: 50%; display: grid; place-items: center; }
        .pager .page.current { background: var(--green); color: #fff; }
        .pager .disabled { opacity: .35; pointer-events: none; }

        .flash { padding: 10px 14px; border-radius: 8px; font-size: 12px; margin-top: 16px; }
        .flash.success { background: #e3f6ea; color: #14683a; }
        .flash.error { background: #fde7e9; color: #9b1420; }

        .overlay { position: fixed; inset: 0; background: rgba(120,120,120,.55); backdrop-filter: blur(3px); display: none; align-items: center; justify-content: center; z-index: 10; padding: 16px; }
        .overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 28px; width: 100%; max-width: 460px; max-height: 95vh; overflow-y: auto; padding-bottom: 26px; }
        .modal.sm { max-width: 380px; padding: 28px; text-align: center; }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 22px 20px 12px; border-bottom: 1px solid #999; }
        .modal-head h3 { margin: 0; font-weight: 500; font-size: 18px; }
        .btns { display: flex; gap: 10px; }
        .btn { border-radius: 4px; font-size: 11px; padding: 6px 14px; border: 1px solid transparent; }
        .btn.cancel { background: #fff; color: var(--red); border-color: var(--red); }
        .btn.save { background: var(--green); color: #fff; }
        .btn.danger { background: var(--red); color: #fff; }
        .form { padding: 0 20px; }
        .form label { display: block; margin: 16px 0 6px; font-size: 13px; }
        .form input { width: 100%; border: 1px solid #bdbdbd; border-radius: 10px; height: 38px; padding: 0 12px; font-family: inherit; font-size: 13px; }
        .form input:focus { outline: 2px solid #b6e6c9; border-color: var(--green); }
        .field-error { color: var(--red); font-size: 11px; margin-top: 4px; }
        .modal.sm p { font-size: 13px; margin: 10px 0 22px; }
        .modal.sm .btns { justify-content: center; }
    </style>
</head>
<body data-mode="read">
@php
    $icons = [
        'trash' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M9 3h6l1 2h4v2H4V5h4l1-2zm-3 6h12l-1 12H7L6 9zm4 2v8h1.5v-8H10zm3.5 0v8H15v-8h-1.5z"/></svg>',
        'edit' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.8 9.94l-3.75-3.75L3 17.25zM20.7 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>',
    ];
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
@endphp

<header class="topbar">
    <div class="brand"><img src="{{ asset('images/resper-logo.png') }}" alt="RESPER logo"> RESPER</div>
    <div class="user">Nama User <span class="avatar">&#9786;</span></div>
</header>

<div class="layout">
    <aside class="sidebar">
        <h2>DASHBOARD</h2>
        <nav class="nav">
            <a href="#">Homepage</a>
            <a href="{{ route('inventory.index') }}" class="active">Inventory</a>
            <a href="#">Sales</a>
            <a href="#">Staff</a>
            <a href="#">Order</a>
            <a href="#">Schedule</a>
        </nav>
    </aside>

    <main class="main">
        <div class="head">
            <h1>Restaurant Inventory</h1>
            <div class="modes">
                <button type="button" class="mode active" data-mode="read">&#128065; Read Inventory</button>
                <button type="button" class="mode" data-mode="update">&#9998; Update</button>
                <button type="button" class="mode" id="btn-add">&#10010; Add</button>
                <button type="button" class="mode" data-mode="delete">&#128465; Delete</button>
            </div>
        </div>

        @if (session('success'))<div class="flash success">{{ session('success') }}</div>@endif
        @if (session('error') && ! $openModal)<div class="flash error">{{ session('error') }}</div>@endif

        <div class="toolbar">
            <span>{{ $totalInventory }} Total Inventory</span>
            <form method="GET" action="{{ route('inventory.index') }}" class="search">
                <input type="text" name="search" value="{{ $search }}" placeholder="Type to search">
                <button type="submit" aria-label="Search">&#128269;</button>
            </form>
            <span>Showing {{ $inventories->count() }} from {{ $inventories->total() }} Inventory</span>
        </div>

        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Name</th><th>ID</th><th>Total Inventory</th><th>Last Update</th><th>Manage By</th><th class="col-action"></th>
                </tr>
            </thead>
            <tbody>
            @forelse ($inventories as $item)
                <tr>
                    <td>{{ $inventories->firstItem() + $loop->index }}.</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $item->date->format('d/m/Y') }}</td>
                    <td>{{ $item->manager_name }}</td>
                    <td class="col-action">
                        <button type="button" class="icon-btn edit" aria-label="Update"
                            data-action="{{ route('inventory.update', $item) }}">{!! $icons['edit'] !!}</button>
                        <button type="button" class="icon-btn del" aria-label="Delete"
                            data-action="{{ route('inventory.destroy', $item) }}"
                            data-name="{{ $item->name }}">{!! $icons['trash'] !!}</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada data inventaris.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>

        @if ($inventories->lastPage() > 1)
        <nav class="pager">
            <a href="{{ $inventories->previousPageUrl() }}" class="{{ $inventories->onFirstPage() ? 'disabled' : '' }}">&lsaquo;</a>
            @foreach ($inventories->getUrlRange(1, $inventories->lastPage()) as $page => $url)
                @if ($page == 1 || $page == $inventories->lastPage() || abs($page - $inventories->currentPage()) <= 1)
                    <a href="{{ $url }}" class="page {{ $page == $inventories->currentPage() ? 'current' : '' }}">{{ $page }}</a>
                @elseif (abs($page - $inventories->currentPage()) == 2)
                    <span>&hellip;</span>
                @endif
            @endforeach
            <a href="{{ $inventories->nextPageUrl() }}" class="{{ $inventories->hasMorePages() ? '' : 'disabled' }}">&rsaquo;</a>
        </nav>
        @endif
    </main>
</div>

{{-- Create & Update modal --}}
@foreach ($forms as $key => $title)
<div class="overlay" id="modal-{{ $key }}">
    <form class="modal" method="POST" action="{{ $key === 'create' ? route('inventory.store') : '#' }}" id="form-{{ $key }}" novalidate>
        @csrf
        @if ($key === 'update') @method('PUT') <input type="hidden" name="page" value="{{ request('page') }}"><input type="hidden" name="search" value="{{ request('search') }}"> @endif
        <div class="modal-head">
            <h3>{{ $title }}</h3>
            <div class="btns">
                <button type="button" class="btn cancel" data-close>CANCEL</button>
                <button type="submit" class="btn save">SAVE</button>
            </div>
        </div>
        @if (session('error') && $openModal === $key)<div class="modal-error">{{ session('error') }}</div>@endif
        <div class="modal-error" data-client-error hidden></div>
        <div class="form">
            @foreach ($fields as $name => $meta)
                @php [$label, $type] = $meta; @endphp
                <label for="{{ $key }}-{{ $name }}">{{ $label }}</label>
                <input id="{{ $key }}-{{ $name }}" type="{{ $type }}" name="{{ $name }}" @if ($type === 'number') min="0" @endif
                    value="{{ $openModal === $key ? old($name) : '' }}" required>
                @error($name, $key)<div class="field-error">{{ $message }}</div>@enderror
            @endforeach
        </div>
    </form>
</div>
@endforeach

{{-- Delete confirmation --}}
<div class="overlay" id="modal-delete">
    <form class="modal sm" method="POST" id="form-delete">
        @csrf @method('DELETE')
        <input type="hidden" name="page" value="{{ request('page') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <button type="button" class="modal-x" data-close aria-label="Tutup">&times;</button>
        <h3 style="margin:0">Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus <strong id="delete-name"></strong>?</p>
        <div class="btns">
            <button type="button" class="btn cancel" data-close>CANCEL</button>
            <button type="submit" class="btn danger">YES, DELETE</button>
        </div>
    </form>
</div>

<script>
    const $ = (s, r = document) => r.querySelector(s);
    const open = id => $('#modal-' + id).classList.add('open');
    const closeAll = () => document.querySelectorAll('.overlay').forEach(o => o.classList.remove('open'));

    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', closeAll));
    document.querySelectorAll('.overlay').forEach(o => o.addEventListener('click', e => { if (e.target === o) closeAll(); }));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

    // Mode switching: Read / Update / Delete
    document.querySelectorAll('.mode[data-mode]').forEach(btn => btn.addEventListener('click', () => {
        document.body.dataset.mode = btn.dataset.mode;
        document.querySelectorAll('.mode').forEach(m => m.classList.toggle('active', m === btn));
    }));

    // Add: tampilkan pop-up Create Inventory
    $('#btn-add').addEventListener('click', () => { resetForm($('#form-create')); open('create'); });

    // validateInput(): validasi kelengkapan & format sebelum Save (self-message pada pop-up)
    function validateInput(form) {
        const box = $('[data-client-error]', form);
        const labels = { name: 'Name Inventory', code: 'ID', total: 'Total Inventory', date: 'Date', manager_name: 'Name staff / Manager', position: 'Position' };
        const problems = [];
        for (const [name, label] of Object.entries(labels)) {
            const el = form.elements[name];
            const v = el.value.trim();
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
    document.querySelectorAll('.icon-btn.edit').forEach(btn => btn.addEventListener('click', async () => {
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

    // Delete
    document.querySelectorAll('.icon-btn.del').forEach(btn => btn.addEventListener('click', () => {
        $('#form-delete').action = btn.dataset.action;
        $('#delete-name').textContent = btn.dataset.name;
        open('delete');
    }));

    // Re-open modal after a failed validation / save
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
</body>
</html>
