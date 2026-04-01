<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'Pemula', 'description' => 'Baru mulai membantu', 'icon' => '🌱', 'points_required' => 0],
            ['name' => 'Penolong', 'description' => 'Sudah membantu beberapa kali', 'icon' => '🤝', 'points_required' => 30],
            ['name' => 'Pahlawan', 'description' => 'Pahlawan penemu barang', 'icon' => '🦸', 'points_required' => 50],
            ['name' => 'Legenda', 'description' => 'Legenda I FOUND', 'icon' => '👑', 'points_required' => 100],
            ['name' => 'Master Finder', 'description' => 'Master penemu sejati', 'icon' => '💎', 'points_required' => 200],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['name' => $badge['name']], $badge);
        }
    }
}
