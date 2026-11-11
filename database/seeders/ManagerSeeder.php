<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew([
            'email' => env('MANAGER_EMAIL', 'manager@resper.test'),
        ]);

        $user->name = 'Manajer RESPER';

        if (! $user->exists) {
            $user->password = env('MANAGER_PASSWORD', 'password');
            $user->email_verified_at = now();
        }

        $user->role = UserRole::Manager;
        $user->account_status = AccountStatus::Active;
        $user->save();
    }
}