<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            InsurerSeeder::class,
            ProvinceSeeder::class,
            SpecialtySeeder::class,
            SpecialGroupSeeder::class,
            InsurerProductSeeder::class,         // Must run after InsurerSeeder
            MissingProductsSeeder::class,        // Adds Salud+Dental for insurers with 0 products
            InsurerProvinceSeeder::class,        // 30×52 = 1,560 pivot rows
            InsurerSpecialGroupSeeder::class,    // MUFACE/MUGEJU/ISFAS concertadas
        ]);
    }
}
