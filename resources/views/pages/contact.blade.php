@extends('layouts.app')

@section('title', 'Contacto — Mi Cuadro Médico')
@section('meta_description', 'Contacta con Mi Cuadro Médico. Envíanos tus consultas, sugerencias o incidencias sobre los cuadros médicos de aseguradoras en España.')

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Contacto'],
    ]])
@endsection

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-2xl mb-5">
                <i class="fa-solid fa-envelope text-primary text-2xl"></i>
            </div>
            <h1 class="text-3xl lg:text-4xl font-extrabold text-ink mb-4">Contacto</h1>
            <p class="text-gray-500 text-lg max-w-xl mx-auto leading-relaxed">
                ¿Tienes alguna consulta, sugerencia o has detectado un error? Estamos aquí para ayudarte.
            </p>
        </div>

        {{-- Contact form --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-10" x-data="{ sending: false, sent: false }">

            {{-- Success message --}}
            <div x-show="sent" x-cloak class="mb-8 bg-accent/10 border border-accent/20 rounded-xl p-5 text-center">
                <i class="fa-solid fa-circle-check text-accent text-2xl mb-2"></i>
                <h3 class="font-bold text-ink mb-1">Mensaje enviado</h3>
                <p class="text-sm text-gray-600">Gracias por contactarnos. Te responderemos lo antes posible.</p>
            </div>

            <form x-show="!sent" method="POST" action="{{ route('contact') }}" @submit.prevent="sending = true; $el.submit()" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-ink mb-2">Nombre *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-ink placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition"
                               placeholder="Tu nombre">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-ink mb-2">Email *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-ink placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition"
                               placeholder="tu@email.com">
                    </div>
                </div>

                {{-- Subject --}}
                <div>
                    <label for="subject" class="block text-sm font-semibold text-ink mb-2">Asunto *</label>
                    <select id="subject" name="subject" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-ink bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition">
                        <option value="">Selecciona un asunto</option>
                        <option value="consulta">Consulta general</option>
                        <option value="error">Reportar un error en los datos</option>
                        <option value="sugerencia">Sugerencia de mejora</option>
                        <option value="aseguradora">Solicitar añadir una aseguradora</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-sm font-semibold text-ink mb-2">Mensaje *</label>
                    <textarea id="message" name="message" rows="5" required
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-ink placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40 transition resize-y"
                              placeholder="Escribe tu mensaje aquí..."></textarea>
                </div>

                {{-- Privacy --}}
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="privacy" name="privacy" required
                           class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary/50">
                    <label for="privacy" class="text-sm text-gray-500 leading-relaxed">
                        He leído y acepto la <a href="{{ route('legal') }}" class="text-primary hover:underline">política de privacidad</a>. *
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        :disabled="sending"
                        class="w-full sm:w-auto px-8 py-3.5 bg-primary hover:bg-primary-dark disabled:bg-gray-300 text-white font-bold rounded-xl shadow-lg shadow-primary/20 hover:shadow-xl transition-all duration-200 disabled:shadow-none">
                    <span x-show="!sending">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Enviar mensaje
                    </span>
                    <span x-show="sending" x-cloak>
                        <i class="fa-solid fa-spinner animate-spin mr-2"></i>Enviando...
                    </span>
                </button>
            </form>
        </div>

        {{-- Contact info cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-envelope text-primary text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-ink text-sm mb-1">Email</h3>
                    <a href="mailto:info@micuadromedico.es" class="text-sm text-primary hover:underline">info@micuadromedico.es</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-clock text-accent text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-ink text-sm mb-1">Horario de respuesta</h3>
                    <p class="text-sm text-gray-500">Lunes a viernes, 9:00 — 18:00</p>
                </div>
            </div>
        </div>

        {{-- Note --}}
        <div class="mt-8 bg-amber-50 border border-amber-100 rounded-xl p-5 flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
            <div>
                <h3 class="font-semibold text-ink text-sm mb-1">Nota importante</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    micuadromedico.es es un directorio informativo independiente. Para consultas sobre tu póliza, coberturas o trámites, contacta directamente con tu aseguradora. Si buscas contratar un seguro de salud, puedes
                    <a href="https://tupolizadesalud.com/comparador-seguros/?utm_source=micuadromedico&utm_medium=contact" target="_blank" rel="noopener" class="text-primary font-medium hover:underline">comparar precios en Tu Póliza de Salud</a>.
                </p>
            </div>
        </div>

    </div>
</div>

@endsection
