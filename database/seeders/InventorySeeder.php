<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Beras', 'Minyak Goreng', 'Ayam Potong', 'Telur', 'Gula Pasir', 'Tepung Terigu', 'Cabai Merah'];

        foreach ($items as $i => $name) {
            Inventory::updateOrCreate(
                ['code' => (string) (502623313 + $i)],
                [
                    'name' => $name,
                    'total' => random_int(100, 600),
                    'date' => now()->subDays($i)->toDateString(),
                    'manager_name' => 'Manajer Restoran',
                    'position' => 'Manager',
                ]
            );
        }
    }
}

