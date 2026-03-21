@php
    $year = date('Y');
    $color = $insurer->brand_color ?? '#1d5fa7';
    $initial = mb_strtoupper(mb_substr($insurer->name, 0, 1));
@endphp

@extends('layouts.app')

@section('title', "Cuadro Médico {$insurer->name} {$year} — Mi Cuadro Médico")
@section('meta_description', "Consulta el cuadro médico de {$insurer->name} actualizado en {$year}. Encuentra médicos, especialistas y centros de salud de {$insurer->name} por provincia en toda España.")

@section('og_title', "Cuadro Médico {$insurer->name} {$year}")
@section('og_description', "Cuadro médico completo de {$insurer->name}. Consulta especialistas, centros y hospitales por provincia.")

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => $insurer->name],
    ]])
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "MedicalOrganization",
    "name": "{{ $insurer->name }}",
    "description": "Cuadro médico de {{ $insurer->name }} en España",
    "url": "{{ route('insurer.show', $insurer->slug) }}"
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, {{ $color }}15 0%, {{ $color }}05 100%);">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full opacity-[0.07]" style="background-color: {{ $color }}; transform: translate(30%, -40%);"></div>
        <div class="absolute bottom-0 left-0 w-60 h-60 rounded-full opacity-[0.05]" style="background-color: {{ $color }}; transform: translate(-20%, 40%);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
            {{-- Logo --}}
            <div class="shrink-0">
                @if($insurer->logo ?? false)
                    <img src="{{ asset($insurer->logo) }}"
                         alt="Logo {{ $insurer->name }}"
                         class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl object-contain bg-white border border-gray-200 p-3 shadow-sm"
                         width="96" height="96">
                @else
                    <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl flex items-center justify-center text-white text-3xl lg:text-4xl font-bold shadow-lg"
                         style="background-color: {{ $color }}">
                        {{ $initial }}
                    </div>
                @endif
            </div>

            {{-- Title --}}
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
                    Cuadro Médico {{ $insurer->name }}
                    <span class="text-primary">{{ $year }}</span>
                </h1>
                <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
                    Consulta todos los médicos, especialistas, centros y hospitales de {{ $insurer->name }} organizados por provincia.
                </p>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="lg:grid lg:grid-cols-3 lg:gap-12">

        {{-- Main content --}}
        <div class="lg:col-span-2">

            {{-- Products section --}}
            @if(isset($products) && $products->count() > 0)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-box-open text-primary text-lg"></i>
                    Productos de {{ $insurer->name }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($products as $product)
                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:border-gray-300 hover:shadow-sm transition-all">
                        <h3 class="font-bold text-ink text-[15px] mb-1.5">{{ $product->name }}</h3>
                        @if($product->description ?? false)
                            <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Province grid --}}
            <section class="mb-12" id="provincias">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-2 flex items-center gap-2.5">
                    <i class="fa-solid fa-map-location-dot text-primary text-lg"></i>
                    Consulta por Provincia
                </h2>
                <p class="text-gray-500 text-sm mb-6">
                    Selecciona una provincia para ver el cuadro médico de {{ $insurer->name }} en tu zona.
                </p>

                @if(isset($provincesByRegion) && count($provincesByRegion) > 0)
                    <div class="space-y-8">
                        @foreach($provincesByRegion as $region => $provinces)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <span class="w-8 h-px bg-gray-200"></span>
                                {{ $region }}
                            </h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                @foreach($provinces as $province)
                                <a href="{{ route('insurer.province', [$insurer->slug, $province->slug]) }}"
                                   class="group flex items-center gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3 hover:border-primary/30 hover:bg-primary/[0.02] hover:shadow-sm transition-all">
                                    <span class="text-sm font-medium text-ink group-hover:text-primary transition-colors">{{ $province->name }}</span>
                                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    {{-- Flat list if no regions --}}
                    @if(isset($provinces) && $provinces->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @foreach($provinces as $province)
                        <a href="{{ route('insurer.province', [$insurer->slug, $province->slug]) }}"
                           class="group flex items-center gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3 hover:border-primary/30 hover:bg-primary/[0.02] hover:shadow-sm transition-all">
                            <span class="text-sm font-medium text-ink group-hover:text-primary transition-colors">{{ $province->name }}</span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                        <i class="fa-solid fa-map text-gray-200 text-4xl mb-3"></i>
                        <p class="text-gray-400">Las provincias de {{ $insurer->name }} se añadirán próximamente.</p>
                    </div>
                    @endif
                @endif
            </section>

            {{-- Description --}}
            @if($insurer->description ?? false)
            <section class="mb-12">
                <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-info text-primary text-lg"></i>
                    Sobre {{ $insurer->name }}
                </h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6 lg:p-8 prose prose-gray max-w-none prose-headings:text-ink prose-a:text-primary">
                    {!! $insurer->description !!}
                </div>
            </section>
            @endif

            {{-- FAQ --}}
            @if(isset($faqs) && count($faqs) > 0)
                @include('components.faq-accordion', ['faqs' => $faqs, 'title' => "Preguntas Frecuentes sobre {$insurer->name}"])
            @endif

            {{-- CTA --}}
            @include('components.cta-banner', ['insurer_name' => $insurer->name])

        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-1 mt-10 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">

                {{-- Insurer quick info --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-bold text-ink text-sm uppercase tracking-wider mb-4">Información</h3>
                    <dl class="space-y-3 text-sm">
                        @if($insurer->website ?? false)
                        <div class="flex items-start gap-3">
                            <dt class="text-gray-400 shrink-0 w-5 text-center"><i class="fa-solid fa-globe"></i></dt>
                            <dd><a href="{{ $insurer->website }}" target="_blank" rel="noopener nofollow" class="text-primary hover:underline break-all">{{ parse_url($insurer->website, PHP_URL_HOST) }}</a></dd>
                        </div>
                        @endif
                        @if($insurer->phone ?? false)
                        <div class="flex items-start gap-3">
                            <dt class="text-gray-400 shrink-0 w-5 text-center"><i class="fa-solid fa-phone"></i></dt>
                            <dd class="text-ink">{{ $insurer->phone }}</dd>
                        </div>
                        @endif
                        @if(isset($provinces))
                        <div class="flex items-start gap-3">
                            <dt class="text-gray-400 shrink-0 w-5 text-center"><i class="fa-solid fa-map-pin"></i></dt>
                            <dd class="text-ink">{{ $provinces->count() }} provincias</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                {{-- Other insurers --}}
                @if(isset($otherInsurers) && $otherInsurers->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-bold text-ink text-sm uppercase tracking-wider mb-4">Otras Aseguradoras</h3>
                    <ul class="space-y-2">
                        @foreach($otherInsurers->take(10) as $other)
                        <li>
                            <a href="{{ route('insurer.show', $other->slug) }}"
                               class="flex items-center gap-3 py-2 px-3 -mx-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                @if($other->logo)
                                    <img src="{{ asset($other->logo) }}" alt="{{ $other->name }}" class="w-8 h-8 rounded-lg object-contain bg-white border border-gray-100 p-0.5 shrink-0" width="32" height="32" loading="lazy">
                                @else
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0" style="background-color: {{ $other->brand_color ?? '#1d5fa7' }}">{{ mb_strtoupper(mb_substr($other->name, 0, 1)) }}</div>
                                @endif
                                <span class="text-sm text-gray-600 group-hover:text-primary transition-colors">{{ $other->name }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('home') }}" class="block text-center text-sm text-primary font-medium mt-4 hover:underline">
                        Ver todas las aseguradoras
                    </a>
                </div>
                @endif

                {{-- Mini CTA --}}
                <div class="bg-gradient-to-br from-accent to-accent-dark rounded-xl p-6 text-white">
                    <i class="fa-solid fa-shield-heart text-2xl mb-3 opacity-80"></i>
                    <h3 class="font-bold text-base mb-2">¿Buscas el mejor precio?</h3>
                    <p class="text-sm text-green-100 mb-4 leading-relaxed">
                        Compara seguros de salud y encuentra la mejor oferta para ti.
                    </p>
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=sidebar&utm_content={{ $insurer->slug }}" target="_blank" rel="noopener"
                       class="block w-full text-center px-4 py-2.5 bg-white text-accent font-semibold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Comparar precios
                    </a>
                </div>
            </div>
        </aside>

    </div>
</div>

@endsection
