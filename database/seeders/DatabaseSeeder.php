<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(ManagerSeeder::class);

        User::factory()->cashier()->create([
            'name' => 'Kasir Contoh',
            'email' => 'kasir@resper.test',
        ]);

        User::factory()->staff()->create([
            'name' => 'Staff Contoh',
            'email' => 'staff@resper.test',
        ]);
    }
}