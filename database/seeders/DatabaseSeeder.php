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
            InsurerProductSeeder::class, // Must run after InsurerSeeder
        ]);
    }
}
