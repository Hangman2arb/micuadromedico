@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Especialidades'],
    ]])
@endsection

@section('content')
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #1d5fa715 0%, #1d5fa705 100%);">
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-extrabold text-ink leading-tight">
            Especialidades <span class="text-primary">Médicas</span>
        </h1>
        <p class="text-gray-500 text-base lg:text-lg mt-3 max-w-2xl leading-relaxed">
            Consulta qué aseguradoras cubren cada especialidad médica en sus cuadros médicos. 56 especialidades en 3 categorías.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    @php
        $categoryLabels = ['medica' => 'Especialidades Médicas', 'dental' => 'Especialidades Dentales', 'pruebas' => 'Pruebas Diagnósticas'];
        $categoryIcons = ['medica' => 'fa-stethoscope', 'dental' => 'fa-tooth', 'pruebas' => 'fa-microscope'];
    @endphp

    <div class="space-y-10">
        @foreach($specialtiesByCategory as $category => $specialties)
        <section>
            <h2 class="text-xl lg:text-2xl font-bold text-ink mb-5 flex items-center gap-2.5">
                <i class="fa-solid {{ $categoryIcons[$category] ?? 'fa-stethoscope' }} text-primary text-lg"></i>
                {{ $categoryLabels[$category] ?? ucfirst($category) }}
                <span class="text-sm font-normal text-gray-400 ml-2">({{ $specialties->count() }})</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($specialties as $specialty)
                <a href="{{ url('/especialidades/' . $specialty->slug) }}"
                   class="group flex items-center gap-3.5 bg-white rounded-xl border border-gray-200 p-4 hover:border-primary/30 hover:shadow-sm transition-all">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 bg-primary/5 group-hover:bg-primary/10 transition-colors">
                        <i class="fa-solid {{ $categoryIcons[$category] ?? 'fa-stethoscope' }} text-sm text-primary"></i>
                    </div>
                    <span class="font-medium text-sm text-ink group-hover:text-primary transition-colors">{{ $specialty->name }}</span>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                </a>
                @endforeach
            </div>
        </section>
        @endforeach
    </div>

    <div class="mt-12">
        @include('components.cta-banner')
    </div>
</div>
@endsection
