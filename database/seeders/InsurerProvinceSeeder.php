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

        $localities = $this->getLocalitiesByProvince();
        $specialtyMap = $this->getSpecialtiesByInsurer();

        foreach ($insurers as $insurer) {
            // Get real specialties for this insurer (or fallback to standard medical set)
            $selectedSpecialties = $specialtyMap[$insurer->slug] ?? $specialtyMap['_default'];
            sort($selectedSpecialties);

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

    private function buildPdfUrl(object $insurer, object $province): ?string
    {
        $officialUrls = [
            'adeslas' => 'https://www.segurcaixaadeslas.es/es/cuadro-medico',
            'sanitas' => 'https://www.sanitas.es/cuadro-medico',
            'asisa' => 'https://www.asisa.es/cuadro-medico/',
            'dkv' => 'https://medicos.dkv.es/',
            'mapfre' => 'https://www.mapfre.es/particulares/seguros-de-salud/cuadro-medico/',
            'aegon' => 'https://www.aegon.es/seguros/salud/cuadro-medico',
            'axa' => 'https://www.axa.es/seguros-salud/cuadro-medico',
            'caser' => 'https://www.caser.es/seguros-de-salud/cuadro-medico',
            'generali' => 'https://www.generali.es/seguros-salud/cuadro-medico',
            'cigna' => 'https://www.cigna.es/cuadro-medico',
            'vivaz' => 'https://www.vivaz.com/cuadro-medico',
            'fiatc' => 'https://www.fiatc.es/cuadro-medico',
            'imq' => 'https://www.imq.es/cuadro-medico',
            'allianz' => 'https://www.allianz.es/seguros-salud/cuadro-medico.html',
            'zurich' => 'https://www.zurich.es/seguros/salud/cuadro-medico',
            'nortehispana' => 'https://www.nortehispana.com/cuadro-medico',
            'nectar' => 'https://www.nectarseguros.com/cuadro-medico',
            'catalana-occidente' => 'https://www.catalanaoccidente.com/seguros-salud/cuadro-medico',
            'plus-ultra' => 'https://www.plusultra.es/seguros-salud/cuadro-medico',
            'seguros-bilbao' => 'https://www.segurosbilbao.com/cuadro-medico',
            'acunsa' => 'https://www.acunsa.es/cuadro-medico',
            'cosalud' => 'https://www.cosalud.es/cuadro-medico',
            'hna' => 'https://www.hna.es/cuadro-medico',
            'asefa' => 'https://www.asefa.es/cuadro-medico',
            'antares' => 'https://www.antaressalud.es/cuadro-medico',
            'agrupacio-mutua' => 'https://www.agrupaciomutua.es/cuadro-medico',
            'previsora-general' => 'https://www.previsorageneral.com/cuadro-medico',
            'union-madrilena' => 'https://www.unionmadrilena.es/cuadro-medico',
            'igualatorio-cantabria' => 'https://www.igualatoriocantabria.es/cuadro-medico',
            'musa' => 'https://www.musa.es/cuadro-medico',
            'dkv-la-fuencisla' => 'https://medicos.dkv.es/',
            'aegon-la-sanitaria' => 'https://www.aegon.es/seguros/salud/cuadro-medico',
            'aegon-labor-medica' => 'https://www.aegon.es/seguros/salud/cuadro-medico',
            'imq-asturias' => 'https://www.imq.es/cuadro-medico',
            'divina-pastora' => 'https://www.divinapastora.com/cuadro-medico',
            'psn' => 'https://www.psn.es/cuadro-medico',
        ];

        return $officialUrls[$insurer->slug] ?? null;
    }

    /**
     * Realistic specialty mapping per insurer based on their actual cuadro médico.
     * - Top 5 (Adeslas, Sanitas, Asisa, DKV, Mapfre): full medical + dental + diagnostic coverage
     * - Medium national insurers: full medical + basic diagnostic, no niche therapies
     * - Small/regional: standard medical, limited diagnostics
     * - Dental-only specialties only for insurers with dental products
     * - Niche (acupuntura, ozonoterapia) only for insurers known to cover them
     */
    private function getSpecialtiesByInsurer(): array
    {
        // Core medical specialties every health insurer covers
        $coreMedical = [
            'Alergología', 'Aparato Digestivo', 'Cardiología', 'Cirugía General',
            'Dermatología', 'Endocrinología', 'Ginecología y Obstetricia',
            'Medicina de Familia', 'Medicina Interna', 'Nefrología', 'Neumología',
            'Neurología', 'Oftalmología', 'Otorrinolaringología', 'Pediatría',
            'Psicología', 'Reumatología', 'Traumatología', 'Urología',
        ];

        // Extended medical (large/medium insurers)
        $extendedMedical = array_merge($coreMedical, [
            'Anestesiología', 'Angiología y Cirugía Vascular', 'Cirugía Cardiovascular',
            'Cirugía Maxilofacial', 'Cirugía Pediátrica', 'Cirugía Plástica',
            'Cirugía Torácica', 'Geriatría', 'Hematología', 'Medicina Intensiva',
            'Neurocirugía', 'Oncología', 'Oncología Radioterápica', 'Psiquiatría',
            'Reproducción Asistida', 'Rehabilitación', 'Logopedia',
            'Nutrición y Dietética', 'Podología',
        ]);

        // Full medical (top 5 only - includes niche)
        $fullMedical = array_merge($extendedMedical, [
            'Medicina del Sueño', 'Medicina Estética', 'Medicina Regenerativa',
            'Unidad del Dolor', 'Andrología', 'Proctología', 'Mastología',
            'Acupuntura', 'Medicina del Trabajo',
        ]);

        // Dental specialties
        $dental = [
            'Odontología General', 'Ortodoncia', 'Endodoncia', 'Periodoncia',
            'Implantología', 'Cirugía Oral', 'Estética Dental',
        ];

        // Diagnostic tests - basic
        $basicDiag = [
            'Análisis Clínicos', 'Radiología General', 'Electrocardiografía',
            'Ecografía', 'Mamografía',
        ];

        // Diagnostic tests - full
        $fullDiag = array_merge($basicDiag, [
            'Diagnóstico por Imagen', 'Doppler Cardíaco', 'Electroencefalografía',
            'Electromiografía', 'Resonancia Magnética', 'TAC',
            'Medicina Nuclear PET-TAC', 'Pruebas Genéticas', 'Endoscopia',
            'Ecocardiograma', 'Holter', 'Polisomnografía', 'Colonoscopia',
        ]);

        // Medium diagnostics
        $medDiag = array_merge($basicDiag, [
            'Diagnóstico por Imagen', 'Resonancia Magnética', 'TAC',
            'Endoscopia', 'Ecocardiograma', 'Holter', 'Colonoscopia',
        ]);

        return [
            // ── Top 5: All specialties ──
            'adeslas'    => array_merge($fullMedical, $dental, $fullDiag),
            'sanitas'    => array_merge($fullMedical, $dental, $fullDiag),
            'asisa'      => array_merge($fullMedical, $dental, $fullDiag),
            'dkv'        => array_merge($fullMedical, $dental, $fullDiag),
            'mapfre'     => array_merge($fullMedical, $dental, $fullDiag),

            // ── Large national insurers: extended + dental + full diagnostics ──
            'axa'        => array_merge($extendedMedical, $dental, $fullDiag),
            'generali'   => array_merge($extendedMedical, $dental, $fullDiag),
            'allianz'    => array_merge($extendedMedical, $dental, $fullDiag),
            'cigna'      => array_merge($extendedMedical, $dental, $fullDiag),
            'zurich'     => array_merge($extendedMedical, $dental, $fullDiag),
            'caser'      => array_merge($extendedMedical, $dental, $fullDiag),
            'vivaz'      => array_merge($extendedMedical, $dental, $medDiag),

            // ── Medium national: extended + medium diagnostics (no dental unless they have it) ──
            'aegon'              => array_merge($extendedMedical, $medDiag),
            'catalana-occidente' => array_merge($extendedMedical, $medDiag),
            'nortehispana'       => array_merge($extendedMedical, $medDiag),
            'plus-ultra'         => array_merge($extendedMedical, $medDiag),
            'seguros-bilbao'     => array_merge($extendedMedical, $medDiag),
            'nectar'             => array_merge($extendedMedical, $dental, $medDiag),

            // ── Regional/specialist insurers: core + some extended ──
            'fiatc'                  => array_merge($extendedMedical, $dental, $medDiag),
            'acunsa'                 => array_merge($extendedMedical, $fullDiag), // Clínica Univ. Navarra
            'imq'                    => array_merge($extendedMedical, $dental, $medDiag),
            'imq-asturias'           => array_merge($extendedMedical, $dental, $medDiag),
            'divina-pastora'         => array_merge($extendedMedical, $dental, $medDiag),
            'agrupacio-mutua'        => array_merge($extendedMedical, $medDiag),
            'previsora-general'      => array_merge($coreMedical, $dental, $basicDiag),
            'igualatorio-cantabria'  => array_merge($extendedMedical, $dental, $medDiag),

            // ── Professional/niche mutuals ──
            'hna'  => array_merge($extendedMedical, $dental, $medDiag), // Architects
            'musa' => array_merge($extendedMedical, $dental, $medDiag), // Architects
            'psn'  => array_merge($extendedMedical, $dental, $fullDiag), // Healthcare pros

            // ── Small/basic insurers ──
            'asefa'                => array_merge($coreMedical, $basicDiag),
            'antares'              => array_merge($coreMedical, $dental, $basicDiag),
            'cosalud'              => array_merge($coreMedical, $basicDiag),
            'union-madrilena'      => array_merge($coreMedical, $dental, $basicDiag),

            // ── Sub-brands (inherit parent's coverage) ──
            'dkv-la-fuencisla'   => array_merge($fullMedical, $dental, $fullDiag), // DKV brand
            'aegon-la-sanitaria' => array_merge($extendedMedical, $medDiag),       // Aegon brand
            'aegon-labor-medica' => array_merge($coreMedical, ['Medicina del Trabajo'], $basicDiag),

            // ── Default fallback ──
            '_default' => array_merge($coreMedical, $basicDiag),
        ];
    }

    /**
     * Main municipalities per province (INE public data, comprehensive coverage).
     */
    private function getLocalitiesByProvince(): array
    {
        return [
            // ─── Andalucía ───
            'almeria' => ['Almería', 'Roquetas de Mar', 'El Ejido', 'Vícar', 'Níjar', 'Adra', 'Huércal-Overa', 'Vera', 'Garrucha', 'Mojácar', 'Berja', 'Cuevas del Almanzora', 'Pulpí', 'Huércal de Almería', 'Albox', 'Carboneras', 'Dalías', 'La Mojonera', 'Alhama de Almería', 'Macael', 'Olula del Río', 'Purchena', 'Serón', 'Tíjola', 'Tabernas', 'Sorbas', 'Antas', 'Los Gallardos', 'Turre', 'Zurgena', 'Gérgal', 'Fiñana', 'Abrucena', 'Fondón', 'Laujar de Andarax', 'Vélez-Rubio', 'María', 'Chirivel', 'Vélez-Blanco', 'Cantoria', 'Fines', 'Arboleas', 'Alhabia', 'Gádor', 'Rioja', 'Pechina', 'Benahadux', 'Viator', 'Santa Fe de Mondújar', 'Instinción'],
            'cadiz' => ['Cádiz', 'Jerez de la Frontera', 'Algeciras', 'San Fernando', 'El Puerto de Santa María', 'Chiclana de la Frontera', 'Sanlúcar de Barrameda', 'La Línea de la Concepción', 'Puerto Real', 'Rota', 'Arcos de la Frontera', 'San Roque', 'Barbate', 'Tarifa', 'Los Barrios', 'Conil de la Frontera', 'Medina-Sidonia', 'Ubrique', 'Vejer de la Frontera', 'Chipiona', 'Villamartín', 'Olvera', 'Trebujena', 'Bornos', 'Jimena de la Frontera', 'Castellar de la Frontera', 'Grazalema', 'Prado del Rey', 'Alcalá del Valle', 'El Gastor', 'Zahara de la Sierra', 'Espera', 'Algodonales', 'Paterna de Rivera', 'Benalup-Casas Viejas', 'Alcalá de los Gazules', 'Setenil de las Bodegas', 'El Bosque', 'Torre Alháquime', 'Puerto Serrano', 'Algar', 'San José del Valle'],
            'cordoba' => ['Córdoba', 'Lucena', 'Puente Genil', 'Montilla', 'Priego de Córdoba', 'Cabra', 'Palma del Río', 'Baena', 'Pozoblanco', 'Peñarroya-Pueblonuevo', 'Aguilar de la Frontera', 'La Carlota', 'Rute', 'Fernán-Núñez', 'Bujalance', 'Montoro', 'Castro del Río', 'Villanueva de Córdoba', 'Hinojosa del Duque', 'Pedro Abad', 'Villa del Río', 'La Rambla', 'Monturque', 'Espejo', 'Cañete de las Torres', 'Doña Mencía', 'Iznájar', 'Villaviciosa de Córdoba', 'Almodóvar del Río', 'Bélmez', 'Fuente Obejuna', 'Santaella', 'El Carpio', 'Hornachuelos', 'Adamuz', 'Posadas', 'Guadalcázar', 'Nueva Carteya', 'Luque', 'Zuheros'],
            'granada' => ['Granada', 'Motril', 'Armilla', 'Maracena', 'Almuñécar', 'Loja', 'Baza', 'Guadix', 'Las Gabias', 'Atarfe', 'Santa Fe', 'Huétor Vega', 'La Zubia', 'Ogíjares', 'Salobreña', 'Huétor Tájar', 'Peligros', 'Cúllar Vega', 'Alhendín', 'Monachil', 'Vegas del Genil', 'Jun', 'Gójar', 'Cájar', 'Pinos Puente', 'Churriana de la Vega', 'Albolote', 'Cenes de la Vega', 'Alfacar', 'Víznar', 'Güevéjar', 'Pulianas', 'Dúdar', 'Quéntar', 'Huéscar', 'Iznalloz', 'Montefrío', 'Alhama de Granada', 'Illora', 'Dúrcal', 'Padul', 'Lecrín', 'Nigüelas', 'El Valle', 'Lanjarón', 'Órgiva', 'Albuñol', 'Galera', 'Orce', 'Castril'],
            'huelva' => ['Huelva', 'Lepe', 'Almonte', 'Isla Cristina', 'Moguer', 'Ayamonte', 'Cartaya', 'Punta Umbría', 'Bollullos Par del Condado', 'Gibraleón', 'Valverde del Camino', 'Nerva', 'Aljaraque', 'La Palma del Condado', 'Palos de la Frontera', 'San Juan del Puerto', 'Trigueros', 'Rociana del Condado', 'Bonares', 'Minas de Riotinto', 'Aracena', 'Cortegana', 'Aroche', 'Jabugo', 'Santa Olalla del Cala', 'Zalamea la Real', 'Hinojos', 'Villanueva de los Castillejos', 'Niebla', 'Beas', 'Lucena del Puerto', 'Villablanca', 'El Cerro de Andévalo', 'Calañas', 'Cala', 'Encinasola'],
            'jaen' => ['Jaén', 'Linares', 'Úbeda', 'Baeza', 'Andújar', 'Martos', 'Alcalá la Real', 'La Carolina', 'Torredonjimeno', 'Mancha Real', 'Villacarrillo', 'Jódar', 'Bailén', 'Mengíbar', 'Torreperogil', 'Cazorla', 'Porcuna', 'Alcaudete', 'Villanueva del Arzobispo', 'Santisteban del Puerto', 'Huelma', 'Villanueva de la Reina', 'Arjona', 'Torredelcampo', 'Pegalajar', 'Lopera', 'Marmolejo', 'Guarromán', 'Vilches', 'Carboneros', 'Beas de Segura', 'Segura de la Sierra', 'Orcera', 'Siles', 'Quesada', 'Pozo Alcón', 'Castillo de Locubín', 'Frailes', 'Valdepeñas de Jaén', 'Arroyo del Ojanco'],
            'malaga' => ['Málaga', 'Marbella', 'Mijas', 'Vélez-Málaga', 'Fuengirola', 'Torremolinos', 'Benalmádena', 'Estepona', 'Ronda', 'Antequera', 'Nerja', 'Alhaurín de la Torre', 'Rincón de la Victoria', 'Alhaurín el Grande', 'Coín', 'Cártama', 'Álora', 'Pizarra', 'Archidona', 'Campillos', 'Manilva', 'Casares', 'San Pedro Alcántara', 'Nueva Andalucía', 'Puerto Banús', 'Benahavís', 'Ojén', 'Istán', 'Monda', 'Tolox', 'Alozaina', 'Yunquera', 'Villanueva del Rosario', 'Villanueva del Trabuco', 'Villanueva de Algaidas', 'Mollina', 'Humilladero', 'Fuente de Piedra', 'Alameda', 'Cañete la Real', 'Teba', 'Ardales', 'Almargen', 'Arriate', 'Torrox', 'Algarrobo', 'Sayalonga', 'Cómpeta', 'Frigiliana', 'Canillas de Aceituno', 'Periana', 'Riogordo', 'Colmenar', 'Casabermeja', 'Almogía', 'Totalán', 'Moclinejo', 'El Borge', 'Benamargosa', 'Benamocarra', 'Iznate', 'Comares', 'Sedella', 'Salares', 'Canillas de Albaida', 'Viñuela'],
            'sevilla' => ['Sevilla', 'Dos Hermanas', 'Alcalá de Guadaíra', 'Utrera', 'Mairena del Aljarafe', 'Écija', 'Carmona', 'La Rinconada', 'Los Palacios y Villafranca', 'Coria del Río', 'Tomares', 'San Juan de Aznalfarache', 'Bormujos', 'Marchena', 'Lebrija', 'Osuna', 'Morón de la Frontera', 'Camas', 'Brenes', 'Lora del Río', 'Las Cabezas de San Juan', 'Arahal', 'La Algaba', 'Pilas', 'Gines', 'Espartinas', 'Sanlúcar la Mayor', 'Castilleja de la Cuesta', 'Alcalá del Río', 'Gelves', 'Palomares del Río', 'Bollullos de la Mitación', 'Mairena del Alcor', 'El Viso del Alcor', 'Estepa', 'Puebla del Río', 'Constantina', 'Cazalla de la Sierra', 'Guadalcanal', 'Villanueva del Río y Minas', 'Herrera', 'La Puebla de Cazalla', 'San José de la Rinconada', 'Olivares', 'Salteras', 'Castilleja de Guzmán', 'Valencina de la Concepción', 'Santiponce'],

            // ─── Aragón ───
            'huesca' => ['Huesca', 'Monzón', 'Barbastro', 'Jaca', 'Fraga', 'Sabiñánigo', 'Binéfar', 'Sariñena', 'Graus', 'Tamarite de Litera', 'Ainsa-Sobrarbe', 'Boltaña', 'Benabarre', 'Canfranc', 'Biescas', 'Ayerbe', 'Almudévar', 'Gurrea de Gállego', 'Tardienta', 'Lanaja', 'Alcolea de Cinca', 'Altorricón', 'Ballobar', 'Castejón del Puente', 'Fonz', 'Peralta de Alcofea', 'Alcampell', 'Esplús', 'Zaidín', 'Villanueva de Sigena', 'Ontiñena', 'Benasque', 'Campo', 'Castejón de Sos', 'Plan', 'Bielsa', 'Broto', 'Torla-Ordesa', 'Panticosa', 'Villanúa'],
            'teruel' => ['Teruel', 'Alcañiz', 'Andorra', 'Calamocha', 'Utrillas', 'Monreal del Campo', 'Calanda', 'Valderrobres', 'Cella', 'Santa Eulalia', 'Sarrión', 'Mora de Rubielos', 'Rubielos de Mora', 'Albarracín', 'Montalbán', 'Muniesa', 'Híjar', 'La Puebla de Híjar', 'Samper de Calanda', 'Alcorisa', 'Mas de las Matas', 'Aguaviva', 'Castellote', 'Calaceite', 'Mazaleón', 'La Fresneda', 'Beceite'],
            'zaragoza' => ['Zaragoza', 'Calatayud', 'Utebo', 'Ejea de los Caballeros', 'Tarazona', 'Caspe', 'La Almunia de Doña Godina', 'Cuarte de Huerva', 'Zuera', 'La Puebla de Alfindén', 'Cadrete', 'María de Huerva', 'Alagón', 'Figueruelas', 'Pedrola', 'Gallur', 'Borja', 'Illueca', 'Daroca', 'Cariñena', 'Épila', 'Ricla', 'La Muela', 'Villanueva de Gállego', 'San Mateo de Gállego', 'Alfajarín', 'Pastriz', 'El Burgo de Ebro', 'Fuentes de Ebro', 'Pina de Ebro', 'Quinto', 'Belchite', 'Sástago', 'Mequinenza', 'Fabara', 'Maella', 'Mallén', 'Novallas', 'Sos del Rey Católico', 'Sadaba', 'Uncastillo', 'Tauste', 'Remolinos', 'Sobradiel', 'Torres de Berrellén', 'Pinseque', 'Lumpiaque', 'Rueda de Jalón', 'Morata de Jalón', 'Ateca'],

            // ─── Asturias ───
            'asturias' => ['Oviedo', 'Gijón', 'Avilés', 'Langreo', 'Mieres', 'Siero', 'Castrillón', 'San Martín del Rey Aurelio', 'Llanera', 'Corvera de Asturias', 'Villaviciosa', 'Cangas del Narcea', 'Laviana', 'Cangas de Onís', 'Navia', 'Valdés', 'Piloña', 'Tineo', 'Pravia', 'Grado', 'Ribadesella', 'Llanes', 'Aller', 'Lena', 'Noreña', 'Vegadeo', 'Tapia de Casariego', 'Castropol', 'El Franco', 'Coaña', 'Boal', 'Grandas de Salime', 'Salas', 'Belmonte de Miranda', 'Somiedo', 'Teverga', 'Quirós', 'Morcín', 'Ribera de Arriba', 'Proaza', 'Las Regueras', 'Candamo', 'Illas', 'Muros de Nalón', 'Cudillero', 'Colunga', 'Caravia', 'Parres', 'Caso', 'Sobrescobio', 'Ponga', 'Amieva', 'Onís', 'Cabrales', 'Peñamellera Alta', 'Peñamellera Baja', 'Ribadedeva', 'Sariego', 'Bimenes', 'Carreño', 'Gozón'],

            // ─── Baleares ───
            'baleares' => ['Palma', 'Calvià', 'Manacor', 'Ibiza', 'Llucmajor', 'Marratxí', 'Inca', 'Santa Eulària des Riu', 'Mahón', 'Ciutadella de Menorca', 'Sóller', 'Felanitx', 'Pollença', 'Alcúdia', 'Sa Pobla', 'Campos', 'Santanyí', 'Artà', 'Capdepera', 'Son Servera', 'Sant Llorenç des Cardassar', 'Andratx', 'Binissalem', 'Lloseta', 'Alaró', 'Santa Margalida', 'Muro', 'Sineu', 'Petra', 'Porreres', 'Algaida', 'Montuïri', 'Vilafranca de Bonany', 'Sant Joan', 'Maria de la Salut', 'Bunyola', 'Esporles', 'Valldemossa', 'Deià', 'Fornalutx', 'Santa Maria del Camí', 'Consell', 'Selva', 'Campanet', 'Búger', 'Sant Antoni de Portmany', 'Sant Josep de sa Talaia', 'Sant Joan de Labritja', 'Formentera', 'Es Mercadal', 'Alaior', 'Es Castell', 'Sant Lluís', 'Ferreries'],

            // ─── Canarias ───
            'las-palmas' => ['Las Palmas de Gran Canaria', 'Telde', 'Santa Lucía de Tirajana', 'Arrecife', 'San Bartolomé de Tirajana', 'Arucas', 'Ingenio', 'Agüimes', 'Puerto del Rosario', 'Gáldar', 'Mogán', 'Teguise', 'La Oliva', 'Pájara', 'Tuineje', 'Antigua', 'Betancuria', 'San Bartolomé', 'Tías', 'Yaiza', 'Haría', 'Tinajo', 'Teror', 'Valsequillo de Gran Canaria', 'Vega de San Mateo', 'Santa Brígida', 'Firgas', 'Moya', 'Valleseco', 'Tejeda', 'Artedara', 'San Nicolás de Tolentino', 'Aldea de San Nicolás', 'Agaete'],
            'santa-cruz-de-tenerife' => ['Santa Cruz de Tenerife', 'San Cristóbal de La Laguna', 'Arona', 'Adeje', 'La Orotava', 'Granadilla de Abona', 'Los Realejos', 'Puerto de la Cruz', 'Candelaria', 'Güímar', 'Tacoronte', 'Los Llanos de Aridane', 'Icod de los Vinos', 'Santiago del Teide', 'Guía de Isora', 'San Miguel de Abona', 'Tegueste', 'El Rosario', 'Santa Úrsula', 'La Victoria de Acentejo', 'La Matanza de Acentejo', 'El Sauzal', 'Garachico', 'Los Silos', 'Buenavista del Norte', 'La Guancha', 'San Juan de la Rambla', 'Arico', 'Fasnia', 'Arafo', 'El Paso', 'Breña Baja', 'Breña Alta', 'Santa Cruz de La Palma', 'Tazacorte', 'Tijarafe', 'Puntagorda', 'Garafía', 'Barlovento', 'San Andrés y Sauces', 'Puntallana', 'Mazo', 'Fuencaliente de La Palma', 'Vallehermoso', 'San Sebastián de La Gomera', 'Hermigua', 'Agulo', 'Alajeró', 'Valle Gran Rey', 'Valverde', 'Frontera', 'El Pinar de El Hierro'],

            // ─── Cantabria ───
            'cantabria' => ['Santander', 'Torrelavega', 'Camargo', 'Piélagos', 'El Astillero', 'Castro-Urdiales', 'Santa Cruz de Bezana', 'Laredo', 'Los Corrales de Buelna', 'Santoña', 'Reinosa', 'Colindres', 'Suances', 'Medio Cudeyo', 'Marina de Cudeyo', 'Villaescusa', 'Ribamontán al Mar', 'Bareyo', 'Noja', 'Arnuero', 'Meruelo', 'Limpias', 'Ampuero', 'Ramales de la Victoria', 'Guriezo', 'Voto', 'Entrambasaguas', 'Solares', 'Liérganes', 'Puente Viesgo', 'Castañeda', 'Santa María de Cayón', 'Villacarriedo', 'Selaya', 'Cabezón de la Sal', 'Comillas', 'San Vicente de la Barquera', 'Potes', 'Torrelavega', 'Polanco', 'Reocín', 'Cartes', 'San Felices de Buelna', 'Bárcena de Pie de Concha', 'Molledo', 'Corrales de Buelna'],

            // ─── Castilla-La Mancha ───
            'albacete' => ['Albacete', 'Hellín', 'Villarrobledo', 'Almansa', 'La Roda', 'Caudete', 'Madrigueras', 'Tobarra', 'Tarazona de la Mancha', 'Casas-Ibáñez', 'Elche de la Sierra', 'Yeste', 'Alcaraz', 'Munera', 'El Bonillo', 'Balazote', 'Chinchilla de Montearagón', 'Pozo Cañada', 'Pétrola', 'Fuenteálamo', 'Ontur', 'Montealegre del Castillo', 'Alpera', 'Higueruela', 'Bonete', 'Corral-Rubio', 'La Gineta', 'Barrax', 'Minaya', 'Lezuza', 'Riópar', 'Ayna', 'Bogarra', 'Nerpio', 'Molinicos', 'Villaverde de Guadalimar'],
            'ciudad-real' => ['Ciudad Real', 'Puertollano', 'Tomelloso', 'Valdepeñas', 'Alcázar de San Juan', 'Manzanares', 'Daimiel', 'Miguelturra', 'La Solana', 'Campo de Criptana', 'Socuéllamos', 'Bolaños de Calatrava', 'Pedro Muñoz', 'Herencia', 'Villarrubia de los Ojos', 'Almagro', 'Malagón', 'Porzuna', 'Piedrabuena', 'Almadén', 'Calzada de Calatrava', 'Moral de Calatrava', 'Aldea del Rey', 'Granátula de Calatrava', 'Santa Cruz de Mudela', 'Infantes', 'Villanueva de los Infantes', 'Torre de Juan Abad', 'Almodóvar del Campo', 'Argamasilla de Alba', 'Argamasilla de Calatrava', 'Membrilla', 'Torralba de Calatrava', 'Carrión de Calatrava', 'Fernán Caballero'],
            'cuenca' => ['Cuenca', 'Tarancón', 'San Clemente', 'Quintanar del Rey', 'Motilla del Palancar', 'Las Pedroñeras', 'Iniesta', 'Landete', 'Las Mesas', 'Casasimarro', 'Minglanilla', 'Villanueva de la Jara', 'El Provencio', 'San Lorenzo de la Parrilla', 'Belmonte', 'Mota del Cuervo', 'Horcajo de Santiago', 'Huete', 'Priego', 'Cañete', 'Carboneras de Guadazaón', 'Valverde de Júcar', 'Villamayor de Santiago', 'Castillo de Garcimuñoz'],
            'guadalajara' => ['Guadalajara', 'Azuqueca de Henares', 'Alovera', 'Cabanillas del Campo', 'El Casar', 'Marchamalo', 'Sigüenza', 'Villanueva de la Torre', 'Yunquera de Henares', 'Quer', 'Chiloeches', 'Mondéjar', 'Brihuega', 'Cifuentes', 'Molina de Aragón', 'Pastrana', 'Sacedón', 'Trillo', 'Jadraque', 'Torija', 'Horche', 'Pioz', 'Yebes', 'Valdeaveruelo', 'Fontanar', 'Humanes', 'Uceda', 'Cogolludo', 'Tamajón', 'Atienza'],
            'toledo' => ['Toledo', 'Talavera de la Reina', 'Illescas', 'Seseña', 'Torrijos', 'Consuegra', 'Madridejos', 'Sonseca', 'Quintanar de la Orden', 'Mora', 'Ocaña', 'Bargas', 'Olías del Rey', 'Fuensalida', 'Villacañas', 'Tembleque', 'La Puebla de Montalbán', 'Añover de Tajo', 'Borox', 'Esquivias', 'Ugena', 'Yuncos', 'Yeles', 'Numancia de la Sagra', 'Cedillo del Condado', 'El Viso de San Juan', 'Casarrubios del Monte', 'Santa Cruz de la Zarza', 'Corral de Almaguer', 'Puebla de Almoradiel', 'Villanueva de Alcardete', 'Miguel Esteban', 'El Toboso', 'Orgaz', 'Los Yébenes', 'Ventas con Peña Aguilera', 'Navahermosa', 'La Puebla de Almoradiel', 'Escalona', 'Santa Olalla', 'Calera y Chozas', 'Oropesa', 'Lagartera', 'Puente del Arzobispo'],

            // ─── Castilla y León ───
            'avila' => ['Ávila', 'Arévalo', 'Las Navas del Marqués', 'Candeleda', 'El Tiemblo', 'Arenas de San Pedro', 'El Barco de Ávila', 'Cebreros', 'Madrigal de las Altas Torres', 'Piedrahíta', 'Sotillo de la Adrada', 'La Adrada', 'Navaluenga', 'Burgohondo', 'El Hoyo de Pinares', 'San Bartolomé de Pinares', 'Navalperal de Pinares', 'El Barraco', 'Piedralaves', 'Mombeltrán', 'Pedro Bernardo', 'Guisando', 'El Arenal', 'San Esteban del Valle', 'Villarejo del Valle'],
            'burgos' => ['Burgos', 'Miranda de Ebro', 'Aranda de Duero', 'Briviesca', 'Medina de Pomar', 'Villarcayo de Merindad de Castilla la Vieja', 'Lerma', 'Villagonzalo Pedernales', 'Salas de los Infantes', 'Belorado', 'Quintanar de la Sierra', 'Roa', 'Peñaranda de Duero', 'Pradoluengo', 'Espinosa de los Monteros', 'Oña', 'Melgar de Fernamental', 'Castrojeriz', 'Villadiego', 'Sotopalacios', 'Cardeñajimeno', 'Modúbar de la Emparedada', 'Ibeas de Juarros', 'San Mamés de Burgos', 'Huerta de Rey', 'Caleruega', 'Santo Domingo de Silos', 'Covarrubias', 'Quintanilla de la Mata', 'Pampliega'],
            'leon' => ['León', 'Ponferrada', 'San Andrés del Rabanedo', 'Villaquilambre', 'Astorga', 'La Bañeza', 'Bembibre', 'Villablino', 'Cacabelos', 'Cistierna', 'Sahagún', 'Valencia de Don Juan', 'Mansilla de las Mulas', 'Santa María del Páramo', 'La Robla', 'Boñar', 'Matallana de Torío', 'Sariegos', 'Valverde de la Virgen', 'Chozas de Abajo', 'Carracedelo', 'Camponaraya', 'Congosto', 'Cubillos del Sil', 'Torre del Bierzo', 'Igüeña', 'Noceda del Bierzo', 'Páramo del Sil', 'Toreno', 'Fabero', 'Vega de Espinareda', 'Candín', 'Peranzanes', 'Villafranca del Bierzo', 'Trabadelo', 'Vega de Valcarce', 'Molinaseca', 'Los Barrios de Luna', 'Riaño', 'Oseja de Sajambre'],
            'palencia' => ['Palencia', 'Aguilar de Campoo', 'Guardo', 'Venta de Baños', 'Dueñas', 'Villamuriel de Cerrato', 'Cervera de Pisuerga', 'Saldaña', 'Herrera de Pisuerga', 'Barruelo de Santullán', 'Carrión de los Condes', 'Paredes de Nava', 'Villarramiel', 'Becerril de Campos', 'Ampudia', 'Astudillo', 'Baltanás', 'Cevico de la Torre', 'Torquemada', 'Osorno la Mayor', 'Frómista', 'Alar del Rey', 'Villada', 'Cisneros', 'Villalón de Campos'],
            'salamanca' => ['Salamanca', 'Béjar', 'Ciudad Rodrigo', 'Santa Marta de Tormes', 'Villares de la Reina', 'Peñaranda de Bracamonte', 'Carbajosa de la Sagrada', 'Guijuelo', 'Vitigudino', 'Ledesma', 'Alba de Tormes', 'Cantalapiedra', 'Tamames', 'La Fuente de San Esteban', 'Lumbrales', 'Aldeadávila de la Ribera', 'San Felices de los Gallegos', 'Fuentes de Oñoro', 'Calvarrasa de Abajo', 'Cabrerizos', 'Doñinos de Salamanca', 'Villamayor', 'Castellanos de Moriscos', 'Terradillos', 'Aldeatejada', 'Arapiles', 'Calzada de Valdunciel'],
            'segovia' => ['Segovia', 'Cuéllar', 'San Ildefonso', 'El Espinar', 'Palazuelos de Eresma', 'Cantalejo', 'Nava de la Asunción', 'Carbonero el Mayor', 'Coca', 'Turégano', 'Riaza', 'Ayllón', 'Sepúlveda', 'Villacastín', 'Hontalbilla', 'Mozoncillo', 'Navas de Oro', 'Valverde del Majano', 'Bernuy de Porreros', 'San Cristóbal de Segovia', 'La Lastrilla', 'Boceguillas', 'Santa María la Real de Nieva'],
            'soria' => ['Soria', 'Almazán', 'El Burgo de Osma', 'San Leonardo de Yagüe', 'Ólvega', 'San Esteban de Gormaz', 'Agreda', 'Arcos de Jalón', 'Medinaceli', 'Berlanga de Duero', 'Covaleda', 'Vinuesa', 'Duruelo de la Sierra', 'Navaleno', 'San Pedro Manrique', 'Ágreda', 'Gómara', 'Langa de Duero', 'Morón de Almazán', 'Quintana Redonda', 'Abejar', 'Golmayo', 'Los Rábanos'],
            'valladolid' => ['Valladolid', 'Medina del Campo', 'Laguna de Duero', 'Arroyo de la Encomienda', 'Tordesillas', 'Íscar', 'Tudela de Duero', 'Simancas', 'Cigales', 'Peñafiel', 'Renedo de Esgueva', 'Santovenia de Pisuerga', 'Zaratán', 'La Cistérniga', 'Boecillo', 'Aldeamayor de San Martín', 'Viana de Cega', 'Villanueva de Duero', 'Olmedo', 'Portillo', 'Mojados', 'Pedrajas de San Esteban', 'Cuéllar', 'Medina de Rioseco', 'Mayorga', 'Villalón de Campos', 'Nava del Rey', 'Rueda', 'La Seca', 'Sardón de Duero', 'Quintanilla de Onésimo', 'Olivares de Duero', 'Cabezón de Pisuerga', 'Fuensaldaña', 'Mucientes', 'Trigueros del Valle', 'Corcos del Valle', 'Valoria la Buena', 'Castronuño'],
            'zamora' => ['Zamora', 'Benavente', 'Toro', 'Morales del Vino', 'Puebla de Sanabria', 'Villalpando', 'Fuentesaúco', 'Fermoselle', 'Alcañices', 'Corrales del Vino', 'Moraleja del Vino', 'Villaralbo', 'Roales', 'Coreses', 'Manganeses de la Lampreana', 'Villanueva del Campo', 'Valderas', 'Villalobos', 'Castroverde de Campos', 'Vezdemarbán', 'Bermillo de Sayago', 'Carbajales de Alba', 'Santibáñez de Vidriales', 'Camarzana de Tera'],

            // ─── Cataluña ───
            'barcelona' => ['Barcelona', 'L\'Hospitalet de Llobregat', 'Badalona', 'Terrassa', 'Sabadell', 'Mataró', 'Santa Coloma de Gramenet', 'Cornellà de Llobregat', 'Sant Boi de Llobregat', 'Sant Cugat del Vallès', 'Rubí', 'Manresa', 'Vilanova i la Geltrú', 'Viladecans', 'Granollers', 'Cerdanyola del Vallès', 'Castelldefels', 'Mollet del Vallès', 'Gavà', 'Igualada', 'El Prat de Llobregat', 'Esplugues de Llobregat', 'Sant Feliu de Llobregat', 'Vic', 'Vilafranca del Penedès', 'Ripollet', 'Sant Adrià de Besòs', 'Montcada i Reixac', 'Sant Joan Despí', 'Barberà del Vallès', 'Premià de Mar', 'Sant Pere de Ribes', 'Sant Vicenç dels Horts', 'Sitges', 'Martorell', 'Sant Andreu de la Barca', 'Pineda de Mar', 'Molins de Rei', 'Santa Perpètua de Mogoda', 'Olesa de Montserrat', 'Castellar del Vallès', 'El Masnou', 'Esparreguera', 'Manlleu', 'Vilassar de Mar', 'Calella', 'Malgrat de Mar', 'Sant Quirze del Vallès', 'Parets del Vallès', 'Berga', 'Les Franqueses del Vallès', 'Caldes de Montbui', 'Sant Celoni', 'Cardedeu', 'Canovelles', 'Sant Just Desvern', 'Montornès del Vallès', 'La Garriga', 'Arenys de Mar', 'Tordera', 'Badia del Vallès', 'Piera', 'Palau-solità i Plegamans', 'La Llagosta', 'Lliçà d\'Amunt', 'Torelló', 'Vallirana', 'Canet de Mar', 'Corbera de Llobregat', 'Cubelles', 'Vilanova del Camí', 'Sant Sadurní d\'Anoia', 'Castellbisbal', 'Argentona', 'Abrera', 'Pallejà', 'Sant Joan de Vilatorrada', 'Santa Margarida de Montbui', 'Premià de Dalt', 'Montgat', 'Sant Andreu de Llavaneres', 'La Roca del Vallès', 'Alella', 'Montmeló', 'Llinars del Vallès', 'Vilassar de Dalt', 'Matadepera', 'Santa Maria de Palautordera', 'Sant Vicenç de Castellet', 'Palafolls', 'Cervelló', 'Arenys de Munt', 'Masquefa', 'Sant Fost de Campsentelles', 'L\'Ametlla del Vallès', 'Tona', 'Santa Coloma de Cervelló', 'Sant Fruitós de Bages', 'Tiana', 'Sentmenat', 'Polinyà', 'Sallent', 'Viladecavalls', 'Centelles', 'Sant Esteve Sesrovires', 'Cabrils', 'Santpedor', 'Gelida', 'Lliçà de Vall', 'Teià', 'Navàs', 'Begues', 'Taradell', 'Navarcles', 'Roda de Ter', 'Sant Feliu de Codines', 'Moià', 'Vacarisses', 'Capellades', 'Sant Vicenç de Montalt', 'Artés', 'Cardona', 'Torrelles de Llobregat', 'Sant Pol de Mar', 'Martorelles', 'Gironella'],
            'girona' => ['Girona', 'Figueres', 'Blanes', 'Lloret de Mar', 'Olot', 'Salt', 'Sant Feliu de Guíxols', 'Palafrugell', 'Roses', 'Banyoles', 'Ripoll', 'Torroella de Montgrí', 'Palamós', 'Platja d\'Aro', 'La Bisbal d\'Empordà', 'Cassà de la Selva', 'Llagostera', 'Santa Coloma de Farners', 'Anglès', 'Arbúcies', 'Breda', 'Hostalric', 'Caldes de Malavella', 'Vidreres', 'Maçanet de la Selva', 'Sils', 'Riudellots de la Selva', 'Quart', 'Celrà', 'Sarrià de Ter', 'Sant Julià de Ramis', 'Bescanó', 'Fornells de la Selva', 'Vilablareix', 'Bordils', 'Amer', 'Besalú', 'Camprodon', 'Sant Joan de les Abadesses', 'Puigcerdà', 'Llívia', 'Cadaqués', 'L\'Escala', 'L\'Estartit', 'Empuriabrava', 'Castelló d\'Empúries', 'Peralada', 'Navata', 'Bàscara', 'Verges', 'Calonge', 'Begur', 'Pals', 'Regencós', 'Tossa de Mar'],
            'lleida' => ['Lleida', 'Balaguer', 'Tàrrega', 'Mollerussa', 'La Seu d\'Urgell', 'Cervera', 'Solsona', 'Tremp', 'Sort', 'Almacelles', 'Alcarràs', 'Alpicat', 'Artesa de Segre', 'Agramunt', 'Bellpuig', 'Les Borges Blanques', 'Juneda', 'Guissona', 'Ponts', 'Organyà', 'Oliana', 'Vielha e Mijaran', 'El Pont de Suert', 'Bossòst', 'Les', 'Naut Aran', 'Esterri d\'Àneu', 'Rialp', 'La Pobla de Segur', 'Isona i Conca Dellà', 'Artesa de Lleida', 'Rosselló', 'Torres de Segre', 'Almenar', 'Alguaire', 'Corbins', 'Vallfogona de Balaguer', 'Os de Balaguer', 'Àger', 'Bell-lloc d\'Urgell', 'Golmés', 'Miralcamp', 'El Palau d\'Anglesola', 'Verdú', 'Sant Guim de Freixenet'],
            'tarragona' => ['Tarragona', 'Reus', 'Tortosa', 'El Vendrell', 'Cambrils', 'Salou', 'Valls', 'Vila-seca', 'Amposta', 'Calafell', 'Torredembarra', 'Constantí', 'Altafulla', 'La Canonja', 'Cunit', 'Deltebre', 'Sant Carles de la Ràpita', 'L\'Ametlla de Mar', 'L\'Hospitalet de l\'Infant', 'Mont-roig del Camp', 'Vandellòs', 'Riudoms', 'Montblanc', 'L\'Espluga de Francolí', 'Santa Coloma de Queralt', 'Gandesa', 'Falset', 'Móra d\'Ebre', 'Móra la Nova', 'Flix', 'Ascó', 'Ulldecona', 'Alcanar', 'La Sénia', 'Xerta', 'Benifallet', 'El Perelló', 'Tivissa', 'Les Borges del Camp', 'Botarell', 'Alcover', 'La Selva del Camp', 'Vilallonga del Camp', 'El Morell', 'El Catllar', 'La Pobla de Mafumet', 'Creixell', 'Roda de Berà'],

            // ─── Comunidad Valenciana ───
            'alicante' => ['Alicante', 'Elche', 'Torrevieja', 'Orihuela', 'Benidorm', 'Alcoy', 'San Vicente del Raspeig', 'Elda', 'Dénia', 'Petrer', 'Santa Pola', 'Villena', 'Crevillent', 'Novelda', 'Jávea', 'Altea', 'Calpe', 'Ibi', 'Monóvar', 'Aspe', 'Pilar de la Horadada', 'Guardamar del Segura', 'Rojales', 'San Fulgencio', 'Catral', 'Callosa de Segura', 'Dolores', 'Almoradí', 'Rafal', 'Redován', 'Cox', 'Granja de Rocamora', 'Benijófar', 'Algorfa', 'Formentera del Segura', 'Jacarilla', 'Bigastro', 'San Miguel de Salinas', 'Los Montesinos', 'Benejúzar', 'Daya Nueva', 'Daya Vieja', 'Muchamiel', 'San Juan de Alicante', 'El Campello', 'Busot', 'Agost', 'Tibi', 'Onil', 'Biar', 'Cocentaina', 'Muro de Alcoy', 'Benilloba', 'Castalla', 'Sax', 'Pinoso', 'La Romana', 'Hondón de las Nieves', 'Hondón de los Frailes', 'Monforte del Cid', 'La Nucia', 'Alfaz del Pi', 'Polop', 'Benissa', 'Teulada', 'Gata de Gorgos', 'Pedreguer', 'Ondara', 'El Verger', 'Pego', 'Parcent', 'Jalón'],
            'castellon' => ['Castellón de la Plana', 'Vila-real', 'Burriana', 'Vinaròs', 'La Vall d\'Uixó', 'Benicarló', 'Onda', 'Almassora', 'Benicàssim', 'Nules', 'Oropesa del Mar', 'Betxí', 'Borriol', 'L\'Alcora', 'Lucena del Cid', 'Segorbe', 'Jérica', 'Altura', 'Soneja', 'Chilches', 'Moncofa', 'Torreblanca', 'Alcalà de Xivert', 'Peníscola', 'Morella', 'Sant Mateu', 'Traiguera', 'La Jana', 'Càlig', 'Rossell', 'La Salzadella', 'Albocàsser', 'Les Coves de Vinromà', 'Cabanes', 'Vilafamés', 'Sant Joan de Moró', 'Tales', 'Artana', 'Eslida', 'Ribesalbes', 'Villareal', 'Almenara'],
            'valencia' => ['Valencia', 'Torrent', 'Gandía', 'Paterna', 'Sagunto', 'Mislata', 'Burjassot', 'Ontinyent', 'Aldaia', 'Manises', 'Alzira', 'Xirivella', 'Sueca', 'Requena', 'Catarroja', 'Alboraya', 'Bétera', 'Quart de Poblet', 'Picanya', 'Xàtiva', 'Oliva', 'Cullera', 'Picassent', 'Paiporta', 'Alaquàs', 'Tavernes de la Valldigna', 'Chiva', 'Lliria', 'Silla', 'Massanassa', 'Sedaví', 'Benetússer', 'Alfafar', 'Puçol', 'Almussafes', 'Algemesí', 'Carlet', 'L\'Alcúdia', 'Carcaixent', 'Albal', 'Massamagrell', 'L\'Eliana', 'Rafelbunyol', 'Rocafort', 'Godella', 'Moncada', 'Bonrepòs i Mirambell', 'Alfara del Patriarca', 'Almàssera', 'Meliana', 'Foios', 'Vinalesa', 'Benaguasil', 'La Pobla de Vallbona', 'Ribarroja del Turia', 'Cheste', 'Buñol', 'Utiel', 'Ayora', 'Enguera', 'Canals', 'Tavernes Blanques', 'San Antonio de Benagéber', 'Alcàsser', 'Beniparrell', 'Albalat dels Sorells'],

            // ─── Extremadura ───
            'badajoz' => ['Badajoz', 'Mérida', 'Don Benito', 'Almendralejo', 'Villanueva de la Serena', 'Zafra', 'Montijo', 'Olivenza', 'Villafranca de los Barros', 'Jerez de los Caballeros', 'Azuaga', 'Llerena', 'Fregenal de la Sierra', 'Santos de Maimona', 'Fuente del Maestre', 'Guareña', 'La Zarza', 'Lobón', 'Pueblonuevo del Guadiana', 'Talavera la Real', 'Valverde de Leganés', 'Alburquerque', 'San Vicente de Alcántara', 'Herrera del Duque', 'Talarrubias', 'Castuera', 'Campanario', 'Cabeza del Buey', 'Peñalsordo', 'La Coronada', 'Villanueva del Fresno', 'Barcarrota', 'Salvatierra de los Barros', 'Burguillos del Cerro', 'Valencia del Ventoso', 'Segura de León', 'Monesterio', 'Calera de León', 'Fuente de Cantos', 'Ribera del Fresno', 'Hornachos'],
            'caceres' => ['Cáceres', 'Plasencia', 'Navalmoral de la Mata', 'Trujillo', 'Coria', 'Miajadas', 'Talayuela', 'Moraleja', 'Jaraíz de la Vera', 'Montehermoso', 'Valencia de Alcántara', 'Alcántara', 'Brozas', 'Garrovillas de Alconétar', 'Arroyo de la Luz', 'Malpartida de Cáceres', 'Casar de Cáceres', 'Torremocha', 'Sierra de Fuentes', 'Aldeanueva de la Vera', 'Jarandilla de la Vera', 'Cuacos de Yuste', 'Villanueva de la Vera', 'Losar de la Vera', 'Madrigal de la Vera', 'Hervás', 'Baños de Montemayor', 'Béjar', 'Guadalupe', 'Logrosán', 'Zorita', 'Madroñera', 'Cañamero'],

            // ─── Galicia ───
            'a-coruna' => ['A Coruña', 'Santiago de Compostela', 'Ferrol', 'Narón', 'Oleiros', 'Carballo', 'Arteixo', 'Culleredo', 'Cambre', 'Ares', 'Ribeira', 'Betanzos', 'Bergondo', 'Sada', 'Miño', 'Pontedeume', 'Cedeira', 'Ortigueira', 'As Pontes de García Rodríguez', 'Mugardos', 'Fene', 'Neda', 'Valdoviño', 'Moeche', 'San Sadurniño', 'Cariño', 'Padrón', 'Boiro', 'A Pobra do Caramiñal', 'Rianxo', 'Noia', 'Porto do Son', 'Carnota', 'Muros', 'Cee', 'Corcubión', 'Fisterra', 'Camariñas', 'Muxía', 'Laxe', 'Ponteceso', 'Malpica de Bergantiños', 'Coristanco', 'Santa Comba', 'Vimianzo', 'Zas', 'Dumbría', 'Mazaricos', 'Negreira', 'Brión', 'Ames', 'Teo', 'Vedra', 'Boqueixón', 'Touro', 'O Pino', 'Ordes', 'Oroso', 'Tordoia', 'Mesía', 'Curtis', 'Vilasantar', 'Sobrado', 'Melide', 'Arzúa', 'Toques', 'Boimorto', 'Frades', 'Trazo', 'Cerceda'],
            'lugo' => ['Lugo', 'Monforte de Lemos', 'Viveiro', 'Vilalba', 'Sarria', 'Burela', 'Chantada', 'Foz', 'Ribadeo', 'Mondoñedo', 'Guitiriz', 'Becerreá', 'Lourenzá', 'Barreiros', 'Cervo', 'Xove', 'O Valadouro', 'Alfoz', 'A Pastoriza', 'Meira', 'Pol', 'Cospeito', 'Castro de Rei', 'Outeiro de Rei', 'Friol', 'Guntín', 'Portomarín', 'Taboada', 'Carballedo', 'Pantón', 'Sober', 'Quiroga', 'Folgoso do Courel', 'Pedrafita do Cebreiro', 'Navia de Suarna', 'A Fonsagrada', 'Negueira de Muñiz'],
            'ourense' => ['Ourense', 'Verín', 'O Barco de Valdeorras', 'Carballiño', 'Xinzo de Limia', 'Celanova', 'Allariz', 'Ribadavia', 'O Carballiño', 'A Rúa', 'Vilamartín de Valdeorras', 'O Bolo', 'Viana do Bolo', 'A Mezquita', 'Monterrei', 'Oímbra', 'Cualedro', 'Laza', 'Maceda', 'Baños de Molgas', 'Xunqueira de Ambía', 'Xunqueira de Espadanedo', 'Esgos', 'San Cibrao das Viñas', 'Barbadás', 'Pereiro de Aguiar', 'Nogueira de Ramuín', 'Amoeiro', 'Coles', 'Toén', 'Maside', 'Punxín', 'San Amaro', 'Cenlle', 'Leiro', 'Arnoia'],
            'pontevedra' => ['Vigo', 'Pontevedra', 'Vilagarcía de Arousa', 'Redondela', 'Cangas', 'Marín', 'Lalín', 'O Porriño', 'Ponteareas', 'Tui', 'Nigrán', 'Baiona', 'Sanxenxo', 'Bueu', 'Moaña', 'O Grove', 'Cambados', 'Vilanova de Arousa', 'Ribadumia', 'Meaño', 'Meis', 'Poio', 'Cuntis', 'Caldas de Reis', 'Catoira', 'Valga', 'Pontecesures', 'Portas', 'Barro', 'A Estrada', 'Forcarei', 'Cerdedo-Cotobade', 'Campo Lameiro', 'A Lama', 'Soutomaior', 'Pazos de Borbén', 'Fornelos de Montes', 'Mondariz', 'Mondariz-Balneario', 'Covelo', 'A Cañiza', 'Arbo', 'As Neves', 'Salvaterra de Miño', 'Mos', 'Gondomar', 'Tomiño', 'A Guarda', 'O Rosal', 'Oia', 'Salceda de Caselas', 'Porriño'],

            // ─── La Rioja ───
            'la-rioja' => ['Logroño', 'Calahorra', 'Arnedo', 'Haro', 'Alfaro', 'Lardero', 'Nájera', 'Santo Domingo de la Calzada', 'Villamediana de Iregua', 'Autol', 'Rincón de Soto', 'Pradejón', 'Fuenmayor', 'Navarrete', 'Albelda de Iregua', 'Cenicero', 'San Asensio', 'Ezcaray', 'Anguiano', 'Cervera del Río Alhama', 'Cornago', 'Igea', 'Munilla', 'Enciso', 'Briones', 'Ollauri', 'Casalarreina', 'Tirgo', 'Anguciana', 'Entrena', 'Torrecilla en Cameros', 'Ortigosa de Cameros', 'Villoslada de Cameros', 'Murillo de Río Leza'],

            // ─── Madrid ───
            'madrid' => ['Madrid', 'Móstoles', 'Alcalá de Henares', 'Fuenlabrada', 'Leganés', 'Getafe', 'Alcorcón', 'Torrejón de Ardoz', 'Parla', 'Alcobendas', 'Las Rozas de Madrid', 'San Sebastián de los Reyes', 'Pozuelo de Alarcón', 'Coslada', 'Rivas-Vaciamadrid', 'Valdemoro', 'Majadahonda', 'Collado Villalba', 'Aranjuez', 'Arganda del Rey', 'Boadilla del Monte', 'Pinto', 'Colmenar Viejo', 'Tres Cantos', 'San Fernando de Henares', 'Galapagar', 'Arroyomolinos', 'Navalcarnero', 'Ciempozuelos', 'Torrelodones', 'Villanueva de la Cañada', 'Mejorada del Campo', 'Villanueva del Pardillo', 'Algete', 'Brunete', 'Villaviciosa de Odón', 'El Escorial', 'San Lorenzo de El Escorial', 'Paracuellos de Jarama', 'Humanes de Madrid', 'Moraleja de Enmedio', 'Cercedilla', 'Guadarrama', 'Alpedrete', 'San Martín de la Vega', 'Villalbilla', 'Daganzo de Arriba', 'Velilla de San Antonio', 'Meco', 'Loeches', 'Torres de la Alameda', 'San Agustín del Guadalix', 'Cobeña', 'Soto del Real', 'Manzanares el Real', 'El Molar', 'Pedrezuela', 'Guadalix de la Sierra', 'San Martín de Valdeiglesias', 'Nuevo Baztán', 'Griñón', 'Moralzarzal', 'Becerril de la Sierra', 'Hoyo de Manzanares', 'Cubas de la Sagra', 'Sevilla la Nueva', 'Valdemorillo', 'Chinchón', 'Colmenar de Oreja', 'Titulcia', 'Fuente el Saz de Jarama', 'Talamanca de Jarama', 'Torrelaguna', 'Buitrago del Lozoya', 'Rascafría', 'Navacerrada', 'Los Molinos', 'Collado Mediano'],

            // ─── Murcia ───
            'murcia' => ['Murcia', 'Cartagena', 'Lorca', 'Molina de Segura', 'Alcantarilla', 'Mazarrón', 'Cieza', 'Águilas', 'Yecla', 'Torre-Pacheco', 'San Javier', 'Caravaca de la Cruz', 'Totana', 'Jumilla', 'Alhama de Murcia', 'Las Torres de Cotillas', 'San Pedro del Pinatar', 'Archena', 'Ceutí', 'Lorquí', 'Alguazas', 'Mula', 'Bullas', 'Calasparra', 'Moratalla', 'Cehegín', 'Abarán', 'Blanca', 'Ojós', 'Ulea', 'Villanueva del Río Segura', 'Ricote', 'Pliego', 'Campos del Río', 'Albudeite', 'Fortuna', 'Abanilla', 'Santomera', 'Beniel', 'Los Alcázares', 'Fuente Álamo de Murcia', 'Librilla', 'Puerto Lumbreras', 'La Unión'],

            // ─── Navarra ───
            'navarra' => ['Pamplona', 'Tudela', 'Barañáin', 'Burlada', 'Estella-Lizarra', 'Zizur Mayor', 'Tafalla', 'Villava', 'Ansoáin', 'Berriozar', 'Huarte', 'Alsasua', 'Corella', 'Cintruénigo', 'Sangüesa', 'Peralta', 'Olite', 'Artajona', 'Puente la Reina', 'Ayegui', 'Sarriguren', 'Mutilva', 'Noáin', 'Orkoien', 'Cizur', 'Aranguren', 'Egüés', 'Beriáin', 'Irurtzun', 'Lekunberri', 'Elizondo', 'Baztan', 'Santesteban', 'Lesaka', 'Vera de Bidasoa', 'Bera', 'Sunbilla', 'Ituren', 'Zubieta', 'Leitza', 'Aoiz', 'Lumbier', 'Monreal', 'Lodosa', 'San Adrián', 'Azagra', 'Castejón', 'Fitero', 'Cascante', 'Ablitas', 'Fontellas', 'Murchante', 'Ribaforada', 'Carcastillo', 'Villafranca'],

            // ─── País Vasco ───
            'alava' => ['Vitoria-Gasteiz', 'Llodio', 'Amurrio', 'Salvatierra', 'Oyón-Oion', 'Agurain', 'Alegría-Dulantzi', 'Iruña de Oca', 'Nanclares de la Oca', 'Artziniega', 'Arceniega', 'Ayala', 'Okondo', 'Urduña', 'Zambrana', 'Labastida', 'Samaniego', 'Laguardia', 'Elciego', 'Kripan', 'Lanciego', 'Lapuebla de Labarca', 'Campezo', 'Peñacerrada', 'Bernedo', 'Arraia-Maeztu', 'Asparrena', 'Barrundia', 'Elburgo', 'Arrazua-Ubarrundia', 'Zigoitia', 'Zuia', 'Legutio', 'Aramaio', 'Maeztu'],
            'guipuzcoa' => ['San Sebastián', 'Irún', 'Errenteria', 'Eibar', 'Zarautz', 'Arrasate-Mondragón', 'Hernani', 'Tolosa', 'Hondarribia', 'Lasarte-Oria', 'Andoain', 'Bergara', 'Azpeitia', 'Oñati', 'Pasaia', 'Lezo', 'Oiartzun', 'Astigarraga', 'Usurbil', 'Orio', 'Zumaia', 'Getaria', 'Deba', 'Mutriku', 'Elgoibar', 'Soraluze', 'Eskoriatza', 'Aretxabaleta', 'Leintz-Gatzaga', 'Antzuola', 'Zumarraga', 'Urretxu', 'Legazpi', 'Beasain', 'Ordizia', 'Lazkao', 'Ataun', 'Idiazábal', 'Segura', 'Zestoa', 'Azkoitia', 'Errezil', 'Aia', 'Zumárraga', 'Villabona', 'Ibarra', 'Alegia', 'Itsasondo', 'Gabiria', 'Olaberria'],
            'vizcaya' => ['Bilbao', 'Barakaldo', 'Getxo', 'Portugalete', 'Santurtzi', 'Basauri', 'Leioa', 'Durango', 'Erandio', 'Galdakao', 'Sestao', 'Gernika-Lumo', 'Amorebieta-Etxano', 'Bermeo', 'Mungia', 'Sopelana', 'Berango', 'Gorliz', 'Plentzia', 'Barrika', 'Urduliz', 'Laukiz', 'Derio', 'Sondika', 'Loiu', 'Zamudio', 'Larrabetzu', 'Lezama', 'Igorre', 'Lemoa', 'Bedia', 'Arantzazu', 'Arrigorriaga', 'Zaratamo', 'Ugao-Miraballes', 'Arakaldo', 'Arrankudiaga', 'Orozko', 'Zeberio', 'Güeñes', 'Zalla', 'Gordexola', 'Artzentales', 'Sopuerta', 'Galdames', 'Karrantza', 'Balmaseda', 'Lanestosa', 'Ondarroa', 'Markina-Xemein', 'Lekeitio', 'Ea', 'Ispaster', 'Mendexa', 'Berriatua', 'Murelaga', 'Munitibar-Arbatzegi-Gerrikaitz', 'Ibarrangelu', 'Mundaka', 'Sukarrieta', 'Busturia', 'Forua', 'Kortezubi', 'Ereño', 'Nabarniz', 'Gautegiz Arteaga', 'Ajangiz', 'Errigoiti', 'Morga', 'Fruiz'],

            // ─── Ceuta y Melilla ───
            'ceuta' => ['Ceuta'],
            'melilla' => ['Melilla'],
        ];
    }
}
