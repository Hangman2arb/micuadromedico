@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('og_title', 'Cuadros Médicos por Provincia — España ' . date('Y'))

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Provincias'],
    ]])
@endsection

@section('content')
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1d5fa715 0%, #1d5fa705 100%);">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
            Cuadros Médicos por <span class="text-primary">Provincia</span>
        </h1>
        <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
            Encuentra todas las aseguradoras disponibles en tu provincia. Consulta cuadros médicos, especialidades y descarga PDFs.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="space-y-10">
        @foreach($provincesByRegion as $region => $provinces)
        <div>
            <h2 class="text-lg font-bold text-ink mb-4 flex items-center gap-2.5">
                <i class="fa-solid fa-map text-primary text-base"></i>
                {{ $region }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($provinces as $province)
                <a href="{{ url('/provincias/' . $province->slug) }}"
                   class="group flex items-center gap-3 bg-white rounded-xl border border-gray-200 px-4 py-3.5 hover:border-primary/30 hover:shadow-sm transition-all">
                    <i class="fa-solid fa-location-dot text-primary/50 group-hover:text-primary text-sm transition-colors"></i>
                    <span class="text-sm font-medium text-ink group-hover:text-primary transition-colors">{{ $province->name }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12">
        @include('components.cta-banner')
    </div>
</div>
@endsection
