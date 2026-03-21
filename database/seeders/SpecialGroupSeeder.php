<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialGroupSeeder extends Seeder
{
    /**
     * Seed the 3 special groups for civil servants (MUFACE, MUGEJU, ISFAS).
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'MUFACE',
                'slug' => 'muface',
                'description' => 'MUFACE (Mutualidad General de Funcionarios Civiles del Estado) es el organismo público que gestiona la protección social de los funcionarios de la Administración General del Estado y sus familiares beneficiarios. Los mutualistas de MUFACE pueden elegir entre la asistencia sanitaria pública del Sistema Nacional de Salud o la prestada por entidades de seguro libre (aseguradoras privadas) a través de conciertos que se renuevan periódicamente. Actualmente, varias aseguradoras privadas ofrecen cobertura sanitaria a los funcionarios de MUFACE con cuadros médicos específicos.',
                'meta_title' => 'Cuadro Médico MUFACE 2026 - Aseguradoras y Coberturas para Funcionarios',
                'meta_description' => 'Consulta los cuadros médicos de MUFACE 2026. Compara las aseguradoras disponibles para funcionarios civiles del Estado: Adeslas, DKV, Asisa y más.',
            ],
            [
                'name' => 'MUGEJU',
                'slug' => 'mugeju',
                'description' => 'MUGEJU (Mutualidad General Judicial) es la entidad que proporciona la protección social de los miembros de la carrera judicial, fiscales, letrados de la administración de justicia y demás personal al servicio de la Administración de Justicia. Los mutualistas de MUGEJU pueden optar entre recibir asistencia sanitaria a través del sistema público o mediante aseguradoras privadas concertadas, con cuadros médicos adaptados a las necesidades del colectivo judicial.',
                'meta_title' => 'Cuadro Médico MUGEJU 2026 - Aseguradoras para Personal Judicial',
                'meta_description' => 'Consulta los cuadros médicos de MUGEJU 2026. Aseguradoras disponibles para jueces, fiscales y personal de la Administración de Justicia.',
            ],
            [
                'name' => 'ISFAS',
                'slug' => 'isfas',
                'description' => 'ISFAS (Instituto Social de las Fuerzas Armadas) es el organismo encargado de gestionar la protección social de los miembros de las Fuerzas Armadas españolas, la Guardia Civil y sus familias. Los afiliados al ISFAS pueden elegir anualmente entre la sanidad pública o la cobertura sanitaria a través de aseguradoras privadas concertadas, que ofrecen cuadros médicos específicos con acceso a especialistas, hospitales y centros de salud en todo el territorio nacional.',
                'meta_title' => 'Cuadro Médico ISFAS 2026 - Aseguradoras para Fuerzas Armadas',
                'meta_description' => 'Consulta los cuadros médicos de ISFAS 2026. Compara las aseguradoras disponibles para militares y Guardia Civil: coberturas, médicos y centros.',
            ],
        ];

        $now = now();

        foreach ($groups as $group) {
            DB::table('special_groups')->updateOrInsert(
                ['slug' => $group['slug']],
                array_merge($group, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
