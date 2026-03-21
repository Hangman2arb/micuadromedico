<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Seed all 52 Spanish provinces with their autonomous communities.
     */
    public function run(): void
    {
        $provinces = [
            // Andalucía (8)
            ['name' => 'Almería', 'slug' => 'almeria', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Cádiz', 'slug' => 'cadiz', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Córdoba', 'slug' => 'cordoba', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Granada', 'slug' => 'granada', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Huelva', 'slug' => 'huelva', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Jaén', 'slug' => 'jaen', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Málaga', 'slug' => 'malaga', 'autonomous_community' => 'Andalucía'],
            ['name' => 'Sevilla', 'slug' => 'sevilla', 'autonomous_community' => 'Andalucía'],

            // Aragón (3)
            ['name' => 'Huesca', 'slug' => 'huesca', 'autonomous_community' => 'Aragón'],
            ['name' => 'Teruel', 'slug' => 'teruel', 'autonomous_community' => 'Aragón'],
            ['name' => 'Zaragoza', 'slug' => 'zaragoza', 'autonomous_community' => 'Aragón'],

            // Asturias (1)
            ['name' => 'Asturias', 'slug' => 'asturias', 'autonomous_community' => 'Asturias'],

            // Baleares (1)
            ['name' => 'Baleares', 'slug' => 'baleares', 'autonomous_community' => 'Baleares'],

            // Canarias (2)
            ['name' => 'Las Palmas', 'slug' => 'las-palmas', 'autonomous_community' => 'Canarias'],
            ['name' => 'Santa Cruz de Tenerife', 'slug' => 'santa-cruz-de-tenerife', 'autonomous_community' => 'Canarias'],

            // Cantabria (1)
            ['name' => 'Cantabria', 'slug' => 'cantabria', 'autonomous_community' => 'Cantabria'],

            // Castilla-La Mancha (5)
            ['name' => 'Albacete', 'slug' => 'albacete', 'autonomous_community' => 'Castilla-La Mancha'],
            ['name' => 'Ciudad Real', 'slug' => 'ciudad-real', 'autonomous_community' => 'Castilla-La Mancha'],
            ['name' => 'Cuenca', 'slug' => 'cuenca', 'autonomous_community' => 'Castilla-La Mancha'],
            ['name' => 'Guadalajara', 'slug' => 'guadalajara', 'autonomous_community' => 'Castilla-La Mancha'],
            ['name' => 'Toledo', 'slug' => 'toledo', 'autonomous_community' => 'Castilla-La Mancha'],

            // Castilla y León (9)
            ['name' => 'Ávila', 'slug' => 'avila', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Burgos', 'slug' => 'burgos', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'León', 'slug' => 'leon', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Palencia', 'slug' => 'palencia', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Salamanca', 'slug' => 'salamanca', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Segovia', 'slug' => 'segovia', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Soria', 'slug' => 'soria', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Valladolid', 'slug' => 'valladolid', 'autonomous_community' => 'Castilla y León'],
            ['name' => 'Zamora', 'slug' => 'zamora', 'autonomous_community' => 'Castilla y León'],

            // Cataluña (4)
            ['name' => 'Barcelona', 'slug' => 'barcelona', 'autonomous_community' => 'Cataluña'],
            ['name' => 'Girona', 'slug' => 'girona', 'autonomous_community' => 'Cataluña'],
            ['name' => 'Lleida', 'slug' => 'lleida', 'autonomous_community' => 'Cataluña'],
            ['name' => 'Tarragona', 'slug' => 'tarragona', 'autonomous_community' => 'Cataluña'],

            // Comunidad Valenciana (3)
            ['name' => 'Alicante', 'slug' => 'alicante', 'autonomous_community' => 'Comunidad Valenciana'],
            ['name' => 'Castellón', 'slug' => 'castellon', 'autonomous_community' => 'Comunidad Valenciana'],
            ['name' => 'Valencia', 'slug' => 'valencia', 'autonomous_community' => 'Comunidad Valenciana'],

            // Extremadura (2)
            ['name' => 'Badajoz', 'slug' => 'badajoz', 'autonomous_community' => 'Extremadura'],
            ['name' => 'Cáceres', 'slug' => 'caceres', 'autonomous_community' => 'Extremadura'],

            // Galicia (4)
            ['name' => 'A Coruña', 'slug' => 'a-coruna', 'autonomous_community' => 'Galicia'],
            ['name' => 'Lugo', 'slug' => 'lugo', 'autonomous_community' => 'Galicia'],
            ['name' => 'Ourense', 'slug' => 'ourense', 'autonomous_community' => 'Galicia'],
            ['name' => 'Pontevedra', 'slug' => 'pontevedra', 'autonomous_community' => 'Galicia'],

            // La Rioja (1)
            ['name' => 'La Rioja', 'slug' => 'la-rioja', 'autonomous_community' => 'La Rioja'],

            // Madrid (1)
            ['name' => 'Madrid', 'slug' => 'madrid', 'autonomous_community' => 'Madrid'],

            // Murcia (1)
            ['name' => 'Murcia', 'slug' => 'murcia', 'autonomous_community' => 'Murcia'],

            // Navarra (1)
            ['name' => 'Navarra', 'slug' => 'navarra', 'autonomous_community' => 'Navarra'],

            // País Vasco (3)
            ['name' => 'Álava', 'slug' => 'alava', 'autonomous_community' => 'País Vasco'],
            ['name' => 'Guipúzcoa', 'slug' => 'guipuzcoa', 'autonomous_community' => 'País Vasco'],
            ['name' => 'Vizcaya', 'slug' => 'vizcaya', 'autonomous_community' => 'País Vasco'],

            // Ceuta (1)
            ['name' => 'Ceuta', 'slug' => 'ceuta', 'autonomous_community' => 'Ceuta'],

            // Melilla (1)
            ['name' => 'Melilla', 'slug' => 'melilla', 'autonomous_community' => 'Melilla'],
        ];

        $now = now();

        foreach ($provinces as $province) {
            DB::table('provinces')->updateOrInsert(
                ['slug' => $province['slug']],
                array_merge($province, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
