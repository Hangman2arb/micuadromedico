@php
    $year = date('Y');
    $color = $insurer->brand_color ?? '#1d5fa7';
    $initial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
    $hasPdf = isset($pdfUrl) && $pdfUrl;
@endphp

@extends('layouts.app')

@section('title', "Cuadro Médico {$insurer->name} en {$province->name} {$year} — Mi Cuadro Médico")
@section('meta_description', "Cuadro médico de {$insurer->name} en {$province->name} actualizado en {$year}. Consulta médicos, especialistas, centros de salud y hospitales disponibles en {$province->name}.")

@section('og_title', "Cuadro Médico {$insurer->name} en {$province->name} {$year}")
@section('og_description', "Encuentra médicos y especialistas de {$insurer->name} en {$province->name}. Cuadro médico completo y actualizado.")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => $insurer->name, 'url' => route('insurer.show', $insurer->slug)],
        ['label' => $province->name],
    ]])
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "MedicalOrganization",
    "name": "{{ $insurer->name }} — {{ $province->name }}",
    "description": "Cuadro médico de {{ $insurer->name }} en {{ $province->name }}",
    "url": "{{ route('insurer.province', [$insurer->slug, $province->slug]) }}",
    "areaServed": {
        "@@type": "AdministrativeArea",
        "name": "{{ $province->name }}",
        "containedInPlace": {
            "@@type": "Country",
            "name": "España"
        }
    }
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden border-b border-gray-100" style="background: linear-gradient(135deg, {{ $color }}12 0%, {{ $color }}03 100%);">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full opacity-[0.06]" style="background-color: {{ $color }}; transform: translate(25%, -35%);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
        <div class="flex flex-col sm:flex-row items-start gap-5">
            {{-- Insurer badge --}}
            <div class="shrink-0">
                @if($insurer->logo ?? false)
                    <img src="{{ asset($insurer->logo) }}" alt="Logo {{ $insurer->name }}"
                         class="w-16 h-16 rounded-xl object-contain bg-white border border-gray-200 p-2 shadow-sm"
                         width="64" height="64">
                @else
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-md"
                         style="background-color: {{ $color }}">
                        {{ $initial }}
                    </div>
                @endif
            </div>

            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $color }}">
                        <i class="fa-solid fa-map-pin text-[10px]"></i>
                        {{ $province->name }}
                    </span>
                    <span class="text-xs text-gray-400">Actualizado {{ $year }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-ink leading-tight">
                    Cuadro Médico {{ $insurer->name }}
                    <span class="block sm:inline">en {{ $province->name }}</span>
                    <span class="text-primary">{{ $year }}</span>
                </h1>

                <p class="text-gray-500 mt-3 max-w-2xl leading-relaxed">
                    Consulta todos los médicos, especialistas, centros y hospitales de {{ $insurer->name }} disponibles en la provincia de {{ $province->name }}.
                </p>
            </div>
        </div>

        {{-- PDF Download --}}
        @if($hasPdf)
        <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                <i class="fa-solid fa-file-pdf text-lg"></i>
                <span>Descargar cuadro médico en PDF</span>
            </a>
            <span class="text-xs text-gray-400 self-center">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Documento oficial de {{ $insurer->name }}
            </span>
        </div>
        @endif
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="lg:grid lg:grid-cols-3 lg:gap-12">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-12">

            {{-- Specialties --}}
            @if(isset($specialties) && $specialties->count() > 0)
            <section id="especialidades" x-data="{ filter: '' }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                    <h2 class="text-xl lg:text-2xl font-bold text-ink flex items-center gap-2.5">
                        <i class="fa-solid fa-user-doctor text-primary text-lg"></i>
                        Especialidades Disponibles
                    </h2>
                    @if($specialties->count() > 12)
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" x-model="filter" placeholder="Filtrar especialidades..."
                               class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 w-full sm:w-56"
                               aria-label="Filtrar especialidades">
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($specialties as $specialty)
                    <div class="specialty-item bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3.5 hover:border-primary/20 hover:shadow-sm transition-all"
                         x-show="!filter || '{{ mb_strtolower($specialty->name) }}'.includes(filter.toLowerCase())"
                         data-name="{{ mb_strtolower($specialty->name) }}">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ $color }}15;">
                            <i class="fa-solid fa-stethoscope text-sm" style="color: {{ $color }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-ink text-sm truncate">{{ $specialty->name }}</h3>
                            @if($specialty->count ?? false)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $specialty->count }} profesionales</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Localities --}}
            @if(isset($localities) && $localities->count() > 0)
            <section id="localidades">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-city text-primary text-lg"></i>
                    Localidades con Cobertura
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($localities as $locality)
                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-sm text-gray-600 rounded-lg border border-gray-100">
                            {{ $locality->name }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            {{-- Compatible products --}}
            @if(isset($compatibleProducts) && $compatibleProducts->count() > 0)
            <section>
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-shield-heart text-primary text-lg"></i>
                    Pólizas Compatibles con este Cuadro Médico
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                    @foreach($compatibleProducts as $product)
                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-ink text-sm">{{ $product->name }}</h3>
                            @if($product->price_text ?? false)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $product->price_text }}</p>
                            @endif
                        </div>
                        @if($product->url ?? false)
                        <a href="{{ $product->url }}" class="text-sm text-primary font-medium hover:underline shrink-0">
                            Ver detalles <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Editorial content (AI-generated, province-specific) --}}
            @if($pivotData?->content_html)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary text-lg"></i>
                    {{ $insurer->name }} en {{ $province->name }}
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6 lg:p-8 prose prose-gray max-w-none prose-headings:text-ink prose-a:text-primary">
                    {!! $pivotData->content_html !!}
                </div>
            </section>
            @endif

            {{-- FAQ --}}
            @if(isset($faqs) && count($faqs) > 0)
                @include('components.faq-accordion', [
                    'faqs' => $faqs,
                    'title' => "Preguntas sobre {$insurer->name} en {$province->name}"
                ])
            @else
                @include('components.faq-accordion', [
                    'title' => "Preguntas sobre {$insurer->name} en {$province->name}",
                    'faqs' => [
                        [
                            'question' => "¿Cómo puedo consultar el cuadro médico de {$insurer->name} en {$province->name}?",
                            'answer' => "En esta página puedes ver las especialidades y localidades con cobertura de {$insurer->name} en {$province->name}. Si está disponible, también puedes descargar el cuadro médico completo en formato PDF. Para información en tiempo real, te recomendamos consultar la web o app de {$insurer->name}."
                        ],
                        [
                            'question' => "¿Puedo cambiar de médico dentro del cuadro de {$insurer->name} en {$province->name}?",
                            'answer' => "Sí, dentro del cuadro médico de {$insurer->name} puedes elegir libremente entre los profesionales disponibles en {$province->name}. No necesitas autorización previa para cambiar de médico de cabecera o especialista dentro del cuadro."
                        ],
                        [
                            'question' => "¿El cuadro médico de {$insurer->name} cubre urgencias en {$province->name}?",
                            'answer' => "Sí, {$insurer->name} cuenta con centros de urgencias dentro de su cuadro médico en {$province->name}. En caso de urgencia vital, puedes acudir a cualquier centro hospitalario y tu aseguradora cubrirá la asistencia."
                        ],
                    ]
                ])
            @endif

            {{-- CTA --}}
            @include('components.cta-banner', ['insurer_name' => $insurer->name])
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-1 mt-10 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">

                {{-- PDF card (if available) --}}
                @if($hasPdf)
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <i class="fa-solid fa-file-pdf text-red-500 text-3xl mb-3"></i>
                    <h3 class="font-bold text-ink text-sm mb-2">Cuadro Médico en PDF</h3>
                    <p class="text-xs text-gray-500 mb-4">Documento oficial de {{ $insurer->name }} para {{ $province->name }}.</p>
                    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
                       class="block w-full px-4 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors">
                        <i class="fa-solid fa-download mr-1.5"></i>Descargar PDF
                    </a>
                </div>
                @endif

                {{-- Other provinces --}}
                @if(isset($otherProvinces) && $otherProvinces->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-bold text-ink text-sm uppercase tracking-wider mb-4">
                        {{ $insurer->name }} en otras provincias
                    </h3>
                    <ul class="space-y-1.5 max-h-72 overflow-y-auto">
                        @foreach($otherProvinces as $otherProv)
                        <li>
                            <a href="{{ route('insurer.province', [$insurer->slug, $otherProv->slug]) }}"
                               class="flex items-center gap-2 py-1.5 px-2 -mx-2 rounded-lg text-sm text-gray-600 hover:text-primary hover:bg-gray-50 transition-colors {{ $otherProv->slug === $province->slug ? 'text-primary font-semibold bg-primary/5' : '' }}">
                                <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                                {{ $otherProv->name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('insurer.show', $insurer->slug) }}" class="block text-center text-xs text-primary font-medium mt-4 hover:underline">
                        Ver todas las provincias
                    </a>
                </div>
                @endif

                {{-- Quick info --}}
                <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                    <h3 class="font-bold text-ink text-sm mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        Datos clave
                    </h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Aseguradora</dt>
                            <dd class="font-medium text-ink">{{ $insurer->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Provincia</dt>
                            <dd class="font-medium text-ink">{{ $province->name }}</dd>
                        </div>
                        @if(isset($specialties) && $specialties->count() > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Especialidades</dt>
                            <dd class="font-medium text-ink">{{ $specialties->count() }}</dd>
                        </div>
                        @endif
                        @if(isset($localities) && $localities->count() > 0)
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Localidades</dt>
                            <dd class="font-medium text-ink">{{ $localities->count() }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Actualización</dt>
                            <dd class="font-medium text-ink">{{ $year }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Mini CTA --}}
                <div class="bg-gradient-to-br from-accent to-accent-dark rounded-xl p-6 text-white">
                    <i class="fa-solid fa-bolt text-xl mb-3 opacity-80"></i>
                    <h3 class="font-bold text-base mb-2">Compara seguros en {{ $province->name }}</h3>
                    <p class="text-sm text-green-100 mb-4 leading-relaxed">
                        Encuentra las mejores ofertas de seguros de salud en tu provincia.
                    </p>
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=sidebar&utm_content={{ $insurer->slug }}-{{ $province->slug }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-4 py-2.5 bg-white text-accent font-semibold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Comparar precios
                    </a>
                </div>
            </div>
        </aside>

    </div>
</div>

@endsection
