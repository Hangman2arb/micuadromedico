<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerProvinceSeeder extends Seeder
{
    /**
     * Seed insurer_province pivot: 30 insurers × 52 provinces = 1,560 rows.
     */
    public function run(): void
    {
        $insurers = DB::table('insurers')->select('id', 'name', 'slug', 'sort_order')->get();
        $provinces = DB::table('provinces')->select('id', 'name', 'slug')->get();
        $allSpecialties = DB::table('specialties')->pluck('name')->toArray();

        $now = now();
        $rows = 0;

        // Top 5 insurers get all 56 specialties
        $topSlugs = ['adeslas', 'sanitas', 'asisa', 'dkv', 'mapfre'];
        // Medium insurers (sort_order 6-15) get 35-45
        // Small insurers (sort_order 16+) get 20-30

        $localities = $this->getLocalitiesByProvince();

        foreach ($insurers as $insurer) {
            // Determine specialty count based on tier
            if (in_array($insurer->slug, $topSlugs)) {
                $specialtyCount = count($allSpecialties); // All 56
            } elseif ($insurer->sort_order <= 15) {
                $specialtyCount = rand(35, 45);
            } else {
                $specialtyCount = rand(20, 30);
            }

            // Shuffle and pick specialties (always include core ones)
            $coreSpecialties = [
                'Medicina de Familia', 'Pediatría', 'Ginecología y Obstetricia',
                'Traumatología', 'Dermatología', 'Oftalmología', 'Cardiología',
                'Otorrinolaringología', 'Urología', 'Análisis Clínicos',
                'Radiología General', 'Odontología General',
            ];

            if ($specialtyCount >= count($allSpecialties)) {
                $selectedSpecialties = $allSpecialties;
            } else {
                $remaining = array_diff($allSpecialties, $coreSpecialties);
                shuffle($remaining);
                $extra = array_slice($remaining, 0, max(0, $specialtyCount - count($coreSpecialties)));
                $selectedSpecialties = array_merge($coreSpecialties, $extra);
                sort($selectedSpecialties);
            }

            foreach ($provinces as $province) {
                $pdfUrl = $this->buildPdfUrl($insurer, $province);

                DB::table('insurer_province')->updateOrInsert(
                    [
                        'insurer_id' => $insurer->id,
                        'province_id' => $province->id,
                    ],
                    [
                        'pdf_url' => $pdfUrl,
                        'meta_title' => "Cuadro Médico {$insurer->name} en {$province->name} 2026",
                        'meta_description' => "Consulta el cuadro médico de {$insurer->name} en {$province->name} 2026. Encuentra médicos, especialistas, centros de salud y hospitales de {$insurer->name} en {$province->name}.",
                        'specialties_available' => json_encode($selectedSpecialties, JSON_UNESCAPED_UNICODE),
                        'localities_covered' => json_encode($localities[$province->slug] ?? [], JSON_UNESCAPED_UNICODE),
                        'last_updated_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
                $rows++;
            }
        }

        $this->command->info("Insurer-Province pivot: {$rows} rows seeded");
    }

    private function buildPdfUrl(object $insurer, object $province): string
    {
        // DKV has its own PDF portal
        if ($insurer->slug === 'dkv') {
            $provinceName = $this->normalizeName($province->name);
            return "https://medicos.dkv.es/ccmpdfs/Privada_{$provinceName}_ES.PDF";
        }

        // Default: polizamedica.es pattern
        $insurerName = $this->normalizeName($insurer->name);
        $provinceName = $this->normalizeName($province->name);

        return "https://www.polizamedica.es/Cuadros-Medicos/{$insurerName}/{$provinceName}.pdf";
    }

    /**
     * Normalize names for URL patterns (remove accents, spaces → hyphens).
     */
    private function normalizeName(string $name): string
    {
        $map = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ü' => 'U', 'Ñ' => 'N',
        ];
        $name = strtr($name, $map);
        $name = str_replace(' ', '-', $name);
        return $name;
    }

    /**
     * Main municipalities per province (INE public data, 5-20 per province).
     */
    private function getLocalitiesByProvince(): array
    {
        return [
            // Andalucía
            'almeria' => ['Almería', 'Roquetas de Mar', 'El Ejido', 'Vícar', 'Níjar', 'Adra', 'Huércal-Overa', 'Vera', 'Garrucha', 'Mojácar'],
            'cadiz' => ['Cádiz', 'Jerez de la Frontera', 'Algeciras', 'San Fernando', 'El Puerto de Santa María', 'Chiclana de la Frontera', 'Sanlúcar de Barrameda', 'La Línea de la Concepción', 'Puerto Real', 'Rota', 'Arcos de la Frontera'],
            'cordoba' => ['Córdoba', 'Lucena', 'Puente Genil', 'Montilla', 'Priego de Córdoba', 'Cabra', 'Palma del Río', 'Baena', 'Pozoblanco', 'Peñarroya-Pueblonuevo'],
            'granada' => ['Granada', 'Motril', 'Armilla', 'Maracena', 'Almuñécar', 'Loja', 'Baza', 'Guadix', 'Las Gabias', 'Atarfe', 'Santa Fe'],
            'huelva' => ['Huelva', 'Lepe', 'Almonte', 'Isla Cristina', 'Moguer', 'Ayamonte', 'Cartaya', 'Punta Umbría', 'Bollullos Par del Condado', 'Gibraleón'],
            'jaen' => ['Jaén', 'Linares', 'Úbeda', 'Baeza', 'Andújar', 'Martos', 'Alcalá la Real', 'La Carolina', 'Torredonjimeno', 'Mancha Real'],
            'malaga' => ['Málaga', 'Marbella', 'Mijas', 'Vélez-Málaga', 'Fuengirola', 'Torremolinos', 'Benalmádena', 'Estepona', 'Ronda', 'Antequera', 'Nerja', 'Alhaurín de la Torre', 'Rincón de la Victoria'],
            'sevilla' => ['Sevilla', 'Dos Hermanas', 'Alcalá de Guadaíra', 'Utrera', 'Mairena del Aljarafe', 'Écija', 'Carmona', 'La Rinconada', 'Los Palacios y Villafranca', 'Coria del Río', 'Tomares', 'San Juan de Aznalfarache'],

            // Aragón
            'huesca' => ['Huesca', 'Monzón', 'Barbastro', 'Jaca', 'Fraga', 'Sabiñánigo', 'Binéfar', 'Sariñena'],
            'teruel' => ['Teruel', 'Alcañiz', 'Andorra', 'Calamocha', 'Utrillas', 'Monreal del Campo'],
            'zaragoza' => ['Zaragoza', 'Calatayud', 'Utebo', 'Ejea de los Caballeros', 'Tarazona', 'Caspe', 'La Almunia de Doña Godina', 'Cuarte de Huerva', 'Zuera', 'La Puebla de Alfindén'],

            // Asturias
            'asturias' => ['Oviedo', 'Gijón', 'Avilés', 'Langreo', 'Mieres', 'Siero', 'Castrillón', 'San Martín del Rey Aurelio', 'Llanera', 'Corvera de Asturias', 'Villaviciosa', 'Cangas del Narcea'],

            // Baleares
            'baleares' => ['Palma', 'Calvià', 'Manacor', 'Ibiza', 'Llucmajor', 'Marratxí', 'Inca', 'Santa Eulària des Riu', 'Mahón', 'Ciutadella de Menorca', 'Sóller', 'Felanitx'],

            // Canarias
            'las-palmas' => ['Las Palmas de Gran Canaria', 'Telde', 'Santa Lucía de Tirajana', 'Arrecife', 'San Bartolomé de Tirajana', 'Arucas', 'Ingenio', 'Agüimes', 'Puerto del Rosario', 'Gáldar', 'Mogán', 'Teguise'],
            'santa-cruz-de-tenerife' => ['Santa Cruz de Tenerife', 'San Cristóbal de La Laguna', 'Arona', 'Adeje', 'La Orotava', 'Granadilla de Abona', 'Los Realejos', 'Puerto de la Cruz', 'Candelaria', 'Güímar', 'Tacoronte', 'Los Llanos de Aridane'],

            // Cantabria
            'cantabria' => ['Santander', 'Torrelavega', 'Camargo', 'Piélagos', 'El Astillero', 'Castro-Urdiales', 'Santa Cruz de Bezana', 'Laredo', 'Los Corrales de Buelna', 'Santoña'],

            // Castilla-La Mancha
            'albacete' => ['Albacete', 'Hellín', 'Villarrobledo', 'Almansa', 'La Roda', 'Caudete', 'Madrigueras', 'Tobarra'],
            'ciudad-real' => ['Ciudad Real', 'Puertollano', 'Tomelloso', 'Valdepeñas', 'Alcázar de San Juan', 'Manzanares', 'Daimiel', 'Miguelturra', 'La Solana', 'Campo de Criptana'],
            'cuenca' => ['Cuenca', 'Tarancón', 'San Clemente', 'Quintanar del Rey', 'Motilla del Palancar', 'Las Pedroñeras'],
            'guadalajara' => ['Guadalajara', 'Azuqueca de Henares', 'Alovera', 'Cabanillas del Campo', 'El Casar', 'Marchamalo', 'Sigüenza', 'Villanueva de la Torre'],
            'toledo' => ['Toledo', 'Talavera de la Reina', 'Illescas', 'Seseña', 'Torrijos', 'Consuegra', 'Madridejos', 'Sonseca', 'Quintanar de la Orden', 'Mora'],

            // Castilla y León
            'avila' => ['Ávila', 'Arévalo', 'Las Navas del Marqués', 'Candeleda', 'El Tiemblo', 'Arenas de San Pedro'],
            'burgos' => ['Burgos', 'Miranda de Ebro', 'Aranda de Duero', 'Briviesca', 'Medina de Pomar', 'Villarcayo de Merindad de Castilla la Vieja', 'Lerma'],
            'leon' => ['León', 'Ponferrada', 'San Andrés del Rabanedo', 'Villaquilambre', 'Astorga', 'La Bañeza', 'Bembibre', 'Villablino', 'Cacabelos'],
            'palencia' => ['Palencia', 'Aguilar de Campoo', 'Guardo', 'Venta de Baños', 'Dueñas', 'Villamuriel de Cerrato'],
            'salamanca' => ['Salamanca', 'Béjar', 'Ciudad Rodrigo', 'Santa Marta de Tormes', 'Villares de la Reina', 'Peñaranda de Bracamonte', 'Carbajosa de la Sagrada'],
            'segovia' => ['Segovia', 'Cuéllar', 'San Ildefonso', 'El Espinar', 'Palazuelos de Eresma', 'Cantalejo'],
            'soria' => ['Soria', 'Almazán', 'El Burgo de Osma', 'San Leonardo de Yagüe', 'Ólvega', 'San Esteban de Gormaz'],
            'valladolid' => ['Valladolid', 'Medina del Campo', 'Laguna de Duero', 'Arroyo de la Encomienda', 'Tordesillas', 'Íscar', 'Tudela de Duero', 'Simancas', 'Cigales', 'Peñafiel'],
            'zamora' => ['Zamora', 'Benavente', 'Toro', 'Morales del Vino', 'Puebla de Sanabria', 'Villalpando'],

            // Cataluña
            'barcelona' => ['Barcelona', 'L\'Hospitalet de Llobregat', 'Badalona', 'Terrassa', 'Sabadell', 'Mataró', 'Santa Coloma de Gramenet', 'Cornellà de Llobregat', 'Sant Boi de Llobregat', 'Sant Cugat del Vallès', 'Rubí', 'Manresa', 'Vilanova i la Geltrú', 'Viladecans', 'Granollers', 'Cerdanyola del Vallès', 'Castelldefels', 'Mollet del Vallès', 'Gavà', 'Igualada'],
            'girona' => ['Girona', 'Figueres', 'Blanes', 'Lloret de Mar', 'Olot', 'Salt', 'Sant Feliu de Guíxols', 'Palafrugell', 'Roses', 'Banyoles', 'Ripoll'],
            'lleida' => ['Lleida', 'Balaguer', 'Tàrrega', 'Mollerussa', 'La Seu d\'Urgell', 'Cervera', 'Solsona', 'Tremp', 'Sort'],
            'tarragona' => ['Tarragona', 'Reus', 'Tortosa', 'El Vendrell', 'Cambrils', 'Salou', 'Valls', 'Vila-seca', 'Amposta', 'Calafell', 'Torredembarra'],

            // Comunidad Valenciana
            'alicante' => ['Alicante', 'Elche', 'Torrevieja', 'Orihuela', 'Benidorm', 'Alcoy', 'San Vicente del Raspeig', 'Elda', 'Dénia', 'Petrer', 'Santa Pola', 'Villena', 'Crevillent', 'Novelda', 'Jávea', 'Altea', 'Calpe', 'Ibi'],
            'castellon' => ['Castellón de la Plana', 'Vila-real', 'Burriana', 'Vinaròs', 'La Vall d\'Uixó', 'Benicarló', 'Onda', 'Almassora', 'Benicàssim', 'Nules', 'Oropesa del Mar'],
            'valencia' => ['Valencia', 'Torrent', 'Gandía', 'Paterna', 'Sagunto', 'Mislata', 'Burjassot', 'Ontinyent', 'Aldaia', 'Manises', 'Alzira', 'Xirivella', 'Sueca', 'Requena', 'Catarroja', 'Alboraya', 'Bétera'],

            // Extremadura
            'badajoz' => ['Badajoz', 'Mérida', 'Don Benito', 'Almendralejo', 'Villanueva de la Serena', 'Zafra', 'Montijo', 'Olivenza', 'Villafranca de los Barros', 'Jerez de los Caballeros'],
            'caceres' => ['Cáceres', 'Plasencia', 'Navalmoral de la Mata', 'Trujillo', 'Coria', 'Miajadas', 'Talayuela', 'Moraleja'],

            // Galicia
            'a-coruna' => ['A Coruña', 'Santiago de Compostela', 'Ferrol', 'Narón', 'Oleiros', 'Carballo', 'Arteixo', 'Culleredo', 'Cambre', 'Ares', 'Ribeira', 'Betanzos'],
            'lugo' => ['Lugo', 'Monforte de Lemos', 'Viveiro', 'Vilalba', 'Sarria', 'Burela', 'Chantada', 'Foz'],
            'ourense' => ['Ourense', 'Verín', 'O Barco de Valdeorras', 'Carballiño', 'Xinzo de Limia', 'Celanova', 'Allariz'],
            'pontevedra' => ['Vigo', 'Pontevedra', 'Vilagarcía de Arousa', 'Redondela', 'Cangas', 'Marín', 'Lalín', 'O Porriño', 'Ponteareas', 'Tui', 'Nigrán', 'Baiona', 'Sanxenxo'],

            // La Rioja
            'la-rioja' => ['Logroño', 'Calahorra', 'Arnedo', 'Haro', 'Alfaro', 'Lardero', 'Nájera', 'Santo Domingo de la Calzada', 'Villamediana de Iregua'],

            // Madrid
            'madrid' => ['Madrid', 'Móstoles', 'Alcalá de Henares', 'Fuenlabrada', 'Leganés', 'Getafe', 'Alcorcón', 'Torrejón de Ardoz', 'Parla', 'Alcobendas', 'Las Rozas de Madrid', 'San Sebastián de los Reyes', 'Pozuelo de Alarcón', 'Coslada', 'Rivas-Vaciamadrid', 'Valdemoro', 'Majadahonda', 'Collado Villalba', 'Aranjuez', 'Arganda del Rey'],

            // Murcia
            'murcia' => ['Murcia', 'Cartagena', 'Lorca', 'Molina de Segura', 'Alcantarilla', 'Mazarrón', 'Cieza', 'Águilas', 'Yecla', 'Torre-Pacheco', 'San Javier', 'Caravaca de la Cruz', 'Totana', 'Jumilla'],

            // Navarra
            'navarra' => ['Pamplona', 'Tudela', 'Barañáin', 'Burlada', 'Estella-Lizarra', 'Zizur Mayor', 'Tafalla', 'Villava', 'Ansoáin', 'Berriozar', 'Huarte'],

            // País Vasco
            'alava' => ['Vitoria-Gasteiz', 'Llodio', 'Amurrio', 'Salvatierra', 'Oyón-Oion', 'Agurain'],
            'guipuzcoa' => ['San Sebastián', 'Irún', 'Errenteria', 'Eibar', 'Zarautz', 'Arrasate-Mondragón', 'Hernani', 'Tolosa', 'Hondarribia', 'Lasarte-Oria', 'Andoain', 'Bergara', 'Azpeitia', 'Oñati'],
            'vizcaya' => ['Bilbao', 'Barakaldo', 'Getxo', 'Portugalete', 'Santurtzi', 'Basauri', 'Leioa', 'Durango', 'Erandio', 'Galdakao', 'Sestao', 'Gernika-Lumo', 'Amorebieta-Etxano', 'Bermeo', 'Mungia'],

            // Ceuta y Melilla
            'ceuta' => ['Ceuta'],
            'melilla' => ['Melilla'],
        ];
    }
}
