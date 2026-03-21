@php
    $year = date('Y');
    $color = $insurer->brand_color ?? '#1d5fa7';
    $initial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
    $groupColors = ['muface' => '#1d5fa7', 'mugeju' => '#8e44ad', 'isfas' => '#27ae60'];
    $groupIcons = ['muface' => 'fa-landmark', 'mugeju' => 'fa-scale-balanced', 'isfas' => 'fa-shield-halved'];
    $groupColor = $groupColors[$group->slug] ?? '#1d5fa7';
    $groupIcon = $groupIcons[$group->slug] ?? 'fa-landmark';
@endphp

@extends('layouts.app')

@section('title', "Cuadro Médico {$insurer->name} {$groupName} {$year} — Mi Cuadro Médico")
@section('meta_description', "Consulta el cuadro médico de {$insurer->name} para funcionarios de {$groupName} ({$groupFullName}) en {$year}. Médicos, especialistas, centros y hospitales para mutualistas.")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => $insurer->name, 'url' => route('insurer.show', $insurer->slug)],
        ['label' => $groupName],
    ]])
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "MedicalOrganization",
    "name": "{{ $insurer->name }} — {{ $groupName }}",
    "description": "Cuadro médico de {{ $insurer->name }} para funcionarios de {{ $groupName }}",
    "url": "{{ url("/{$insurer->slug}/{$group->slug}") }}"
}
</script>
@endsection

@section('content')
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $groupColor }}15 0%, {{ $color }}05 100%);">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full opacity-[0.06]" style="background-color: {{ $groupColor }}; transform: translate(30%, -40%);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            <div class="shrink-0">
                @if($insurer->logo)
                    <img src="{{ asset($insurer->logo) }}" alt="Logo {{ $insurer->name }}"
                         class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl object-contain bg-white border border-gray-200 p-3 shadow-sm"
                         width="96" height="96">
                @else
                    <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg"
                         style="background-color: {{ $color }}">{{ $initial }}</div>
                @endif
            </div>

            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold text-white" style="background-color: {{ $groupColor }}">
                        <i class="fa-solid {{ $groupIcon }} text-[10px]"></i>
                        {{ $groupName }}
                    </span>
                    <span class="text-xs text-gray-400">{{ $year }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
                    Cuadro Médico {{ $insurer->name }}
                    <span style="color: {{ $groupColor }}">{{ $groupName }}</span>
                </h1>
                <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
                    Cuadro médico de {{ $insurer->name }} para funcionarios de {{ $groupFullName }}. Consulta médicos, especialistas y centros disponibles.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="lg:grid lg:grid-cols-3 lg:gap-12">
        <div class="lg:col-span-2">

            {{-- What is this section --}}
            <section class="mb-12">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-8">
                    <h2 class="text-xl lg:text-2xl font-bold text-ink mb-4 flex items-center gap-2.5">
                        <i class="fa-solid {{ $groupIcon }} text-lg" style="color: {{ $groupColor }}"></i>
                        {{ $insurer->name }} con {{ $groupName }}
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        {{ $insurer->name }} es una de las aseguradoras concertadas con {{ $groupName }} ({{ $groupFullName }}) para el período {{ $year }}. Los mutualistas de {{ $groupName }} que elijan {{ $insurer->name }} como su aseguradora privada tienen acceso a todo el cuadro médico de {{ $insurer->name }}, incluyendo médicos de atención primaria, especialistas, hospitales y centros de salud en toda España.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        El cuadro médico de {{ $insurer->name }} para {{ $groupName }} ofrece cobertura sanitaria completa en las 52 provincias de España, con acceso a especialistas sin listas de espera y posibilidad de elegir libremente entre los profesionales del cuadro.
                    </p>
                </div>
            </section>

            {{-- Provinces grid --}}
            <section class="mb-12" id="provincias">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-2 flex items-center gap-2.5">
                    <i class="fa-solid fa-map-location-dot text-primary text-lg"></i>
                    {{ $insurer->name }} {{ $groupName }} por Provincia
                </h2>
                <p class="text-gray-500 text-sm mb-6">
                    Selecciona una provincia para consultar el cuadro médico de {{ $insurer->name }} {{ $groupName }} en tu zona.
                </p>

                @php
                    $provincesByRegion = $provinces->groupBy('autonomous_community');
                @endphp

                <div class="space-y-8">
                    @foreach($provincesByRegion as $region => $regionProvinces)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span class="w-8 h-px bg-gray-200"></span>
                            {{ $region }}
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            @foreach($regionProvinces as $province)
                            <a href="{{ route('insurer.province', [$insurer->slug, $province->slug]) }}"
                               class="group flex items-center gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3 hover:border-primary/30 hover:shadow-sm transition-all">
                                <span class="text-sm font-medium text-ink group-hover:text-primary transition-colors">{{ $province->name }}</span>
                                <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- Description --}}
            @if($insurer->description)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary text-lg"></i>
                    Sobre {{ $insurer->name }}
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6 lg:p-8 prose prose-gray max-w-none">
                    {!! $insurer->description !!}
                </div>
            </section>
            @endif

            {{-- FAQ --}}
            @if(!empty($faqs))
                @include('components.faq-accordion', ['faqs' => $faqs, 'title' => "Preguntas sobre {$insurer->name} {$groupName}"])
            @endif

            @include('components.cta-banner', ['insurer_name' => $insurer->name])
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-1 mt-10 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">

                {{-- Group info card --}}
                <div class="rounded-xl border p-6" style="background-color: {{ $groupColor }}08; border-color: {{ $groupColor }}20;">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg" style="background-color: {{ $groupColor }}">
                            <i class="fa-solid {{ $groupIcon }}"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-ink text-sm">{{ $groupName }}</h3>
                            <p class="text-xs text-gray-500">{{ $groupFullName }}</p>
                        </div>
                    </div>
                    <a href="{{ url('/' . $group->slug) }}" class="block text-center text-sm text-primary font-medium hover:underline">
                        Ver todas las aseguradoras de {{ $groupName }} <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>

                {{-- Other concertada insurers --}}
                @if($otherInsurers->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-bold text-ink text-sm uppercase tracking-wider mb-4">Otras concertadas {{ $groupName }}</h3>
                    <ul class="space-y-2">
                        @foreach($otherInsurers as $other)
                        <li>
                            <a href="{{ url('/' . $other->slug . '/' . $group->slug) }}"
                               class="flex items-center gap-3 py-2 px-3 -mx-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                @if($other->logo)
                                    <img src="{{ asset($other->logo) }}" alt="{{ $other->name }}" class="w-8 h-8 rounded-lg object-contain bg-white border border-gray-100 p-0.5 shrink-0" width="32" height="32" loading="lazy">
                                @else
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0" style="background-color: {{ $other->brand_color ?? '#1d5fa7' }}">{{ mb_strtoupper(mb_substr($other->name, 0, 1)) }}</div>
                                @endif
                                <span class="text-sm text-gray-600 group-hover:text-primary transition-colors">{{ $other->name }} {{ $groupName }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- CTA --}}
                <div class="bg-gradient-to-br from-accent to-accent-dark rounded-xl p-6 text-white">
                    <i class="fa-solid fa-shield-heart text-2xl mb-3 opacity-80"></i>
                    <h3 class="font-bold text-base mb-2">¿Eres funcionario?</h3>
                    <p class="text-sm text-green-100 mb-4 leading-relaxed">
                        Compara las aseguradoras disponibles para {{ $groupName }} y elige la mejor opción.
                    </p>
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=sidebar&utm_content={{ $insurer->slug }}-{{ $group->slug }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-4 py-2.5 bg-white text-accent font-semibold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Comparar precios
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
