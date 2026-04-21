@extends('layouts.app')

@section('title', 'Cuadros Médicos de España 2026 — Mi Cuadro Médico')
@section('meta_description', 'Consulta los cuadros médicos actualizados de todas las aseguradoras de salud en España. Encuentra médicos, especialistas y centros de salud por provincia. MUFACE, MUGEJU e ISFAS.')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Mi Cuadro Médico",
    "url": "{{ config('app.url') }}",
    "description": "Directorio de cuadros médicos de aseguradoras en España",
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ config('app.url') }}/buscar?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="relative bg-gradient-to-br from-primary via-primary to-primary-dark overflow-hidden">
    {{-- Decorative elements --}}
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white/[0.02] rounded-full"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28">
        <div class="max-w-3xl mx-auto text-center">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                <span class="text-white/80 text-xs font-medium uppercase tracking-wider">Actualizado {{ date('Y') }}</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-[3.25rem] font-extrabold text-white leading-tight mb-6">
                Todos los Cuadros Médicos
                <span class="block text-blue-200">de España {{ date('Y') }}</span>
            </h1>

            <p class="text-base sm:text-lg text-blue-100 max-w-2xl mx-auto leading-relaxed mb-10">
                Consulta el cuadro médico de tu aseguradora actualizado. Encuentra médicos, especialistas y centros de salud por provincia.
            </p>

            {{-- Search --}}
            <div class="max-w-xl mx-auto" x-data="{ search: '' }">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        x-model="search"
                        @input="$dispatch('insurer-search', { query: search })"
                        placeholder="Busca tu aseguradora... (ej. Adeslas, Sanitas, Asisa)"
                        class="w-full pl-14 pr-5 py-4 rounded-2xl bg-white shadow-xl shadow-black/10 text-ink text-base placeholder:text-gray-400 border-0 focus:outline-none focus:ring-4 focus:ring-white/30"
                        aria-label="Buscar aseguradora"
                    >
                    <button
                        x-show="search.length > 0"
                        x-cloak
                        @click="search = ''; $dispatch('insurer-search', { query: '' })"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1"
                        aria-label="Limpiar búsqueda"
                    >
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="mt-10 flex items-center justify-center gap-8 sm:gap-12 text-white/70">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-white">{{ $insurers->count() ?? '20+' }}</div>
                    <div class="text-xs mt-1">Aseguradoras</div>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-white">52</div>
                    <div class="text-xs mt-1">Provincias</div>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-white">{{ date('Y') }}</div>
                    <div class="text-xs mt-1">Actualizado</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave separator --}}
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 60V20C240 0 480 40 720 30C960 20 1200 0 1440 20V60H0Z" fill="#f5f7fb"/>
        </svg>
    </div>
</section>

{{-- Insurer grid --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16" x-data="insurerFilter()">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl lg:text-3xl font-bold text-ink">
            Aseguradoras
        </h2>
        <span class="text-sm text-gray-400" x-text="visibleCount + ' aseguradoras'" x-cloak></span>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="insurer-grid">
        @forelse($insurers as $insurer)
            <div class="insurer-item" data-name="{{ mb_strtolower($insurer->name) }}">
                @include('components.insurer-card', ['insurer' => $insurer])
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <i class="fa-solid fa-building text-gray-200 text-5xl mb-4"></i>
                <p class="text-gray-400 text-lg">No hay aseguradoras disponibles.</p>
            </div>
        @endforelse
    </div>

    {{-- No results (search) --}}
    <div x-show="visibleCount === 0 && searching" x-cloak class="py-16 text-center">
        <i class="fa-solid fa-magnifying-glass text-gray-200 text-5xl mb-4"></i>
        <p class="text-gray-500 text-lg mb-2">No se encontraron aseguradoras</p>
        <p class="text-gray-400 text-sm">Prueba con otro nombre o comprueba la ortografía.</p>
    </div>
</section>

{{-- Browse by province (SEO hub → spokes) --}}
@if(isset($provincesByRegion) && $provincesByRegion->count() > 0)
<section class="bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex items-end justify-between mb-8 flex-wrap gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-primary/5 rounded-full px-4 py-1.5 mb-3">
                    <i class="fa-solid fa-map-location-dot text-primary text-sm"></i>
                    <span class="text-primary text-xs font-semibold uppercase tracking-wider">Por provincia</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-ink">
                    Cuadros médicos por provincia
                </h2>
                <p class="text-gray-500 mt-2 max-w-xl">
                    Encuentra los cuadros médicos de todas las aseguradoras disponibles en tu provincia.
                </p>
            </div>
            <a href="{{ url('/provincias') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Ver todas las provincias
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
            @foreach($provincesByRegion as $region => $provinces)
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2.5">{{ $region }}</h3>
                <ul class="space-y-1">
                    @foreach($provinces as $p)
                    <li>
                        <a href="{{ url('/provincias/' . $p->slug) }}"
                           class="inline-flex items-center gap-1.5 text-sm text-gray-700 hover:text-primary transition-colors py-0.5">
                            <i class="fa-solid fa-chevron-right text-[9px] text-gray-300"></i>
                            {{ $p->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Browse by specialty (SEO hub → spokes) --}}
@if(isset($topSpecialties) && $topSpecialties->count() > 0)
<section class="bg-bg border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="flex items-end justify-between mb-8 flex-wrap gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-primary/5 rounded-full px-4 py-1.5 mb-3">
                    <i class="fa-solid fa-user-doctor text-primary text-sm"></i>
                    <span class="text-primary text-xs font-semibold uppercase tracking-wider">Por especialidad</span>
                </div>
                <h2 class="text-2xl lg:text-3xl font-bold text-ink">
                    Aseguradoras por especialidad médica
                </h2>
                <p class="text-gray-500 mt-2 max-w-xl">
                    Consulta qué aseguradoras incluyen cada especialidad en su cuadro médico.
                </p>
            </div>
            <a href="{{ url('/especialidades') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Ver todas las especialidades
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($topSpecialties as $s)
            <a href="{{ route('specialty.show', $s->slug) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-sm text-gray-700 rounded-xl border border-gray-200 hover:bg-primary/5 hover:text-primary hover:border-primary/30 transition-colors"
               title="Aseguradoras con {{ $s->name }}">
                <i class="fa-solid fa-stethoscope text-[11px] text-primary/60"></i>
                {{ $s->name }}
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Funcionarios section --}}
@if(Route::has('special-group.show'))
<section class="bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-primary/5 rounded-full px-4 py-1.5 mb-4">
                <i class="fa-solid fa-building-columns text-primary text-sm"></i>
                <span class="text-primary text-xs font-semibold uppercase tracking-wider">Para funcionarios</span>
            </div>
            <h2 class="text-2xl lg:text-3xl font-bold text-ink mb-3">
                Cuadros Médicos para Funcionarios
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto">
                Accede a los cuadros médicos de las mutualidades de funcionarios: MUFACE, MUGEJU e ISFAS.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 max-w-4xl mx-auto">
            @php
            $groups = [
                ['name' => 'MUFACE', 'route' => 'special-group.show', 'desc' => 'Funcionarios de la Administración General del Estado', 'icon' => 'fa-landmark', 'color' => '#1d5fa7'],
                ['name' => 'MUGEJU', 'route' => 'special-group.mugeju', 'desc' => 'Funcionarios de la Administración de Justicia', 'icon' => 'fa-scale-balanced', 'color' => '#8e44ad'],
                ['name' => 'ISFAS', 'route' => 'special-group.isfas', 'desc' => 'Fuerzas Armadas y personal militar', 'icon' => 'fa-shield-halved', 'color' => '#27ae60'],
            ];
            @endphp

            @foreach($groups as $group)
            <a href="{{ route($group['route']) }}"
               class="group block bg-bg rounded-2xl p-6 text-center hover:shadow-lg hover:shadow-gray-200/50 border border-transparent hover:border-gray-200 transition-all duration-300">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-4 flex items-center justify-center text-white text-xl group-hover:scale-110 transition-transform duration-300"
                     style="background-color: {{ $group['color'] }}">
                    <i class="fa-solid {{ $group['icon'] }}"></i>
                </div>
                <h3 class="text-lg font-bold text-ink mb-2 group-hover:text-primary transition-colors">{{ $group['name'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $group['desc'] }}</p>
                <div class="mt-4 text-sm font-semibold text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                    Ver cuadro médico <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA Banner --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @include('components.cta-banner')
</div>

{{-- FAQ Section --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 lg:pb-20">
    @include('components.faq-accordion', [
        'title' => 'Preguntas Frecuentes sobre Cuadros Médicos',
        'faqs' => [
            [
                'question' => '¿Qué es un cuadro médico?',
                'answer' => 'Un cuadro médico es el listado de profesionales sanitarios, centros médicos, hospitales y clínicas que una aseguradora de salud pone a disposición de sus asegurados. Incluye médicos de atención primaria, especialistas, servicios de urgencias, pruebas diagnósticas y hospitales, entre otros.'
            ],
            [
                'question' => '¿Con qué frecuencia se actualizan los cuadros médicos?',
                'answer' => 'Las aseguradoras actualizan sus cuadros médicos de forma periódica, generalmente cada trimestre o cuando se producen altas o bajas de profesionales. En Mi Cuadro Médico actualizamos la información regularmente para ofrecerte los datos más recientes disponibles.'
            ],
            [
                'question' => '¿Puedo usar el cuadro médico en cualquier provincia?',
                'answer' => 'Sí, la mayoría de seguros de salud ofrecen cobertura nacional, por lo que puedes utilizar los servicios del cuadro médico en cualquier provincia de España. Sin embargo, el catálogo de profesionales y centros varía según la provincia. Consulta el cuadro médico de tu aseguradora en la provincia que te interese.'
            ],
            [
                'question' => '¿Qué diferencia hay entre cuadro médico abierto y cerrado?',
                'answer' => 'Un <strong>cuadro médico cerrado</strong> (o concertado) significa que solo puedes acudir a los profesionales y centros incluidos en el listado de tu aseguradora. Un <strong>cuadro médico abierto</strong> permite acudir a cualquier profesional, con o sin reembolso. La mayoría de seguros en España funcionan con cuadro cerrado, que suelen tener primas más bajas.'
            ],
            [
                'question' => '¿Qué es MUFACE y quién puede acceder a su cuadro médico?',
                'answer' => 'MUFACE (Mutualidad General de Funcionarios Civiles del Estado) es la entidad que gestiona la asistencia sanitaria de los funcionarios de la Administración General del Estado y sus beneficiarios. Los funcionarios pueden elegir entre la sanidad pública (Seguridad Social) o una aseguradora privada concertada con MUFACE, como Adeslas, Asisa o DKV.'
            ],
            [
                'question' => '¿Cómo puedo saber qué especialistas tiene mi aseguradora en mi provincia?',
                'answer' => 'En Mi Cuadro Médico puedes buscar tu aseguradora, seleccionar tu provincia y consultar todas las especialidades disponibles. También puedes descargar el cuadro médico completo en PDF cuando esté disponible. Para información en tiempo real, te recomendamos consultar también la web o app de tu aseguradora.'
            ],
        ]
    ])
</div>

@endsection

@section('scripts')
<script>
function insurerFilter() {
    return {
        searching: false,
        visibleCount: document.querySelectorAll('.insurer-item').length,
        init() {
            window.addEventListener('insurer-search', (e) => {
                const query = e.detail.query.toLowerCase().trim();
                this.searching = query.length > 0;
                const items = document.querySelectorAll('.insurer-item');
                let count = 0;
                items.forEach(item => {
                    const name = item.dataset.name;
                    const match = !query || name.includes(query);
                    item.style.display = match ? '' : 'none';
                    if (match) count++;
                });
                this.visibleCount = count;
            });
        }
    }
}
</script>
@endsection
