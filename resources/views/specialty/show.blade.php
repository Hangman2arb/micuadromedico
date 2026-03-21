@php
    $year = date('Y');
@endphp

@extends('layouts.app')

@section('title', "{$specialty->name} — Aseguradoras con esta Especialidad {$year} — Mi Cuadro Médico")
@section('meta_description', "Aseguradoras que incluyen {$specialty->name} en su cuadro médico en {$year}. Compara {$insurers->count()} aseguradoras con cobertura de {$specialty->name} en toda España.")
@section('og_title', "{$specialty->name} — Cuadros Médicos {$year}")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Especialidades', 'url' => url('/especialidades')],
        ['label' => $specialty->name],
    ]])
@endsection

@section('content')
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1d5fa715 0%, #1d5fa705 100%);">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-white">
                <i class="fa-solid fa-stethoscope text-[10px]"></i>
                {{ $categoryLabel }}
            </span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
            <span class="text-primary">{{ $specialty->name }}</span>
        </h1>
        <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
            {{ $insurers->count() }} aseguradoras incluyen {{ $specialty->name }} en su cuadro médico en España.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="lg:grid lg:grid-cols-3 lg:gap-12">
        <div class="lg:col-span-2">
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-6 flex items-center gap-2.5">
                    <i class="fa-solid fa-hospital text-primary text-lg"></i>
                    Aseguradoras con {{ $specialty->name }}
                </h2>
                <div class="space-y-3">
                    @foreach($insurers as $insurer)
                    @php
                        $iColor = $insurer->brand_color ?? '#1d5fa7';
                        $iInitial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
                    @endphp
                    <a href="{{ route('insurer.show', $insurer->slug) }}"
                       class="group block bg-white rounded-xl border border-gray-200 p-5 hover:border-primary/30 hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shrink-0" style="background-color: {{ $iColor }}">
                                {{ $iInitial }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-ink group-hover:text-primary transition-colors">{{ $insurer->name }}</h3>
                                <p class="text-xs text-gray-400 mt-1">
                                    <i class="fa-solid fa-map-pin mr-1"></i>{{ $insurer->province_count_for_specialty }} provincias con {{ $specialty->name }}
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray-300 group-hover:text-primary transition-colors"></i>
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>

            @include('components.cta-banner')
        </div>

        <aside class="lg:col-span-1 mt-10 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">
                <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                    <h3 class="font-bold text-ink text-sm mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        {{ $specialty->name }}
                    </h3>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Categoría</dt>
                            <dd class="font-medium text-ink">{{ $categoryLabel }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Aseguradoras</dt>
                            <dd class="font-medium text-ink">{{ $insurers->count() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-gradient-to-br from-accent to-accent-dark rounded-xl p-6 text-white">
                    <i class="fa-solid fa-shield-heart text-xl mb-3 opacity-80"></i>
                    <h3 class="font-bold text-base mb-2">¿Necesitas {{ $specialty->name }}?</h3>
                    <p class="text-sm text-green-100 mb-4 leading-relaxed">
                        Compara seguros de salud que cubren {{ $specialty->name }} y encuentra la mejor oferta.
                    </p>
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=sidebar&utm_content={{ $specialty->slug }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-4 py-2.5 bg-white text-accent font-semibold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Comparar precios
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
