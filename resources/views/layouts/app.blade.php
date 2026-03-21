<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Cuadros Médicos de España 2026 — Mi Cuadro Médico')</title>
    <meta name="description" content="@yield('meta_description', 'Consulta el cuadro médico de tu aseguradora actualizado. Encuentra médicos, especialistas y centros de salud por provincia en toda España.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_ES">
    <meta property="og:site_name" content="Mi Cuadro Médico">
    <meta property="og:title" content="@yield('og_title', 'Cuadros Médicos de España 2026 — Mi Cuadro Médico')">
    <meta property="og:description" content="@yield('og_description', 'Consulta el cuadro médico de tu aseguradora actualizado. Encuentra médicos, especialistas y centros de salud por provincia.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Cuadros Médicos de España 2026 — Mi Cuadro Médico')">
    <meta name="twitter:description" content="@yield('og_description', 'Consulta el cuadro médico de tu aseguradora actualizado.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1d5fa7',
                        'primary-dark': '#0f3f75',
                        accent: '#27ae60',
                        'accent-dark': '#1e8c4d',
                        ink: '#0a0f14',
                        bg: '#f5f7fb',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Custom styles --}}
    <style>
        [x-cloak] { display: none !important; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #0a0f14;
            background-color: #f5f7fb;
        }

        /* Sticky nav shadow on scroll */
        .nav-scrolled {
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        /* Smooth accordion */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }
        .accordion-content.open {
            max-height: 2000px;
        }
    </style>

    {{-- Organization Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Mi Cuadro Médico",
        "url": "{{ config('app.url') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "description": "Directorio de cuadros médicos de aseguradoras en España. Consulta médicos, especialistas y centros de salud por provincia.",
        "sameAs": []
    }
    </script>

    @yield('schema')
    @yield('head')
</head>
<body class="min-h-screen flex flex-col bg-bg" x-data="{ mobileMenu: false, cookieConsent: localStorage.getItem('cookie_consent') === 'true' }">

    {{-- Navigation --}}
    <nav x-data="{ scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'nav-scrolled bg-white/95 backdrop-blur-md' : 'bg-white'"
         class="sticky top-0 z-50 transition-all duration-300 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-18">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" aria-label="Mi Cuadro Médico — Inicio">
                    <img src="{{ asset('logo.svg') }}" alt="Mi Cuadro Médico" class="h-9 w-auto" width="36" height="36">
                    <div class="flex flex-col">
                        <span class="text-lg font-bold text-ink leading-tight">Mi Cuadro Médico</span>
                        <span class="text-[10px] text-gray-400 leading-tight -mt-0.5 hidden sm:block">Directorio de aseguradoras</span>
                    </div>
                </a>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary rounded-lg hover:bg-primary/5 transition-colors">
                        Aseguradoras
                    </a>
                    @if(Route::has('special-group.show'))
                    <a href="{{ route('special-group.show') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary rounded-lg hover:bg-primary/5 transition-colors">
                        MUFACE
                    </a>
                    <a href="{{ route('special-group.mugeju') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary rounded-lg hover:bg-primary/5 transition-colors">
                        MUGEJU
                    </a>
                    <a href="{{ route('special-group.isfas') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-primary rounded-lg hover:bg-primary/5 transition-colors">
                        ISFAS
                    </a>
                    @endif
                    <a href="https://tupolizadesalud.com/?utm_source=micuadromedico&utm_medium=nav" target="_blank" rel="noopener"
                       class="ml-2 px-4 py-2 text-sm font-semibold text-white bg-accent hover:bg-accent-dark rounded-lg transition-colors">
                        <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 text-xs"></i>Compara Seguros
                    </a>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition" aria-label="Abrir menú">
                    <i x-show="!mobileMenu" class="fa-solid fa-bars text-xl"></i>
                    <i x-show="mobileMenu" x-cloak class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white px-4 pb-4 pt-2">
            <a href="{{ route('home') }}" class="block py-2.5 text-gray-700 font-medium hover:text-primary">Aseguradoras</a>
            @if(Route::has('special-group.show'))
            <a href="{{ route('special-group.show') }}" class="block py-2.5 text-gray-700 font-medium hover:text-primary">MUFACE</a>
            <a href="{{ route('special-group.mugeju') }}" class="block py-2.5 text-gray-700 font-medium hover:text-primary">MUGEJU</a>
            <a href="{{ route('special-group.isfas') }}" class="block py-2.5 text-gray-700 font-medium hover:text-primary">ISFAS</a>
            @endif
            <div class="pt-2 mt-2 border-t border-gray-100">
                <a href="https://tupolizadesalud.com/?utm_source=micuadromedico&utm_medium=nav" target="_blank" rel="noopener"
                   class="block w-full text-center px-4 py-2.5 text-sm font-semibold text-white bg-accent rounded-lg">
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1.5"></i>Compara Seguros
                </a>
            </div>
        </div>
    </nav>

    {{-- Breadcrumbs --}}
    @hasSection('breadcrumbs')
        <div class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                @yield('breadcrumbs')
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-ink text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Top section --}}
            <div class="py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                        <img src="{{ asset('logo.svg') }}" alt="Mi Cuadro Médico" class="h-8 w-auto" width="32" height="32">
                        <span class="text-lg font-bold text-white">Mi Cuadro Médico</span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Directorio actualizado de cuadros médicos de las principales aseguradoras de salud en España. Consulta especialistas, centros y hospitales por provincia.
                    </p>
                </div>

                {{-- Funcionarios --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Funcionarios</h3>
                    <ul class="space-y-2.5">
                        @if(Route::has('special-group.show'))
                        <li><a href="{{ route('special-group.show') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Cuadro Médico MUFACE</a></li>
                        <li><a href="{{ route('special-group.mugeju') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Cuadro Médico MUGEJU</a></li>
                        <li><a href="{{ route('special-group.isfas') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Cuadro Médico ISFAS</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Legal</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('legal') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Aviso Legal</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>

                {{-- CTA --}}
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Seguros de Salud</h3>
                    <p class="text-sm text-gray-400 mb-4">
                        ¿Buscas un seguro de salud? Compara precios y coberturas de las principales aseguradoras.
                    </p>
                    <a href="https://tupolizadesalud.com/?utm_source=micuadromedico&utm_medium=footer" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-dark text-white text-sm font-semibold rounded-lg transition-colors">
                        Compara en Tu Póliza de Salud
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="border-t border-gray-800 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} micuadromedico.es &mdash; Un proyecto de
                    <a href="https://tupolizadesalud.com/?utm_source=micuadromedico&utm_medium=footer_copy" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors">Tu Póliza de Salud</a>
                </p>
                <p class="text-xs text-gray-500">
                    Los cuadros médicos se actualizan periódicamente. Consulta con tu aseguradora para información oficial.
                </p>
            </div>
        </div>
    </footer>

    {{-- Cookie Consent --}}
    <div x-show="!cookieConsent" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 inset-x-0 z-[100] bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.1)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-600 text-center sm:text-left">
                <i class="fa-solid fa-cookie-bite text-primary mr-1.5"></i>
                Utilizamos cookies propias y de terceros para mejorar tu experiencia. Al continuar navegando, aceptas nuestra
                <a href="{{ route('legal') }}" class="text-primary underline hover:text-primary-dark">política de cookies</a>.
            </p>
            <div class="flex items-center gap-3 shrink-0">
                <button @click="cookieConsent = true; localStorage.setItem('cookie_consent', 'true')"
                        class="px-5 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-lg transition-colors">
                    Aceptar
                </button>
                <button @click="cookieConsent = true; localStorage.setItem('cookie_consent', 'true')"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Rechazar
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
