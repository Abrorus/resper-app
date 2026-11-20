@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@php
    $p = [
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.5a3.5 3.5 0 0 1 0 7M18 14a6.5 6.5 0 0 1 3.5 6"/>',
        'pencil' => '<path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>',
        'plus' => '<path d="M12 4.5v15m7.5-7.5h-15"/>',
        'trash' => '<path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>',
        'search' => '<path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>',
        'x' => '<path d="M6 18L18 6M6 6l12 12"/>',
    ];
    $icon = fn (string $n, string $c = 'h-5 w-5') => new \Illuminate\Support\HtmlString('<svg class="'.$c.'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$p[$n].'</svg>');

    $forms = ['create' => 'Tambah Pengguna', 'update' => 'Ubah Pengguna'];
    $textFields = [
        'name' => ['Nama', 'text'],
        'email' => ['Email', 'email'],
        'phone' => ['Nomor Telepon', 'tel'],
    ];
    $inputCls = 'h-11 w-full rounded-xl border border-[#dce3e0] bg-white px-3.5 text-sm outline-none transition focus:border-[#27AE60] focus:ring-4 focus:ring-[#27AE60]/15';
    $badgeRole = fn ($role) => match ($role) {
        \App\Enums\UserRole::Manager => 'bg-[#e6a417]/15 text-[#8a5a00]',
        \App\Enums\UserRole::Cashier => 'bg-[#27AE60]/15 text-[#1d6b3e]',
        default => 'bg-[#dce3e0] text-[#13211e]',
    };
    // Modal yang harus dibuka kembali setelah validasi gagal
    $reopen = $errors->create->any() ? 'create' : ($errors->update->any() ? 'update' : null);
@endphp

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]">Akun / Kelola Pengguna</p>
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Kelola Pengguna</h1>
            <p class="mt-1.5 text-[#5a6b66]">Tambah, ubah, dan hapus akun pengguna beserta role-nya.</p>
        </div>
        <button type="button" id="btn-add" class="flex cursor-pointer items-center gap-2 rounded-full bg-[#27AE60] px-5 py-2.5 font-semibold text-white shadow-sm transition hover:bg-[#229954]">
            {{ $icon('plus', 'h-4 w-4') }} Tambah Pengguna
        </button>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#27AE60]/30 bg-[#27AE60]/10 px-4 py-3 text-[#0b3d31]">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mt-5 rounded-xl border border-[#c2362f]/30 bg-[#f9e5e3] px-4 py-3 text-[#c2362f]">{{ session('error') }}</div>
    @endif

    <section class="mt-6 rounded-2xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6">
        {{-- Toolbar: total, pencarian, filter role --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#27AE60]/10 text-[#27AE60]">{{ $icon('users') }}</span>
                <div class="leading-tight">
                    <div class="text-lg font-semibold">{{ $totalUsers }}</div>
                    <div class="text-xs text-[#5a6b66]">Total Pengguna</div>
                </div>
            </div>
            <form method="GET" action="{{ route('akun.users.index') }}" class="flex flex-wrap gap-2">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[#5a6b66]">{{ $icon('search', 'h-4 w-4') }}</span>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama, email, telepon"
                           class="h-10 w-64 rounded-lg border border-[#dce3e0] bg-white pl-9 pr-3 text-sm outline-none focus:border-[#27AE60]">
                </div>
                <select name="role" class="h-10 rounded-lg border border-[#dce3e0] bg-white px-3 text-sm">
                    <option value="">Semua role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->value }}" @selected($filter === $role)>{{ $role->label() }}</option>
                    @endforeach
                </select>
                <button type="submit" class="h-10 cursor-pointer rounded-lg border border-[#dce3e0] bg-white px-3.5 text-sm hover:border-[#13211e]">Terapkan</button>
                @if ($search !== '' || $filter)
                    <a href="{{ route('akun.users.index') }}" class="flex h-10 items-center rounded-lg px-3 text-sm text-[#5a6b66] hover:text-[#13211e]">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-[#dce3e0] text-[0.9rem] text-[#5a6b66]">
                    <tr>
                        <th class="py-2.5 pr-4 font-medium">Nama</th>
                        <th class="py-2.5 pr-4 font-medium">Email</th>
                        <th class="py-2.5 pr-4 font-medium">Telepon</th>
                        <th class="py-2.5 pr-4 font-medium">Role</th>
                        <th class="py-2.5 pr-4 font-medium">Status</th>
                        <th class="py-2.5 pr-4 font-medium">Gagal Login</th>
                        <th class="py-2.5 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-[#dce3e0] last:border-0">
                            <td class="py-3 pr-4 font-medium">
                                {{ $user->name }}
                                @if ($user->is(auth()->user()))<span class="ml-1 text-xs text-[#5a6b66]">(Anda)</span>@endif
                            </td>
                            <td class="py-3 pr-4 text-[#5a6b66]">{{ $user->email }}</td>
                            <td class="py-3 pr-4 text-[#5a6b66]">{{ $user->phone ?? '-' }}</td>
                            <td class="py-3 pr-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeRole($user->role) }}">{{ $user->role->label() }}</span></td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->isActive() ? 'bg-[#27AE60]/15 text-[#1d6b3e]' : 'bg-[#f9e5e3] text-[#c2362f]' }}">{{ $user->account_status->label() }}</span>
                            </td>
                            <td class="py-3 pr-4 tabular-nums">{{ $user->failed_login_attempts }}</td>
                            <td class="py-3">
                                <div class="flex justify-end gap-1">
                                    <button type="button" class="icon-edit grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#5a6b66] transition hover:bg-[#27AE60]/10 hover:text-[#27AE60]" aria-label="Ubah {{ $user->name }}"
                                            data-action="{{ route('akun.users.update', $user) }}"
                                            data-user="{{ json_encode(['user_id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'role' => $user->role->value, 'account_status' => $user->account_status->value]) }}">
                                        {{ $icon('pencil', 'h-4.5 w-4.5') }}
                                    </button>
                                    @unless ($user->is(auth()->user()))
                                        <button type="button" class="icon-del grid h-9 w-9 cursor-pointer place-items-center rounded-lg text-[#5a6b66] transition hover:bg-[#f9e5e3] hover:text-[#c2362f]" aria-label="Hapus {{ $user->name }}"
                                                data-action="{{ route('akun.users.destroy', $user) }}" data-name="{{ $user->name }}">
                                            {{ $icon('trash', 'h-4.5 w-4.5') }}
                                        </button>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-[#5a6b66]">Tidak ada pengguna yang cocok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </section>
@endsection

@push('modals')
    @foreach ($forms as $key => $title)
    <div id="modal-{{ $key }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/30 p-4 backdrop-blur-sm">
        <form id="form-{{ $key }}" method="POST" action="{{ $key === 'create' ? route('akun.users.store') : '#' }}" novalidate
              class="max-h-[95vh] w-full max-w-md overflow-y-auto rounded-3xl bg-white pb-6 shadow-2xl">
            @csrf
            @if ($key === 'update')
                @method('PUT')
                <input type="hidden" name="user_id" value="{{ $reopen === 'update' ? old('user_id') : '' }}">
                <input type="hidden" name="page" value="{{ request('page') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <div class="flex items-center justify-between border-b border-[#dce3e0] px-6 py-5">
                <h3 class="text-lg font-semibold">{{ $title }}</h3>
                <button type="button" data-close aria-label="Tutup" class="grid h-8 w-8 cursor-pointer place-items-center rounded-full text-[#5a6b66] transition hover:bg-[#f1f4f2]">{{ $icon('x', 'h-5 w-5') }}</button>
            </div>
            <div class="space-y-4 px-6 pt-4">
                @foreach ($textFields as $name => [$label, $type])
                    <div>
                        <label for="{{ $key }}-{{ $name }}" class="mb-1.5 block text-sm font-medium">{{ $label }} @if ($name !== 'phone')<span class="text-red-500">*</span>@endif</label>
                        <input id="{{ $key }}-{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $reopen === $key ? old($name) : '' }}" class="{{ $inputCls }}">
                        @error($name, $key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="{{ $key }}-role" class="mb-1.5 block text-sm font-medium">Role <span class="text-red-500">*</span></label>
                        <select id="{{ $key }}-role" name="role" class="{{ $inputCls }}">
                            @foreach ($roles as $role)
                                <option value="{{ $role->value }}" @selected($reopen === $key && old('role') === $role->value)>{{ $role->label() }}</option>
                            @endforeach
                        </select>
                        @error('role', $key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="{{ $key }}-account_status" class="mb-1.5 block text-sm font-medium">Status <span class="text-red-500">*</span></label>
                        <select id="{{ $key }}-account_status" name="account_status" class="{{ $inputCls }}">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($reopen === $key && old('account_status') === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        @error('account_status', $key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="{{ $key }}-password" class="mb-1.5 block text-sm font-medium">
                        Password @if ($key === 'create')<span class="text-red-500">*</span>@else<span class="text-xs font-normal text-[#5a6b66]">(kosongkan jika tidak diubah)</span>@endif
                    </label>
                    <input id="{{ $key }}-password" type="password" name="password" autocomplete="new-password" class="{{ $inputCls }}">
                    @error('password', $key)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="{{ $key }}-password_confirmation" class="mb-1.5 block text-sm font-medium">Konfirmasi Password</label>
                    <input id="{{ $key }}-password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="{{ $inputCls }}">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2 px-6">
                <button type="button" data-close class="cursor-pointer rounded-full border border-[#dce3e0] px-5 py-2 text-sm font-medium text-[#5a6b66] transition hover:border-[#13211e] hover:text-[#13211e]">Batal</button>
                <button type="submit" class="cursor-pointer rounded-full bg-[#27AE60] px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#229954]">Simpan</button>
            </div>
        </form>
    </div>
    @endforeach

    {{-- Konfirmasi hapus --}}
    <div id="modal-delete" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/30 p-4 backdrop-blur-sm">
        <form id="form-delete" method="POST" class="relative w-full max-w-sm rounded-3xl bg-white p-8 text-center shadow-2xl">
            @csrf @method('DELETE')
            <input type="hidden" name="page" value="{{ request('page') }}">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <button type="button" data-close aria-label="Tutup" class="absolute right-4 top-4 grid h-8 w-8 cursor-pointer place-items-center rounded-full text-[#5a6b66] transition hover:bg-[#f1f4f2]">{{ $icon('x', 'h-5 w-5') }}</button>
            <span class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-[#f9e5e3] text-[#c2362f]">{{ $icon('trash', 'h-7 w-7') }}</span>
            <h3 class="mt-4 text-lg font-semibold">Hapus Pengguna</h3>
            <p class="mt-2 text-sm text-[#5a6b66]">Yakin ingin menghapus <strong id="delete-name" class="text-[#13211e]"></strong>? Tindakan ini tidak bisa dibatalkan.</p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" data-close class="cursor-pointer rounded-full border border-[#dce3e0] px-5 py-2 text-sm font-medium text-[#5a6b66] transition hover:border-[#13211e]">Batal</button>
                <button type="submit" class="cursor-pointer rounded-full bg-[#c2362f] px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a52d27]">Ya, Hapus</button>
            </div>
        </form>
    </div>
@endpush

@push('scripts')
<script>
    const $ = (s, r = document) => r.querySelector(s);
    const open = id => { const m = $('#modal-' + id); m.classList.remove('hidden'); m.classList.add('flex'); };
    const closeAll = () => document.querySelectorAll('[id^="modal-"]').forEach(o => { o.classList.add('hidden'); o.classList.remove('flex'); });

    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', closeAll));
    document.querySelectorAll('[id^="modal-"]').forEach(o => o.addEventListener('click', e => { if (e.target === o) closeAll(); }));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAll(); });

    // Tambah: form kosong
    $('#btn-add').addEventListener('click', () => { $('#form-create').reset(); open('create'); });

    // Ubah: isi form dari data baris (tanpa request tambahan ke server)
    document.querySelectorAll('.icon-edit').forEach(btn => btn.addEventListener('click', () => {
        const form = $('#form-update');
        form.reset();
        form.action = btn.dataset.action;
        Object.entries(JSON.parse(btn.dataset.user)).forEach(([k, v]) => { const el = form.elements[k]; if (el) el.value = v ?? ''; });
        open('update');
    }));

    // Hapus: konfirmasi dulu
    document.querySelectorAll('.icon-del').forEach(btn => btn.addEventListener('click', () => {
        $('#form-delete').action = btn.dataset.action;
        $('#delete-name').textContent = btn.dataset.name;
        open('delete');
    }));

    // Buka kembali modal setelah validasi gagal
    @if ($reopen === 'create')
        open('create');
    @elseif ($reopen === 'update' && old('user_id'))
        $('#form-update').action = "{{ route('akun.users.update', ['user' => old('user_id', 0)]) }}";
        open('update');
    @endif
</script>
@endpush
