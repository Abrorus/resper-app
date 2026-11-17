<?php

namespace App\Http\Requests\Akun;

final class UserRequestMessages
{
    public const MESSAGES = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah dipakai pengguna lain.',
        'phone.regex' => 'Nomor telepon hanya boleh angka (8-15 digit), boleh diawali +.',
        'password.required' => 'Password wajib diisi.',
        'password.confirmed' => 'Konfirmasi password tidak sama.',
        'password.min' => 'Password minimal 8 karakter.',
        'role.required' => 'Role wajib dipilih.',
        'role.enum' => 'Role tidak dikenal.',
        'account_status.required' => 'Status akun wajib dipilih.',
        'account_status.enum' => 'Status akun tidak dikenal.',
    ];
}
