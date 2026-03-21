<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    /**
     * Seed medical specialties found in Spanish health insurance directories.
     */
    public function run(): void
    {
        $specialties = [
            // ─── Categoría: Médica (48 specialties) ───
            ['name' => 'Alergología', 'category' => 'medica', 'sort_order' => 1],
            ['name' => 'Anestesiología', 'category' => 'medica', 'sort_order' => 2],
            ['name' => 'Angiología y Cirugía Vascular', 'category' => 'medica', 'sort_order' => 3],
            ['name' => 'Aparato Digestivo', 'category' => 'medica', 'sort_order' => 4],
            ['name' => 'Cardiología', 'category' => 'medica', 'sort_order' => 5],
            ['name' => 'Cirugía Cardiovascular', 'category' => 'medica', 'sort_order' => 6],
            ['name' => 'Cirugía General', 'category' => 'medica', 'sort_order' => 7],
            ['name' => 'Cirugía Maxilofacial', 'category' => 'medica', 'sort_order' => 8],
            ['name' => 'Cirugía Pediátrica', 'category' => 'medica', 'sort_order' => 9],
            ['name' => 'Cirugía Plástica', 'category' => 'medica', 'sort_order' => 10],
            ['name' => 'Cirugía Torácica', 'category' => 'medica', 'sort_order' => 11],
            ['name' => 'Dermatología', 'category' => 'medica', 'sort_order' => 12],
            ['name' => 'Endocrinología', 'category' => 'medica', 'sort_order' => 13],
            ['name' => 'Geriatría', 'category' => 'medica', 'sort_order' => 14],
            ['name' => 'Ginecología y Obstetricia', 'category' => 'medica', 'sort_order' => 15],
            ['name' => 'Hematología', 'category' => 'medica', 'sort_order' => 16],
            ['name' => 'Medicina del Sueño', 'category' => 'medica', 'sort_order' => 17],
            ['name' => 'Medicina Estética', 'category' => 'medica', 'sort_order' => 18],
            ['name' => 'Medicina de Familia', 'category' => 'medica', 'sort_order' => 19],
            ['name' => 'Medicina Intensiva', 'category' => 'medica', 'sort_order' => 20],
            ['name' => 'Medicina Interna', 'category' => 'medica', 'sort_order' => 21],
            ['name' => 'Medicina Regenerativa', 'category' => 'medica', 'sort_order' => 22],
            ['name' => 'Nefrología', 'category' => 'medica', 'sort_order' => 23],
            ['name' => 'Neumología', 'category' => 'medica', 'sort_order' => 24],
            ['name' => 'Neurocirugía', 'category' => 'medica', 'sort_order' => 25],
            ['name' => 'Neurología', 'category' => 'medica', 'sort_order' => 26],
            ['name' => 'Oftalmología', 'category' => 'medica', 'sort_order' => 27],
            ['name' => 'Oncología', 'category' => 'medica', 'sort_order' => 28],
            ['name' => 'Oncología Radioterápica', 'category' => 'medica', 'sort_order' => 29],
            ['name' => 'Otorrinolaringología', 'category' => 'medica', 'sort_order' => 30],
            ['name' => 'Pediatría', 'category' => 'medica', 'sort_order' => 31],
            ['name' => 'Psicología', 'category' => 'medica', 'sort_order' => 32],
            ['name' => 'Psiquiatría', 'category' => 'medica', 'sort_order' => 33],
            ['name' => 'Reumatología', 'category' => 'medica', 'sort_order' => 34],
            ['name' => 'Reproducción Asistida', 'category' => 'medica', 'sort_order' => 35],
            ['name' => 'Traumatología', 'category' => 'medica', 'sort_order' => 36],
            ['name' => 'Unidad del Dolor', 'category' => 'medica', 'sort_order' => 37],
            ['name' => 'Urología', 'category' => 'medica', 'sort_order' => 38],
            ['name' => 'Podología', 'category' => 'medica', 'sort_order' => 39],
            ['name' => 'Logopedia', 'category' => 'medica', 'sort_order' => 40],
            ['name' => 'Andrología', 'category' => 'medica', 'sort_order' => 41],
            ['name' => 'Proctología', 'category' => 'medica', 'sort_order' => 42],
            ['name' => 'Rehabilitación', 'category' => 'medica', 'sort_order' => 43],
            ['name' => 'Mastología', 'category' => 'medica', 'sort_order' => 44],
            ['name' => 'Acupuntura', 'category' => 'medica', 'sort_order' => 45],
            ['name' => 'Ozonoterapia', 'category' => 'medica', 'sort_order' => 46],
            ['name' => 'Nutrición y Dietética', 'category' => 'medica', 'sort_order' => 47],
            ['name' => 'Medicina del Trabajo', 'category' => 'medica', 'sort_order' => 48],

            // ─── Categoría: Dental (7 specialties) ───
            ['name' => 'Odontología General', 'category' => 'dental', 'sort_order' => 1],
            ['name' => 'Ortodoncia', 'category' => 'dental', 'sort_order' => 2],
            ['name' => 'Endodoncia', 'category' => 'dental', 'sort_order' => 3],
            ['name' => 'Periodoncia', 'category' => 'dental', 'sort_order' => 4],
            ['name' => 'Implantología', 'category' => 'dental', 'sort_order' => 5],
            ['name' => 'Cirugía Oral', 'category' => 'dental', 'sort_order' => 6],
            ['name' => 'Estética Dental', 'category' => 'dental', 'sort_order' => 7],

            // ─── Categoría: Pruebas diagnósticas (18 specialties) ───
            ['name' => 'Análisis Clínicos', 'category' => 'pruebas', 'sort_order' => 1],
            ['name' => 'Radiología General', 'category' => 'pruebas', 'sort_order' => 2],
            ['name' => 'Diagnóstico por Imagen', 'category' => 'pruebas', 'sort_order' => 3],
            ['name' => 'Doppler Cardíaco', 'category' => 'pruebas', 'sort_order' => 4],
            ['name' => 'Electrocardiografía', 'category' => 'pruebas', 'sort_order' => 5],
            ['name' => 'Electroencefalografía', 'category' => 'pruebas', 'sort_order' => 6],
            ['name' => 'Electromiografía', 'category' => 'pruebas', 'sort_order' => 7],
            ['name' => 'Resonancia Magnética', 'category' => 'pruebas', 'sort_order' => 8],
            ['name' => 'TAC', 'category' => 'pruebas', 'sort_order' => 9],
            ['name' => 'Medicina Nuclear PET-TAC', 'category' => 'pruebas', 'sort_order' => 10],
            ['name' => 'Pruebas Genéticas', 'category' => 'pruebas', 'sort_order' => 11],
            ['name' => 'Ecografía', 'category' => 'pruebas', 'sort_order' => 12],
            ['name' => 'Mamografía', 'category' => 'pruebas', 'sort_order' => 13],
            ['name' => 'Endoscopia', 'category' => 'pruebas', 'sort_order' => 14],
            ['name' => 'Ecocardiograma', 'category' => 'pruebas', 'sort_order' => 15],
            ['name' => 'Holter', 'category' => 'pruebas', 'sort_order' => 16],
            ['name' => 'Polisomnografía', 'category' => 'pruebas', 'sort_order' => 17],
            ['name' => 'Colonoscopia', 'category' => 'pruebas', 'sort_order' => 18],
        ];

        $now = now();

        foreach ($specialties as $specialty) {
            $slug = Str::slug($specialty['name']);

            DB::table('specialties')->updateOrInsert(
                ['slug' => $slug],
                array_merge($specialty, [
                    'slug' => $slug,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
