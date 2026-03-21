<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MissingProductsSeeder extends Seeder
{
    /**
     * Add basic Salud + Dental products for insurers that have 0 products.
     */
    public function run(): void
    {
        $now = now();
        $added = 0;

        // Get all insurer IDs
        $insurers = DB::table('insurers')->select('id', 'name', 'slug')->get();

        // Get insurers that already have products
        $insurersWithProducts = DB::table('insurer_products')
            ->select('insurer_id')
            ->distinct()
            ->pluck('insurer_id')
            ->toArray();

        foreach ($insurers as $insurer) {
            if (in_array($insurer->id, $insurersWithProducts)) {
                continue;
            }

            // Add "{Name} Salud"
            $saludSlug = Str::slug($insurer->name . ' salud');
            DB::table('insurer_products')->updateOrInsert(
                ['slug' => $saludSlug],
                [
                    'insurer_id' => $insurer->id,
                    'name' => "{$insurer->name} Salud",
                    'slug' => $saludSlug,
                    'description' => "Seguro de salud de {$insurer->name}. Accede a médicos, especialistas, hospitales y centros de salud en toda España con el cuadro médico de {$insurer->name}.",
                    'meta_title' => "Cuadro Médico {$insurer->name} Salud 2026 - Coberturas y Médicos",
                    'meta_description' => "Consulta el cuadro médico de {$insurer->name} Salud 2026. Encuentra médicos, especialistas y centros de salud incluidos.",
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $added++;

            // Add "{Name} Dental"
            $dentalSlug = Str::slug($insurer->name . ' dental');
            DB::table('insurer_products')->updateOrInsert(
                ['slug' => $dentalSlug],
                [
                    'insurer_id' => $insurer->id,
                    'name' => "{$insurer->name} Dental",
                    'slug' => $dentalSlug,
                    'description' => "Seguro dental de {$insurer->name}. Incluye revisiones, limpiezas, empastes y extracciones con acceso a una red de clínicas dentales.",
                    'meta_title' => "Cuadro Médico {$insurer->name} Dental 2026 - Clínicas y Dentistas",
                    'meta_description' => "Consulta el cuadro dental de {$insurer->name} 2026. Clínicas dentales y dentistas incluidos en el seguro dental.",
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
            $added++;
        }

        $total = DB::table('insurer_products')->count();
        $this->command->info("Missing products added: {$added} (total products: {$total})");
    }
}
