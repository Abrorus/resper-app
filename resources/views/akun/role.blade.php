@extends('layouts.app')

@section('title', 'Kelola Role')

@section('content')
    <p class="mb-1.5 text-[0.9rem] text-[#5a6b66]">Akun / Kelola Role</p>
    <h1 class="font-['Bricolage_Grotesque',sans-serif] text-[2.1rem] font-bold leading-tight tracking-tight">Kelola Role Pengguna</h1>
    <p class="mt-1.5 text-[#5a6b66]">Atur role dan pulihkan akun yang terblokir.</p>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#27AE60]/30 bg-[#27AE60]/10 px-4 py-3 text-[#0b3d31]">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-5 rounded-xl border border-[#c2362f]/30 bg-[#f9e5e3] px-4 py-3 text-[#c2362f]">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="mt-6 rounded-2xl border border-[#dce3e0] bg-white px-4 pb-4 pt-5 md:px-6">
        <form method="GET" action="{{ route('akun.role.index') }}" class="mb-4 flex flex-wrap gap-2">
            <select name="role" class="rounded-lg border border-[#dce3e0] bg-white px-3 py-2">
                <option value="">Semua role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected($filter === $role)>{{ $role->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="cursor-pointer rounded-lg border border-[#dce3e0] bg-white px-3.5 py-2 hover:border-[#13211e]">Terapkan</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-[#dce3e0] text-[0.9rem] text-[#5a6b66]">
                    <tr>
                        <th class="py-2.5 pr-4 font-medium">Nama</th>
                        <th class="py-2.5 pr-4 font-medium">Email</th>
                        <th class="py-2.5 pr-4 font-medium">Role</th>
                        <th class="py-2.5 pr-4 font-medium">Status</th>
                        <th class="py-2.5 pr-4 font-medium">Gagal Login</th>
                        <th class="py-2.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="border-b border-[#dce3e0] last:border-0">
                            <td class="py-3 pr-4 font-medium">{{ $user->name }}</td>
                            <td class="py-3 pr-4 text-[#5a6b66]">{{ $user->email }}</td>

                            @if ($user->is(auth()->user()))
                                <td class="py-3 pr-4">{{ $user->role->label() }}</td>
                                <td class="py-3 pr-4">{{ $user->account_status->label() }}</td>
                                <td class="py-3 pr-4 tabular-nums">{{ $user->failed_login_attempts }}</td>
                                <td class="py-3 text-[0.9rem] text-[#5a6b66]">Akun Anda</td>
                            @else
                                {{-- <form> tidak boleh membungkus <td>, jadi select dihubungkan lewat atribut form --}}
                                <td class="py-3 pr-4">
                                    <select name="role" form="role-form-{{ $user->id }}" class="rounded-lg border border-[#dce3e0] bg-white px-2.5 py-1.5">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3 pr-4">
                                    <select name="account_status" form="role-form-{{ $user->id }}" class="rounded-lg border border-[#dce3e0] bg-white px-2.5 py-1.5">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($user->account_status === $status)>{{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3 pr-4 tabular-nums">{{ $user->failed_login_attempts }}</td>
                                <td class="py-3">
                                    <form id="role-form-{{ $user->id }}" method="POST" action="{{ route('akun.role.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="cursor-pointer rounded-full bg-[#27AE60] px-4 py-1.5 font-semibold text-white hover:bg-[#229954]">Simpan</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-[#5a6b66]">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </section>
@endsection