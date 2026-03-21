{{-- CTA Banner component
     Usage: @include('components.cta-banner', ['insurer_name' => 'Adeslas'])
     or:    @include('components.cta-banner') for generic version
--}}
@php
    $name = $insurer_name ?? null;
    $utm = 'utm_source=micuadromedico&utm_medium=cta';
    if ($name) {
        $utm .= '&utm_content=' . Str::slug($name);
    }
@endphp

<section class="mt-12 lg:mt-16">
    <div class="relative bg-gradient-to-br from-primary to-primary-dark rounded-2xl overflow-hidden">
        {{-- Decorative pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/20"></div>
            <div class="absolute -left-10 -bottom-10 w-48 h-48 rounded-full bg-white/10"></div>
        </div>

        <div class="relative px-6 py-10 sm:px-10 sm:py-12 lg:px-14 lg:py-14 text-center">
            <div class="max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-white/15 rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-solid fa-shield-heart text-accent text-sm"></i>
                    <span class="text-white/90 text-xs font-medium uppercase tracking-wider">Compara y ahorra</span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold text-white mb-4 leading-tight">
                    @if($name)
                        ¿Quieres contratar {{ $name }}?
                    @else
                        ¿No tienes seguro de salud?
                    @endif
                </h2>

                <p class="text-blue-100 text-base sm:text-lg mb-8 max-w-xl mx-auto leading-relaxed">
                    @if($name)
                        Compara precios y coberturas de {{ $name }} con otras aseguradoras. Encuentra el mejor seguro de salud para ti.
                    @else
                        Compara precios y coberturas de las principales aseguradoras de salud en Espa&ntilde;a. Encuentra la póliza perfecta para ti y tu familia.
                    @endif
                </p>

                <a href="https://tupolizadesalud.com/?{{ $utm }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2.5 px-7 py-3.5 bg-accent hover:bg-accent-dark text-white font-bold rounded-xl shadow-lg shadow-black/20 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    Comparar seguros de salud
                    <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>

                <p class="text-blue-200/60 text-xs mt-5">
                    <i class="fa-solid fa-lock mr-1"></i>
                    Gratuito y sin compromiso &mdash; tupolizadesalud.com
                </p>
            </div>
        </div>
    </div>
</section>
