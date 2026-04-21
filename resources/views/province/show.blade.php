@php
    $year = date('Y');
@endphp

@extends('layouts.app')

@section('title', "Cuadros Médicos en {$province->name} {$year} — Mi Cuadro Médico")
@section('meta_description', "Todas las aseguradoras con cuadro médico en {$province->name}. Compara {$insurers->count()} aseguradoras, consulta especialidades y descarga PDFs del cuadro médico en {$province->name}.")
@section('og_title', "Cuadros Médicos en {$province->name} {$year}")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Provincias', 'url' => url('/provincias')],
        ['label' => $province->name],
    ]])
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Aseguradoras con cuadro médico en {{ $province->name }}",
    "numberOfItems": {{ $insurers->count() }},
    "itemListElement": [
        @foreach($insurers as $i => $insurer)
        {
            "@@type": "ListItem",
            "position": {{ $i + 1 }},
            "item": {
                "@@type": "MedicalOrganization",
                "name": "{{ $insurer->name }}",
                "url": "{{ route('insurer.province', [$insurer->slug, $province->slug]) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endsection

@section('content')
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1d5fa715 0%, #1d5fa705 100%);">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-white">
                <i class="fa-solid fa-map-pin text-[10px]"></i>
                {{ $province->autonomous_community }}
            </span>
            <span class="text-xs text-gray-400">{{ $year }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
            Cuadros Médicos en <span class="text-primary">{{ $province->name }}</span>
        </h1>
        <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
            {{ $insurers->count() }} aseguradoras con cuadro médico disponible en {{ $province->name }}. Compara especialidades, centros y descarga PDFs.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="lg:grid lg:grid-cols-3 lg:gap-12">
        <div class="lg:col-span-2">

            {{-- Insurers grid --}}
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-6 flex items-center gap-2.5">
                    <i class="fa-solid fa-hospital text-primary text-lg"></i>
                    Aseguradoras en {{ $province->name }}
                </h2>
                <div class="space-y-3">
                    @foreach($insurers as $insurer)
                    @php
                        $iColor = $insurer->brand_color ?? '#1d5fa7';
                        $iInitial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
                        $pivotSpecs = $insurer->pivot->specialties_available ?? [];
                        if (is_string($pivotSpecs)) $pivotSpecs = json_decode($pivotSpecs, true) ?? [];
                        $specCount = count($pivotSpecs);
                        $pivotPdf = $insurer->pivot->pdf_url ?? null;
                    @endphp
                    <a href="{{ route('insurer.province', [$insurer->slug, $province->slug]) }}"
                       class="group block bg-white rounded-xl border border-gray-200 p-5 hover:border-primary/30 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            @if($insurer->logo)
                                <img src="{{ asset($insurer->logo) }}" alt="{{ $insurer->name }}" class="w-12 h-12 rounded-xl object-contain bg-white border border-gray-100 p-1 shrink-0" width="48" height="48" loading="lazy">
                            @else
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shrink-0" style="background-color: {{ $iColor }}">{{ $iInitial }}</div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-ink group-hover:text-primary transition-colors">{{ $insurer->name }}</h3>
                                <div class="flex items-center gap-4 mt-1 text-xs text-gray-400">
                                    @if($specCount > 0)
                                    <span><i class="fa-solid fa-stethoscope mr-1"></i>{{ $specCount }} especialidades</span>
                                    @endif
                                    @if($pivotPdf)
                                    <span><i class="fa-solid fa-file-pdf mr-1 text-red-400"></i>PDF disponible</span>
                                    @endif
                                </div>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-primary transition-colors"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>

            {{-- Specialties available (linked to /especialidades/{slug} for SEO cross-linking) --}}
            @if($specialties->count() > 0)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-user-doctor text-primary text-lg"></i>
                    Especialidades en {{ $province->name }}
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($specialties as $spec)
                        @php
                            // Support both Specialty objects (with slug) and plain strings (fallback)
                            $specName = is_object($spec) ? $spec->name : $spec;
                            $specSlug = is_object($spec) ? ($spec->slug ?? null) : null;
                        @endphp
                        @if($specSlug)
                            <a href="{{ route('specialty.show', $specSlug) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-sm text-gray-700 rounded-lg border border-gray-100 hover:bg-primary/5 hover:text-primary hover:border-primary/20 transition-colors"
                               title="Aseguradoras con {{ $specName }}">
                                {{ $specName }}
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 text-sm text-gray-600 rounded-lg border border-gray-100">
                                {{ $specName }}
                            </span>
                        @endif
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            {{-- Nearby provinces (same region) for lateral discovery --}}
            @if(isset($nearbyProvinces) && $nearbyProvinces->count() > 0)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-map-location-dot text-primary text-lg"></i>
                    Otras provincias en {{ $province->autonomous_community }}
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($nearbyProvinces as $np)
                        <a href="{{ url('/provincias/' . $np->slug) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-sm text-gray-700 rounded-lg border border-gray-100 hover:bg-primary/5 hover:text-primary hover:border-primary/20 transition-colors"
                           title="Cuadros médicos en {{ $np->name }}">
                            <i class="fa-solid fa-map-pin text-[10px] text-gray-400"></i>
                            {{ $np->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            @include('components.cta-banner')
        </div>

        <aside class="lg:col-span-1 mt-10 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">
                <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                    <h3 class="font-bold text-ink text-sm mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        {{ $province->name }}
                    </h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Comunidad</dt>
                            <dd class="font-medium text-ink">{{ $province->autonomous_community }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Aseguradoras</dt>
                            <dd class="font-medium text-ink">{{ $insurers->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Especialidades</dt>
                            <dd class="font-medium text-ink">{{ $specialties->count() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gradient-to-br from-accent to-accent-dark rounded-xl p-6 text-white">
                    <i class="fa-solid fa-bolt text-xl mb-3 opacity-80"></i>
                    <h3 class="font-bold text-base mb-2">Compara seguros en {{ $province->name }}</h3>
                    <p class="text-sm text-green-100 mb-4 leading-relaxed">
                        Encuentra las mejores ofertas de seguros de salud en tu provincia.
                    </p>
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=sidebar&utm_content={{ $province->slug }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-4 py-2.5 bg-white text-accent font-semibold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Comparar precios
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
