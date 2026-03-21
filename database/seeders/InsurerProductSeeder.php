<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsurerProductSeeder extends Seeder
{
    /**
     * Seed products for major Spanish health insurers.
     */
    public function run(): void
    {
        $now = now();

        // Build a slug->id map for insurers
        $insurerIds = DB::table('insurers')->pluck('id', 'slug')->toArray();

        $products = [
            // ─── Adeslas (9 products) ───
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Privado',
                'slug' => 'adeslas-privado',
                'description' => 'Adeslas Privado es el seguro de salud más completo de SegurCaixa Adeslas. Incluye hospitalización, consultas con especialistas, pruebas diagnósticas, intervenciones quirúrgicas y acceso al cuadro médico más amplio de España sin listas de espera.',
                'meta_title' => 'Cuadro Médico Adeslas Privado 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Adeslas Privado 2026. Encuentra todos los médicos, especialistas, hospitales y centros incluidos en el plan Adeslas Privado.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Go',
                'slug' => 'adeslas-go',
                'description' => 'Adeslas Go es el seguro de salud básico y asequible de SegurCaixa Adeslas. Diseñado para quienes buscan una cobertura médica esencial con copago, incluye consultas, urgencias y pruebas diagnósticas básicas a un precio competitivo.',
                'meta_title' => 'Cuadro Médico Adeslas Go 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Adeslas Go 2026. Médicos, especialistas y centros de salud incluidos en el seguro básico Adeslas Go.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Classic',
                'slug' => 'adeslas-classic',
                'description' => 'Adeslas Classic es un seguro de salud de gama media de SegurCaixa Adeslas. Ofrece un equilibrio entre coberturas y precio, con acceso a un amplio cuadro médico, hospitalización, consultas con especialistas y pruebas diagnósticas.',
                'meta_title' => 'Cuadro Médico Adeslas Classic 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Adeslas Classic 2026. Encuentra médicos, especialistas y centros de salud del seguro Adeslas Classic.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Seniors',
                'slug' => 'adeslas-seniors',
                'description' => 'Adeslas Seniors es el seguro de salud de Adeslas diseñado específicamente para personas mayores de 60 años. Incluye coberturas adaptadas a las necesidades de la tercera edad, como geriatría, rehabilitación y acceso a especialistas sin listas de espera.',
                'meta_title' => 'Cuadro Médico Adeslas Seniors 2026 - Coberturas para Mayores',
                'meta_description' => 'Consulta el cuadro médico de Adeslas Seniors 2026. Médicos, especialistas y centros de salud para mayores de 60 años con Adeslas.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Seniors Ampliación',
                'slug' => 'adeslas-seniors-ampliacion',
                'description' => 'Adeslas Seniors Ampliación es la versión ampliada del seguro para mayores de Adeslas. Incluye coberturas adicionales respecto al plan Seniors estándar, como mayor número de sesiones de rehabilitación, óptica y prótesis.',
                'meta_title' => 'Cuadro Médico Adeslas Seniors Ampliación 2026 - Coberturas',
                'meta_description' => 'Consulta el cuadro médico de Adeslas Seniors Ampliación 2026. Coberturas ampliadas, médicos y centros para mayores de 60 años.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Dental',
                'slug' => 'adeslas-dental',
                'description' => 'Adeslas Dental es el seguro dental básico de SegurCaixa Adeslas. Incluye revisiones, limpiezas, empastes, extracciones y urgencias dentales, con acceso a una amplia red de clínicas dentales en toda España.',
                'meta_title' => 'Cuadro Médico Adeslas Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Adeslas Dental 2026. Encuentra clínicas dentales, dentistas y ortodoncistas incluidos en Adeslas Dental.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Dental Activa',
                'slug' => 'adeslas-dental-activa',
                'description' => 'Adeslas Dental Activa es un seguro dental con coberturas mejoradas de SegurCaixa Adeslas. Además de las coberturas básicas, incluye tratamientos de endodoncia, periodoncia y descuentos en ortodoncia e implantes dentales.',
                'meta_title' => 'Cuadro Médico Adeslas Dental Activa 2026 - Clínicas y Coberturas',
                'meta_description' => 'Consulta el cuadro dental de Adeslas Dental Activa 2026. Clínicas, dentistas y coberturas mejoradas del seguro dental Adeslas Activa.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Dental Familia',
                'slug' => 'adeslas-dental-familia',
                'description' => 'Adeslas Dental Familia es el seguro dental familiar de SegurCaixa Adeslas. Diseñado para cubrir a toda la familia, incluye coberturas dentales para adultos y niños, con revisiones, tratamientos preventivos y acceso a una amplia red de clínicas.',
                'meta_title' => 'Cuadro Médico Adeslas Dental Familia 2026 - Clínicas Familiares',
                'meta_description' => 'Consulta el cuadro dental de Adeslas Dental Familia 2026. Clínicas y dentistas para toda la familia incluidos en el seguro Adeslas Dental Familia.',
            ],
            [
                'insurer_slug' => 'adeslas',
                'name' => 'Adeslas Dental Óptima',
                'slug' => 'adeslas-dental-optima',
                'description' => 'Adeslas Dental Óptima es el seguro dental más completo de SegurCaixa Adeslas. Incluye todas las coberturas dentales básicas y avanzadas, con ortodoncia incluida, implantes con descuentos especiales y tratamientos estéticos dentales.',
                'meta_title' => 'Cuadro Médico Adeslas Dental Óptima 2026 - Coberturas Premium',
                'meta_description' => 'Consulta el cuadro dental de Adeslas Dental Óptima 2026. La cobertura dental más completa de Adeslas con ortodoncia, implantes y estética.',
            ],

            // ─── Sanitas (6 products) ───
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Más Salud',
                'slug' => 'sanitas-mas-salud',
                'description' => 'Sanitas Más Salud es el seguro de salud más completo de Sanitas. Ofrece acceso sin listas de espera a más de 40.000 profesionales, hospitales y centros propios, con coberturas integrales que incluyen hospitalización, diagnóstico y tratamiento.',
                'meta_title' => 'Cuadro Médico Sanitas Más Salud 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Sanitas Más Salud 2026. Encuentra médicos, especialistas y hospitales del seguro más completo de Sanitas.',
            ],
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Básico',
                'slug' => 'sanitas-basico',
                'description' => 'Sanitas Básico es un seguro de salud asequible con copago. Incluye consultas médicas, urgencias, pruebas diagnósticas y hospitalización a un precio reducido gracias a su sistema de copagos por uso.',
                'meta_title' => 'Cuadro Médico Sanitas Básico 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Sanitas Básico 2026. Médicos, especialistas y centros incluidos en el seguro de salud básico de Sanitas.',
            ],
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Dental',
                'slug' => 'sanitas-dental',
                'description' => 'Sanitas Dental es el seguro dental de referencia en España. Con acceso a las clínicas dentales Sanitas y a una amplia red de dentistas, incluye revisiones, limpiezas, empastes, extracciones y descuentos en tratamientos avanzados.',
                'meta_title' => 'Cuadro Médico Sanitas Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Sanitas 2026. Encuentra clínicas Sanitas Dental, dentistas y ortodoncistas en tu provincia.',
            ],
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Seniors',
                'slug' => 'sanitas-seniors',
                'description' => 'Sanitas Seniors es el seguro de salud de Sanitas diseñado para personas mayores. Ofrece coberturas adaptadas a la tercera edad con acceso a geriatría, rehabilitación, telemedicina y los centros propios de Sanitas.',
                'meta_title' => 'Cuadro Médico Sanitas Seniors 2026 - Coberturas para Mayores',
                'meta_description' => 'Consulta el cuadro médico de Sanitas Seniors 2026. Médicos, especialistas y centros para mayores incluidos en Sanitas Seniors.',
            ],
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Blua',
                'slug' => 'sanitas-blua',
                'description' => 'Sanitas Blua es el seguro de salud digital de Sanitas. Combina la asistencia presencial con herramientas digitales avanzadas: videoconsultas, chat médico, seguimiento de salud por app y acceso al cuadro médico completo de Sanitas.',
                'meta_title' => 'Cuadro Médico Sanitas Blua 2026 - Seguro Digital',
                'meta_description' => 'Consulta el cuadro médico de Sanitas Blua 2026. Médicos, centros y servicios digitales del seguro de salud digital de Sanitas.',
            ],
            [
                'insurer_slug' => 'sanitas',
                'name' => 'Sanitas Primero',
                'slug' => 'sanitas-primero',
                'description' => 'Sanitas Primero es un seguro de salud de gama media que ofrece un buen equilibrio entre coberturas y precio. Incluye consultas con especialistas, hospitalización, pruebas diagnósticas y servicios de telemedicina de Sanitas.',
                'meta_title' => 'Cuadro Médico Sanitas Primero 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Sanitas Primero 2026. Encuentra médicos, especialistas y centros del seguro Sanitas Primero.',
            ],

            // ─── Asisa (5 products) ───
            [
                'insurer_slug' => 'asisa',
                'name' => 'Asisa Integral',
                'slug' => 'asisa-integral',
                'description' => 'Asisa Integral es el seguro de salud más completo de ASISA. Ofrece cobertura sanitaria integral sin copagos, con acceso a hospitales HLA y miles de profesionales médicos en toda España, incluyendo hospitalización, cirugía y diagnóstico avanzado.',
                'meta_title' => 'Cuadro Médico Asisa Integral 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Asisa Integral 2026. Médicos, especialistas, hospitales HLA y centros incluidos en Asisa Integral.',
            ],
            [
                'insurer_slug' => 'asisa',
                'name' => 'Asisa Activa',
                'slug' => 'asisa-activa',
                'description' => 'Asisa Activa es un seguro de salud con copago de ASISA. Ofrece coberturas completas a un precio más reducido mediante un sistema de copagos moderados, con acceso al cuadro médico de ASISA y hospitales del grupo HLA.',
                'meta_title' => 'Cuadro Médico Asisa Activa 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Asisa Activa 2026. Médicos, especialistas y centros del seguro de salud con copago de ASISA.',
            ],
            [
                'insurer_slug' => 'asisa',
                'name' => 'Asisa Momento',
                'slug' => 'asisa-momento',
                'description' => 'Asisa Momento es el seguro de salud básico y económico de ASISA. Ideal para quienes buscan cobertura sanitaria esencial a un precio muy competitivo, con acceso a consultas, urgencias y pruebas diagnósticas básicas.',
                'meta_title' => 'Cuadro Médico Asisa Momento 2026 - Coberturas Básicas',
                'meta_description' => 'Consulta el cuadro médico de Asisa Momento 2026. Médicos y centros del seguro de salud básico y económico de ASISA.',
            ],
            [
                'insurer_slug' => 'asisa',
                'name' => 'Asisa Dental',
                'slug' => 'asisa-dental',
                'description' => 'Asisa Dental es el seguro dental de ASISA. Incluye revisiones, limpiezas, empastes, extracciones y urgencias dentales, con acceso a una amplia red de clínicas dentales y odontólogos en toda España.',
                'meta_title' => 'Cuadro Médico Asisa Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Asisa 2026. Encuentra clínicas dentales, dentistas y ortodoncistas incluidos en Asisa Dental.',
            ],
            [
                'insurer_slug' => 'asisa',
                'name' => 'Asisa Vida',
                'slug' => 'asisa-vida',
                'description' => 'Asisa Vida es el seguro de salud premium de ASISA que combina las coberturas más amplias con servicios exclusivos. Incluye segunda opinión médica internacional, reembolso de gastos y acceso preferente a los mejores especialistas.',
                'meta_title' => 'Cuadro Médico Asisa Vida 2026 - Coberturas Premium',
                'meta_description' => 'Consulta el cuadro médico de Asisa Vida 2026. Médicos, hospitales y coberturas premium del seguro de salud más completo de ASISA.',
            ],

            // ─── DKV (5 products) ───
            [
                'insurer_slug' => 'dkv',
                'name' => 'DKV Integral',
                'slug' => 'dkv-integral',
                'description' => 'DKV Integral es el seguro de salud más completo de DKV Seguros. Ofrece cobertura sanitaria total sin copagos, con acceso a una amplia red médica, hospitalización, pruebas diagnósticas avanzadas y servicios de bienestar digital.',
                'meta_title' => 'Cuadro Médico DKV Integral 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de DKV Integral 2026. Médicos, especialistas, hospitales y centros incluidos en el seguro más completo de DKV.',
            ],
            [
                'insurer_slug' => 'dkv',
                'name' => 'DKV Elección',
                'slug' => 'dkv-eleccion',
                'description' => 'DKV Elección es un seguro de salud modular de DKV que permite personalizar las coberturas. El asegurado elige entre distintos niveles de cobertura y copago, adaptando el seguro a sus necesidades y presupuesto.',
                'meta_title' => 'Cuadro Médico DKV Elección 2026 - Coberturas Personalizables',
                'meta_description' => 'Consulta el cuadro médico de DKV Elección 2026. Médicos y centros disponibles en el seguro de salud personalizable de DKV.',
            ],
            [
                'insurer_slug' => 'dkv',
                'name' => 'DKV Mundisalud',
                'slug' => 'dkv-mundisalud',
                'description' => 'DKV Mundisalud es el seguro de salud internacional de DKV Seguros. Ofrece cobertura sanitaria en todo el mundo con reembolso de gastos médicos, ideal para expatriados y personas que viajan frecuentemente.',
                'meta_title' => 'Cuadro Médico DKV Mundisalud 2026 - Cobertura Internacional',
                'meta_description' => 'Consulta el cuadro médico de DKV Mundisalud 2026. Cobertura sanitaria internacional con médicos y centros en España y el extranjero.',
            ],
            [
                'insurer_slug' => 'dkv',
                'name' => 'DKV Dental',
                'slug' => 'dkv-dental',
                'description' => 'DKV Dental es el seguro dental de DKV Seguros. Incluye revisiones, limpiezas, empastes, extracciones y urgencias dentales, además de descuentos en ortodoncia e implantes en una amplia red de clínicas dentales.',
                'meta_title' => 'Cuadro Médico DKV Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de DKV 2026. Encuentra clínicas dentales, dentistas y ortodoncistas incluidos en DKV Dental.',
            ],
            [
                'insurer_slug' => 'dkv',
                'name' => 'DKV Profesional',
                'slug' => 'dkv-profesional',
                'description' => 'DKV Profesional es el seguro de salud de DKV diseñado para autónomos y profesionales. Ofrece coberturas sanitarias completas con ventajas fiscales, acceso a medicina preventiva y programas de bienestar específicos para trabajadores independientes.',
                'meta_title' => 'Cuadro Médico DKV Profesional 2026 - Seguro para Autónomos',
                'meta_description' => 'Consulta el cuadro médico de DKV Profesional 2026. Médicos, centros y coberturas del seguro de salud para autónomos de DKV.',
            ],

            // ─── Mapfre (4 products) ───
            [
                'insurer_slug' => 'mapfre',
                'name' => 'Mapfre Salud Élite',
                'slug' => 'mapfre-salud-elite',
                'description' => 'Mapfre Salud Élite es el seguro de salud premium de MAPFRE. Incluye las coberturas más amplias sin copagos, con acceso a los mejores hospitales y especialistas, segunda opinión médica y servicios de asistencia en viaje.',
                'meta_title' => 'Cuadro Médico Mapfre Salud Élite 2026 - Coberturas Premium',
                'meta_description' => 'Consulta el cuadro médico de Mapfre Salud Élite 2026. Médicos, hospitales y coberturas premium del mejor seguro de salud de MAPFRE.',
            ],
            [
                'insurer_slug' => 'mapfre',
                'name' => 'Mapfre Salud Óptima',
                'slug' => 'mapfre-salud-optima',
                'description' => 'Mapfre Salud Óptima es un seguro de salud de gama media-alta de MAPFRE. Ofrece coberturas amplias con un buen equilibrio calidad-precio, incluyendo hospitalización, consultas especializadas y pruebas diagnósticas.',
                'meta_title' => 'Cuadro Médico Mapfre Salud Óptima 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Mapfre Salud Óptima 2026. Encuentra médicos, especialistas y centros del seguro Mapfre Salud Óptima.',
            ],
            [
                'insurer_slug' => 'mapfre',
                'name' => 'Mapfre Salud Esencial',
                'slug' => 'mapfre-salud-esencial',
                'description' => 'Mapfre Salud Esencial es el seguro de salud básico de MAPFRE. Diseñado para quienes buscan protección sanitaria esencial a precio asequible, con copago en consultas y acceso a la red médica de MAPFRE.',
                'meta_title' => 'Cuadro Médico Mapfre Salud Esencial 2026 - Coberturas Básicas',
                'meta_description' => 'Consulta el cuadro médico de Mapfre Salud Esencial 2026. Médicos y centros del seguro de salud básico y económico de MAPFRE.',
            ],
            [
                'insurer_slug' => 'mapfre',
                'name' => 'Mapfre Dental',
                'slug' => 'mapfre-dental',
                'description' => 'Mapfre Dental es el seguro dental de MAPFRE. Incluye coberturas dentales básicas como revisiones, limpiezas y empastes, con acceso a una red de clínicas dentales y descuentos en tratamientos avanzados como ortodoncia e implantes.',
                'meta_title' => 'Cuadro Médico Mapfre Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Mapfre 2026. Clínicas dentales, dentistas y coberturas incluidas en el seguro Mapfre Dental.',
            ],

            // ─── AXA (3 products) ───
            [
                'insurer_slug' => 'axa',
                'name' => 'AXA Óptima Salud',
                'slug' => 'axa-optima-salud',
                'description' => 'AXA Óptima Salud es el seguro de salud completo de AXA. Ofrece cobertura sanitaria integral con acceso a una amplia red de profesionales médicos, hospitalización, pruebas diagnósticas y cirugía sin listas de espera.',
                'meta_title' => 'Cuadro Médico AXA Óptima Salud 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de AXA Óptima Salud 2026. Médicos, especialistas, hospitales y centros del seguro completo de AXA.',
            ],
            [
                'insurer_slug' => 'axa',
                'name' => 'AXA Activa Salud',
                'slug' => 'axa-activa-salud',
                'description' => 'AXA Activa Salud es un seguro de salud con copago de AXA. Permite acceder a la misma red médica que los planes completos pero a un precio más reducido gracias a un sistema de copagos moderados por uso.',
                'meta_title' => 'Cuadro Médico AXA Activa Salud 2026 - Coberturas con Copago',
                'meta_description' => 'Consulta el cuadro médico de AXA Activa Salud 2026. Médicos y centros del seguro de salud con copago de AXA.',
            ],
            [
                'insurer_slug' => 'axa',
                'name' => 'AXA Dental',
                'slug' => 'axa-dental',
                'description' => 'AXA Dental es el seguro dental de AXA Seguros. Incluye revisiones, tratamientos preventivos, empastes y extracciones, con descuentos en ortodoncia e implantes a través de una red de clínicas dentales concertadas.',
                'meta_title' => 'Cuadro Médico AXA Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de AXA 2026. Clínicas dentales, dentistas y coberturas del seguro AXA Dental.',
            ],

            // ─── Cigna (3 products) ───
            [
                'insurer_slug' => 'cigna',
                'name' => 'Cigna Conecta',
                'slug' => 'cigna-conecta',
                'description' => 'Cigna Conecta es el seguro de salud completo de Cigna Healthcare. Combina asistencia presencial con servicios digitales de salud, ofreciendo acceso a una amplia red médica, programas de bienestar y herramientas de salud digital.',
                'meta_title' => 'Cuadro Médico Cigna Conecta 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Cigna Conecta 2026. Médicos, especialistas, hospitales y centros del seguro de salud completo de Cigna.',
            ],
            [
                'insurer_slug' => 'cigna',
                'name' => 'Cigna Esencial',
                'slug' => 'cigna-esencial',
                'description' => 'Cigna Esencial es el seguro de salud básico de Cigna Healthcare. Ofrece coberturas sanitarias esenciales con copago, acceso a la red médica de Cigna y programas de bienestar a un precio muy competitivo.',
                'meta_title' => 'Cuadro Médico Cigna Esencial 2026 - Coberturas Básicas',
                'meta_description' => 'Consulta el cuadro médico de Cigna Esencial 2026. Médicos y centros del seguro de salud básico de Cigna Healthcare.',
            ],
            [
                'insurer_slug' => 'cigna',
                'name' => 'Cigna Dental',
                'slug' => 'cigna-dental',
                'description' => 'Cigna Dental es el seguro dental de Cigna Healthcare. Incluye revisiones, tratamientos preventivos, empastes y extracciones, con una amplia red de clínicas dentales y descuentos en tratamientos especializados.',
                'meta_title' => 'Cuadro Médico Cigna Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Cigna 2026. Clínicas dentales, dentistas y coberturas incluidas en el seguro Cigna Dental.',
            ],

            // ─── Caser (3 products) ───
            [
                'insurer_slug' => 'caser',
                'name' => 'Caser Salud Completa',
                'slug' => 'caser-salud-completa',
                'description' => 'Caser Salud Completa es el seguro de salud integral de Caser Seguros. Incluye hospitalización, consultas con especialistas, pruebas diagnósticas y cirugía, con acceso a una amplia red de centros sanitarios y hospitales.',
                'meta_title' => 'Cuadro Médico Caser Salud Completa 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Caser Salud Completa 2026. Médicos, especialistas y hospitales del seguro de salud integral de Caser.',
            ],
            [
                'insurer_slug' => 'caser',
                'name' => 'Caser Salud Esencial',
                'slug' => 'caser-salud-esencial',
                'description' => 'Caser Salud Esencial es el seguro de salud básico de Caser con copago. Ofrece coberturas sanitarias esenciales a un precio accesible, con acceso a la red médica de Caser para consultas, urgencias y diagnóstico.',
                'meta_title' => 'Cuadro Médico Caser Salud Esencial 2026 - Coberturas Básicas',
                'meta_description' => 'Consulta el cuadro médico de Caser Salud Esencial 2026. Médicos y centros del seguro básico con copago de Caser Seguros.',
            ],
            [
                'insurer_slug' => 'caser',
                'name' => 'Caser Dental',
                'slug' => 'caser-dental',
                'description' => 'Caser Dental es el seguro dental de Caser Seguros. Cubre revisiones, limpiezas, empastes, extracciones y urgencias dentales, con acceso a una red de clínicas y profesionales dentales en toda España.',
                'meta_title' => 'Cuadro Médico Caser Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Caser 2026. Clínicas dentales y dentistas incluidos en el seguro Caser Dental.',
            ],

            // ─── Vivaz (2 products) ───
            [
                'insurer_slug' => 'vivaz',
                'name' => 'Vivaz Salud',
                'slug' => 'vivaz-salud',
                'description' => 'Vivaz Salud es el seguro de salud digital de Vivaz. 100% gestionable desde la app, ofrece consultas con especialistas, pruebas diagnósticas, hospitalización y servicios de telemedicina a precios muy competitivos para un público joven y digital.',
                'meta_title' => 'Cuadro Médico Vivaz Salud 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Vivaz Salud 2026. Médicos, especialistas y centros del seguro de salud 100% digital de Vivaz.',
            ],
            [
                'insurer_slug' => 'vivaz',
                'name' => 'Vivaz Dental',
                'slug' => 'vivaz-dental',
                'description' => 'Vivaz Dental es el seguro dental digital de Vivaz. Contratación y gestión online, con cobertura de revisiones, limpiezas, empastes y extracciones en una red de clínicas dentales a precios muy asequibles.',
                'meta_title' => 'Cuadro Médico Vivaz Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de Vivaz 2026. Clínicas dentales y dentistas incluidos en el seguro dental digital de Vivaz.',
            ],

            // ─── Generali (2 products) ───
            [
                'insurer_slug' => 'generali',
                'name' => 'Generali Salud Óptima',
                'slug' => 'generali-salud-optima',
                'description' => 'Generali Salud Óptima es el seguro de salud completo de Generali. Ofrece coberturas integrales sin copagos, con acceso a una extensa red de especialistas, hospitales y centros sanitarios en toda España.',
                'meta_title' => 'Cuadro Médico Generali Salud Óptima 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de Generali Salud Óptima 2026. Médicos, especialistas y hospitales del seguro completo de Generali.',
            ],
            [
                'insurer_slug' => 'generali',
                'name' => 'Generali Salud Inicial',
                'slug' => 'generali-salud-inicial',
                'description' => 'Generali Salud Inicial es un seguro de salud básico de Generali con copago. Permite acceder a la red médica de Generali para consultas, urgencias y diagnóstico a un precio reducido mediante copagos por utilización.',
                'meta_title' => 'Cuadro Médico Generali Salud Inicial 2026 - Coberturas Básicas',
                'meta_description' => 'Consulta el cuadro médico de Generali Salud Inicial 2026. Médicos y centros del seguro de salud básico de Generali.',
            ],

            // ─── FIATC (2 products) ───
            [
                'insurer_slug' => 'fiatc',
                'name' => 'FIATC Salud Integral',
                'slug' => 'fiatc-salud-integral',
                'description' => 'FIATC Salud Integral es el seguro de salud más completo de FIATC Seguros. Incluye hospitalización, consultas con especialistas, pruebas diagnósticas y cirugía, con una red médica especialmente amplia en Cataluña y cobertura nacional.',
                'meta_title' => 'Cuadro Médico FIATC Salud Integral 2026 - Coberturas y Médicos',
                'meta_description' => 'Consulta el cuadro médico de FIATC Salud Integral 2026. Médicos, especialistas y hospitales del seguro más completo de FIATC.',
            ],
            [
                'insurer_slug' => 'fiatc',
                'name' => 'FIATC Dental',
                'slug' => 'fiatc-dental',
                'description' => 'FIATC Dental es el seguro dental de FIATC Seguros. Incluye revisiones, limpiezas, empastes y extracciones, con acceso a una red de clínicas dentales y descuentos en tratamientos de ortodoncia e implantología.',
                'meta_title' => 'Cuadro Médico FIATC Dental 2026 - Clínicas y Dentistas',
                'meta_description' => 'Consulta el cuadro dental de FIATC 2026. Clínicas dentales y dentistas incluidos en el seguro FIATC Dental.',
            ],
        ];

        foreach ($products as $product) {
            $insurerSlug = $product['insurer_slug'];
            unset($product['insurer_slug']);

            if (!isset($insurerIds[$insurerSlug])) {
                $this->command->warn("Insurer '{$insurerSlug}' not found, skipping product '{$product['name']}'");
                continue;
            }

            DB::table('insurer_products')->updateOrInsert(
                ['slug' => $product['slug']],
                array_merge($product, [
                    'insurer_id' => $insurerIds[$insurerSlug],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
