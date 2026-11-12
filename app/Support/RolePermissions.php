<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class RolePermissions
{
    public const MAP = [
        // Modul Akun
        'kelola-role' => [UserRole::Manager],                       // akun.role.*

        // Modul Inventaris
        'kelola-inventaris' => [UserRole::Manager],                 // inventory.*

        // Modul Staff
        'kelola-staf' => [UserRole::Manager],                       // staff.index
        'lihat-jadwal' => [UserRole::Manager, UserRole::Staff, UserRole::Cashier], // staff.schedule
        'kelola-jadwal' => [UserRole::Manager],                     // tombol Add Schedule

        // Modul Pemesanan
        'kelola-pesanan' => [UserRole::Cashier],                    // order.*

        // Modul Keuangan
        'lihat-laporan-keuangan' => [UserRole::Manager],            // finance.report (Keuangan-3)
        'koreksi-transaksi' => [UserRole::Manager],                 // Keuangan-4
        'catat-transaksi' => [UserRole::Cashier],                   // Keuangan-1 dan 2, route-nya belum ada
    ];

    public static function register(): void
    {
        foreach (self::MAP as $ability => $roles) {
            Gate::define(
                $ability,
                fn (User $user): bool => $user->isActive() && $user->hasRole(...$roles)
            );
        }
    }
}