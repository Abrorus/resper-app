<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['name' => 'Nasi Goreng Spesial',  'category' => 'Makanan Utama', 'price' => 24000],
            ['name' => 'Steak Tenderloin',     'category' => 'Makanan Utama', 'price' => 115000],
            ['name' => 'Soto Ayam',            'category' => 'Makanan Utama', 'price' => 15000],
            ['name' => 'Penyetan Ayam Spesial','category' => 'Makanan Utama', 'price' => 18000],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['name' => $menu['name']], $menu);
        }
    }
}
