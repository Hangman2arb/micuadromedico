@extends('layouts.app')

@section('title', 'Aviso Legal y Política de Privacidad — Mi Cuadro Médico')
@section('meta_description', 'Aviso legal, política de privacidad y política de cookies de micuadromedico.es. Información sobre el uso del sitio web y protección de datos.')

@section('breadcrumbs')
    @include('components.breadcrumbs', ['items' => [
        ['label' => 'Inicio', 'url' => route('home')],
        ['label' => 'Aviso Legal'],
    ]])
@endsection

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

    <h1 class="text-3xl lg:text-4xl font-extrabold text-ink mb-10">Aviso Legal y Política de Privacidad</h1>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 lg:p-10 prose prose-gray max-w-none prose-headings:text-ink prose-a:text-primary prose-strong:text-ink">

        <h2>1. Datos Identificativos</h2>
        <p>
            En cumplimiento del deber de información recogido en el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y del Comercio Electrónico, a continuación se reflejan los datos identificativos del titular del sitio web <strong>micuadromedico.es</strong>:
        </p>
        <ul>
            <li><strong>Titular:</strong> Zemma Brokers Correduría de Seguros, S.L.</li>
            <li><strong>Domicilio social:</strong> [Dirección completa]</li>
            <li><strong>CIF:</strong> [CIF de la empresa]</li>
            <li><strong>Email de contacto:</strong> <a href="mailto:info@micuadromedico.es">info@micuadromedico.es</a></li>
            <li><strong>Inscrita en el Registro Mercantil de:</strong> [Datos registrales]</li>
        </ul>

        <h2>2. Objeto del Sitio Web</h2>
        <p>
            micuadromedico.es es un sitio web informativo cuyo objetivo es facilitar la consulta de los cuadros médicos de las aseguradoras de salud que operan en España. La información publicada se obtiene de fuentes públicas y se actualiza periódicamente, si bien no sustituye la consulta directa con la aseguradora correspondiente.
        </p>

        <h2>3. Propiedad Intelectual e Industrial</h2>
        <p>
            Todos los contenidos del sitio web (textos, imágenes, diseño gráfico, código fuente, logotipos, marcas y demás elementos) son propiedad del titular o de terceros que han autorizado su uso, y están protegidos por la legislación vigente en materia de propiedad intelectual e industrial.
        </p>
        <p>
            Los nombres comerciales, marcas y logotipos de las aseguradoras que aparecen en este sitio son propiedad de sus respectivos titulares y se muestran únicamente con fines informativos.
        </p>

        <h2>4. Política de Privacidad</h2>
        <p>
            De conformidad con lo establecido en el Reglamento General de Protección de Datos (RGPD) 2016/679 y la Ley Orgánica 3/2018 de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD):
        </p>
        <ul>
            <li><strong>Responsable del tratamiento:</strong> Zemma Brokers Correduría de Seguros, S.L.</li>
            <li><strong>Finalidad:</strong> Gestión de consultas y solicitudes de información recibidas a través del formulario de contacto.</li>
            <li><strong>Legitimación:</strong> Consentimiento del interesado.</li>
            <li><strong>Destinatarios:</strong> No se ceden datos a terceros, salvo obligación legal.</li>
            <li><strong>Derechos:</strong> Acceso, rectificación, supresión, limitación, portabilidad y oposición.</li>
            <li><strong>Conservación:</strong> Los datos se conservarán mientras sea necesario para la finalidad del tratamiento.</li>
        </ul>
        <p>
            Para ejercer sus derechos puede dirigirse a <a href="mailto:info@micuadromedico.es">info@micuadromedico.es</a>.
        </p>

        <h2>5. Política de Cookies</h2>
        <p>
            Este sitio web utiliza cookies propias y de terceros para mejorar la experiencia del usuario y analizar el tráfico web. Las cookies utilizadas son:
        </p>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th>Cookie</th>
                        <th>Tipo</th>
                        <th>Finalidad</th>
                        <th>Duración</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>cookie_consent</td>
                        <td>Propia</td>
                        <td>Almacena la preferencia de cookies del usuario</td>
                        <td>1 año</td>
                    </tr>
                    <tr>
                        <td>_ga, _gid</td>
                        <td>Terceros (Google)</td>
                        <td>Análisis estadístico del tráfico web</td>
                        <td>2 años / 24 horas</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            Puede configurar su navegador para rechazar cookies o para que le avise cuando se envíe una cookie. Si desactiva las cookies, es posible que algunas funciones del sitio no funcionen correctamente.
        </p>

        <h2>6. Limitación de Responsabilidad</h2>
        <p>
            La información sobre cuadros médicos publicada en este sitio tiene carácter meramente orientativo e informativo. El titular no garantiza la exactitud, actualidad o integridad de dicha información. Para obtener información oficial y actualizada, le recomendamos consultar directamente con su aseguradora.
        </p>
        <p>
            El titular no se hace responsable de los daños o perjuicios que pudieran derivarse del uso de la información contenida en este sitio web.
        </p>

        <h2>7. Legislación Aplicable y Jurisdicción</h2>
        <p>
            Las presentes condiciones se rigen por la legislación española. Para la resolución de cualquier controversia, las partes se someten a los Juzgados y Tribunales de Barcelona, con renuncia expresa a cualquier otro fuero.
        </p>

        <p class="text-sm text-gray-400 mt-8">
            Última actualización: {{ date('d') }} de {{ ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][date('n')-1] }} de {{ date('Y') }}
        </p>
    </div>
</div>

@endsection
