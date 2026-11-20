<?php

namespace App\Enums;

enum UserRole: string
{
    case Manager = 'manager';
    case Cashier = 'cashier';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Manajer',
            self::Cashier => 'Kasir',
            self::Staff => 'Staff',
        };
    }

    /** Halaman pertama setelah login, sesuai route yang sudah ada di repo. */
    public function homeRoute(): string
    {
        return match ($this) {
            self::Manager => 'finance.report',
            self::Cashier => 'order.pesanan',
            self::Staff => 'staff.schedule',
        };
    }
}