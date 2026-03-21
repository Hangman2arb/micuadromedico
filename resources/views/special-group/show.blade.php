@extends('layouts.app')

@php
    $year = date('Y');

    $groupMeta = [
        'muface' => [
            'full_name' => 'MUFACE',
            'long_name' => 'Mutualidad General de Funcionarios Civiles del Estado',
            'color' => '#1d5fa7',
            'icon' => 'fa-landmark',
            'description' => 'MUFACE es la mutualidad que gestiona la protección social de los funcionarios de la Administración General del Estado, incluyendo la asistencia sanitaria. Los mutualistas de MUFACE pueden elegir entre recibir asistencia sanitaria a través del sistema público de Seguridad Social o mediante una entidad de seguro privado concertada con MUFACE.',
            'who' => 'Funcionarios civiles del Estado incluidos en el ámbito de aplicación del Régimen especial de Seguridad Social de los Funcionarios Civiles del Estado, así como sus beneficiarios (cónyuge, hijos y otros familiares dependientes).',
        ],
        'mugeju' => [
            'full_name' => 'MUGEJU',
            'long_name' => 'Mutualidad General Judicial',
            'color' => '#8e44ad',
            'icon' => 'fa-scale-balanced',
            'description' => 'MUGEJU es la mutualidad que proporciona protección social a los funcionarios al servicio de la Administración de Justicia. Los mutualistas pueden optar por la asistencia sanitaria del sistema público o elegir una aseguradora privada concertada.',
            'who' => 'Funcionarios de carrera de la Administración de Justicia (jueces, magistrados, fiscales, secretarios judiciales, etc.) y sus beneficiarios.',
        ],
        'isfas' => [
            'full_name' => 'ISFAS',
            'long_name' => 'Instituto Social de las Fuerzas Armadas',
            'color' => '#27ae60',
            'icon' => 'fa-shield-halved',
            'description' => 'ISFAS es el organismo encargado de la protección social de los miembros de las Fuerzas Armadas. Los asegurados de ISFAS pueden elegir entre la sanidad militar, el sistema público de salud o una aseguradora privada concertada.',
            'who' => 'Militares profesionales de las Fuerzas Armadas (Ejército de Tierra, Armada, Ejército del Aire y del Espacio), miembros del Cuerpo de la Guardia Civil adscritos a ISFAS, y sus beneficiarios.',
        ],
    ];

    $meta = $groupMeta[$specialGroup->slug] ?? $groupMeta['muface'];
@endphp

@section('title', "Cuadro Médico {$meta['full_name']} {$year} — Aseguradoras y Coberturas — Mi Cuadro Médico")
@section('meta_description', "Cuadro médico de {$meta['full_name']} ({$meta['long_name']}) actualizado en {$year}. Consulta las aseguradoras concertadas, coberturas y cómo acceder al cuadro médico de {$meta['full_name']}.")

@section('og_title', "Cuadro Médico {$meta['full_name']} {$year}")
@section('og_description', "Aseguradoras concertadas con {$meta['full_name']}. Consulta coberturas y cuadros médicos actualizados.")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => $meta['full_name']],
    ]])
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "GovernmentOrganization",
    "name": "{{ $meta['full_name'] }}",
    "alternateName": "{{ $meta['long_name'] }}",
    "description": "{{ $meta['description'] }}",
    "url": "{{ route('special-group.show', $specialGroup->slug) }}",
    "areaServed": {
        "@@type": "Country",
        "name": "España"
    }
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $meta['color'] }}15 0%, {{ $meta['color'] }}05 100%);">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-[0.06]" style="background-color: {{ $meta['color'] }}; transform: translate(30%, -40%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-[0.04]" style="background-color: {{ $meta['color'] }}; transform: translate(-20%, 40%);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex flex-col sm:flex-row items-start gap-6">
            {{-- Icon --}}
            <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-white text-3xl lg:text-4xl shadow-lg shrink-0"
                 style="background-color: {{ $meta['color'] }}">
                <i class="fa-solid {{ $meta['icon'] }}"></i>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $meta['color'] }}">
                        <i class="fa-solid fa-building-columns text-[10px]"></i>
                        Funcionarios
                    </span>
                    <span class="text-xs text-gray-400">Actualizado {{ $year }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
                    Cuadro Médico {{ $meta['full_name'] }}
                    <span style="color: {{ $meta['color'] }}">{{ $year }}</span>
                </h1>

                <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
                    {{ $meta['long_name'] }} — Consulta las aseguradoras concertadas y sus cuadros médicos.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">

    {{-- What is section --}}
    <section class="mb-12 max-w-4xl">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
            <h2 class="text-xl lg:text-2xl font-bold text-ink mb-4 flex items-center gap-2.5">
                <i class="fa-solid fa-circle-info text-lg" style="color: {{ $meta['color'] }}"></i>
                ¿Qué es {{ $meta['full_name'] }}?
            </h2>
            <p class="text-gray-600 leading-relaxed mb-5">
                {{ $meta['description'] }}
            </p>

            <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                <h3 class="font-semibold text-ink text-sm mb-2">
                    <i class="fa-solid fa-user-group mr-1.5" style="color: {{ $meta['color'] }}"></i>
                    ¿Quién puede acceder?
                </h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $meta['who'] }}
                </p>
            </div>
        </div>
    </section>

    {{-- Participating insurers --}}
    <section class="mb-12" id="aseguradoras">
        <h2 class="text-2xl lg:text-3xl font-bold text-ink mb-3 flex items-center gap-2.5">
            <i class="fa-solid fa-hospital text-primary text-xl"></i>
            Aseguradoras Concertadas con {{ $meta['full_name'] }}
        </h2>
        <p class="text-gray-500 mb-8 max-w-2xl">
            Estas son las aseguradoras que ofrecen cobertura sanitaria a los mutualistas de {{ $meta['full_name'] }} para el período {{ $year }}.
        </p>

        @if(isset($insurers) && $insurers->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($insurers as $insurer)
            @php
                $insurerColor = $insurer->brand_color ?? '#1d5fa7';
                $insurerInitial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
            @endphp
            <a href="{{ route('insurer.show', $insurer->slug) }}"
               class="group block bg-white rounded-2xl border border-gray-200 p-6 hover:border-transparent hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300 relative overflow-hidden">
                {{-- Accent bar --}}
                <div class="absolute top-0 left-0 right-0 h-1.5 opacity-0 group-hover:opacity-100 transition-opacity" style="background-color: {{ $insurerColor }}"></div>

                <div class="flex items-start gap-4">
                    @if($insurer->logo ?? false)
                        <img src="{{ asset($insurer->logo) }}" alt="Logo {{ $insurer->name }}"
                             class="w-16 h-16 rounded-xl object-contain border border-gray-100 p-2" loading="lazy"
                             width="64" height="64">
                    @else
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-sm"
                             style="background-color: {{ $insurerColor }}">
                            {{ $insurerInitial }}
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-ink text-base group-hover:text-primary transition-colors">
                            {{ $insurer->name }}
                        </h3>
                        @if($insurer->pivot->product_name ?? $insurer->product_name ?? false)
                            <p class="text-sm text-gray-500 mt-1">{{ $insurer->pivot->product_name ?? $insurer->product_name }}</p>
                        @endif
                        <div class="mt-3 flex items-center text-sm font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                            Ver cuadro médico <i class="fa-solid fa-arrow-right ml-1.5 text-xs"></i>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <i class="fa-solid fa-hospital text-gray-200 text-5xl mb-4"></i>
            <p class="text-gray-400 text-lg">Las aseguradoras concertadas se añadirán próximamente.</p>
        </div>
        @endif
    </section>

    {{-- Useful info --}}
    <section class="mb-12 max-w-4xl">
        <h2 class="text-xl lg:text-2xl font-bold text-ink mb-6 flex items-center gap-2.5">
            <i class="fa-solid fa-lightbulb text-amber-500 text-lg"></i>
            Información Útil
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background-color: {{ $meta['color'] }}15;">
                    <i class="fa-solid fa-calendar-check text-sm" style="color: {{ $meta['color'] }}"></i>
                </div>
                <h3 class="font-bold text-ink text-sm mb-1.5">Plazo de Elección</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Los mutualistas de {{ $meta['full_name'] }} pueden cambiar de entidad aseguradora durante el mes de enero de cada año, o cuando se produzcan determinadas circunstancias especiales.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background-color: {{ $meta['color'] }}15;">
                    <i class="fa-solid fa-rotate text-sm" style="color: {{ $meta['color'] }}"></i>
                </div>
                <h3 class="font-bold text-ink text-sm mb-1.5">Cambio de Aseguradora</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Si deseas cambiar de aseguradora, puedes hacerlo en el período ordinario (enero) a través de la sede electrónica de {{ $meta['full_name'] }} o en las oficinas de prestaciones.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background-color: {{ $meta['color'] }}15;">
                    <i class="fa-solid fa-hospital text-sm" style="color: {{ $meta['color'] }}"></i>
                </div>
                <h3 class="font-bold text-ink text-sm mb-1.5">Cobertura Nacional</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Las aseguradoras concertadas con {{ $meta['full_name'] }} ofrecen cobertura en toda España, con cuadros médicos que varían por provincia.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3" style="background-color: {{ $meta['color'] }}15;">
                    <i class="fa-solid fa-heart-pulse text-sm" style="color: {{ $meta['color'] }}"></i>
                </div>
                <h3 class="font-bold text-ink text-sm mb-1.5">Beneficiarios</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    La cobertura sanitaria de {{ $meta['full_name'] }} se extiende también a los beneficiarios del mutualista: cónyuge, hijos y otros familiares dependientes.
                </p>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    @include('components.faq-accordion', [
        'title' => "Preguntas Frecuentes sobre {$meta['full_name']}",
        'faqs' => !empty($faqs) ? $faqs : [
            [
                'question' => "¿Puedo elegir entre sanidad pública y privada con {$meta['full_name']}?",
                'answer' => "Sí, los mutualistas de {$meta['full_name']} pueden elegir entre recibir asistencia sanitaria a través del sistema público de salud (Seguridad Social) o mediante una aseguradora privada concertada. Esta elección se realiza al incorporarse como mutualista y puede modificarse en períodos específicos."
            ],
            [
                'question' => "¿Qué aseguradoras están concertadas con {$meta['full_name']}?",
                'answer' => "Las aseguradoras concertadas con {$meta['full_name']} pueden variar en cada período de concierto. Las entidades habituales incluyen Adeslas, Asisa, DKV y otras. Consulta la lista actualizada en esta página para conocer las opciones disponibles en {$year}."
            ],
            [
                'question' => "¿Los beneficiarios de {$meta['full_name']} tienen la misma cobertura?",
                'answer' => "Sí, los beneficiarios (cónyuge, hijos y otros familiares dependientes) del mutualista de {$meta['full_name']} tienen derecho a la misma asistencia sanitaria que el titular, con acceso al mismo cuadro médico de la aseguradora elegida."
            ],
            [
                'question' => "¿Puedo usar médicos privados que no estén en el cuadro de {$meta['full_name']}?",
                'answer' => "Si has elegido una aseguradora privada concertada con {$meta['full_name']}, debes utilizar los profesionales incluidos en su cuadro médico. Acudir a profesionales fuera del cuadro no estaría cubierto, salvo excepciones como urgencias vitales."
            ],
        ]
    ])

    {{-- CTA --}}
    @include('components.cta-banner')

</div>

@endsection
