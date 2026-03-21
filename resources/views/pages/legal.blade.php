@extends('layouts.app')

@section('title', 'Aviso Legal y Política de Privacidad — Mi Cuadro Médico')
@section('meta_description', 'Aviso legal, política de privacidad y política de cookies de micuadromedico.es. Información legal de Zemma Brokers Correduría de Seguros.')

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

        <h2>1. Objeto y Aceptación</h2>
        <p>
            ZEMMA BROKERS, S.L.U. opera los sitios web
            <a href="https://www.tupolizadesalud.com">www.tupolizadesalud.com</a>,
            <a href="https://www.tupolizadesalud.es">www.tupolizadesalud.es</a>,
            <a href="https://www.zemmaseguros.com">www.zemmaseguros.com</a>,
            <a href="https://www.micuadromedico.es">www.micuadromedico.es</a>,
            <a href="https://www.ofertadeseguro.com">www.ofertadeseguro.com</a> y
            <a href="https://www.tuofertadeseguros.com">www.tuofertadeseguros.com</a>
            desde C/ Romero, nº32, 45122 - Argés (Toledo), España.
        </p>
        <ul>
            <li><strong>Titular:</strong> ZEMMA BROKERS, S.L.U.</li>
            <li><strong>CIF:</strong> B87548087</li>
            <li><strong>Domicilio social:</strong> C/ Romero, nº32, 45122 - Argés (Toledo)</li>
            <li><strong>Email de contacto:</strong> <a href="mailto:info@zemmaseguros.com">info@zemmaseguros.com</a></li>
            <li><strong>Teléfono:</strong> 910 05 92 97 / +34 637 948 630</li>
            <li><strong>Nº identificador profesional:</strong> 76519</li>
            <li><strong>Clave DGS:</strong> J3368</li>
            <li><strong>Registro Mercantil de Madrid</strong></li>
        </ul>
        <p>
            La navegación por el sitio web atribuye la condición de usuario e implica la aceptación plena y sin reservas de todas y cada una de las disposiciones incluidas en este aviso legal. El usuario se compromete a utilizar el sitio web de conformidad con la ley y el presente aviso legal.
        </p>

        <h2>2. Información Legal</h2>
        <p>
            En cumplimiento de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), y la Ley 56/2007, de 28 de diciembre, de Medidas de Impulso de la Sociedad de la Información, se informa que los servicios prestados a través de este sitio web son de carácter gratuito y accesible. El acceso a determinados servicios puede requerir la cumplimentación de formularios, garantizando el usuario la autenticidad y veracidad de los datos proporcionados.
        </p>

        <h2>3. Condiciones de Acceso y Uso</h2>
        <p>
            El usuario se compromete a no utilizar el sitio web ni los servicios ofrecidos para actividades ilícitas, contrarias a la buena fe y al ordenamiento legal, o para:
        </p>
        <ul>
            <li>Difundir contenidos de carácter ilegal, violento, pornográfico, racista, xenófobo u ofensivo.</li>
            <li>Introducir virus informáticos o realizar actuaciones que alteren, deterioren o destruyan los sistemas informáticos de ZEMMA BROKERS o de terceros.</li>
            <li>Acceder a cuentas de correo o áreas restringidas de otros usuarios.</li>
            <li>Vulnerar derechos de propiedad intelectual o industrial de terceros.</li>
            <li>Suplantar la identidad de otros usuarios o de entidades públicas.</li>
            <li>Reproducir, copiar, distribuir o modificar contenidos del sitio web sin autorización.</li>
            <li>Recopilar datos con fines publicitarios sin consentimiento.</li>
        </ul>

        <h2>4. Exclusión de Garantías y Responsabilidad</h2>
        <p>
            La información sobre cuadros médicos publicada en este sitio tiene carácter meramente orientativo e informativo. ZEMMA BROKERS no garantiza la exactitud, actualidad o integridad de dicha información. Para obtener información oficial y actualizada, recomendamos consultar directamente con su aseguradora.
        </p>
        <p>
            Los nombres comerciales, marcas y logotipos de las aseguradoras que aparecen en este sitio son propiedad de sus respectivos titulares y se muestran únicamente con fines informativos.
        </p>
        <p>
            ZEMMA BROKERS no se responsabiliza de posibles fallos en el acceso, inexactitudes en los contenidos, virus informáticos, vulneraciones de propiedad intelectual por terceros, ni de las políticas de los sitios web enlazados.
        </p>

        <h2>5. Protección de Datos Personales</h2>
        <p>
            De conformidad con lo establecido en la Ley Orgánica 3/2018 de Protección de Datos Personales y garantía de los derechos digitales (LOPDGDD) y el Reglamento General de Protección de Datos (RGPD) UE 2016/679:
        </p>
        <ul>
            <li><strong>Responsable del tratamiento:</strong> ZEMMA BROKERS, S.L.U. — C/ Romero, nº32, 45122 - Argés (Toledo)</li>
            <li><strong>Finalidad:</strong> Gestión de la relación con los clientes, atención de consultas y promoción de servicios.</li>
            <li><strong>Legitimación:</strong> Consentimiento del interesado mediante el formulario de contacto.</li>
            <li><strong>Destinatarios:</strong> No se cederán datos a terceros sin autorización expresa, salvo obligación legal.</li>
            <li><strong>Derechos:</strong> Acceso, rectificación, supresión, portabilidad, limitación y oposición.</li>
            <li><strong>Conservación:</strong> Los datos se conservarán mientras sea necesario para la finalidad del tratamiento.</li>
        </ul>
        <p>
            Para ejercer sus derechos puede dirigirse por escrito al Delegado de Protección de Datos en la dirección indicada o mediante correo electrónico a <a href="mailto:info@zemmaseguros.com">info@zemmaseguros.com</a>.
        </p>
        <p>
            Para darse de baja de comunicaciones comerciales, envíe un correo con asunto "BAJA" a <a href="mailto:info@zemmaseguros.com">info@zemmaseguros.com</a>.
        </p>

        <h2>6. Política de Cookies</h2>
        <p>
            Este sitio web utiliza cookies propias y de terceros para mejorar la experiencia del usuario y analizar el tráfico web.
        </p>
        <p><strong>Tipos de cookies utilizadas:</strong></p>
        <ul>
            <li><strong>Cookies técnicas (propias):</strong> Necesarias para el funcionamiento básico del sitio (consentimiento de cookies, sesión).</li>
            <li><strong>Cookies de análisis (terceros — Google Analytics):</strong> Permiten el análisis estadístico del comportamiento de los usuarios.</li>
        </ul>
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
                        <td>Propia / Técnica</td>
                        <td>Almacena la preferencia de cookies del usuario</td>
                        <td>1 año</td>
                    </tr>
                    <tr>
                        <td>_ga</td>
                        <td>Terceros (Google)</td>
                        <td>Análisis estadístico del tráfico web</td>
                        <td>2 años</td>
                    </tr>
                    <tr>
                        <td>_gid</td>
                        <td>Terceros (Google)</td>
                        <td>Análisis estadístico del tráfico web</td>
                        <td>24 horas</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            El usuario puede configurar su navegador para rechazar cookies o para que le avise cuando se envíe una cookie. Si desactiva las cookies, es posible que algunas funciones del sitio no funcionen correctamente.
        </p>

        <h2>7. Propiedad Intelectual e Industrial</h2>
        <p>
            Todos los contenidos del sitio web (textos, imágenes, diseño gráfico, código fuente, logotipos, marcas y demás elementos) son propiedad de ZEMMA BROKERS o de terceros que han autorizado su uso, y están protegidos por la legislación vigente en materia de propiedad intelectual e industrial.
        </p>
        <p>
            La reproducción, copia, enlace, transmisión, distribución o manipulación de cualquier contenido sin autorización escrita previa constituye una infracción de la legislación de propiedad intelectual e industrial vigente.
        </p>

        <h2>8. Legislación Aplicable y Jurisdicción</h2>
        <p>
            Las presentes condiciones se rigen por la legislación española. Para la resolución de cualquier controversia, las partes se someten a los Juzgados y Tribunales de Madrid, con renuncia expresa a cualquier otro fuero.
        </p>

        <p class="text-sm text-gray-400 mt-8">
            Última actualización: {{ date('d') }} de {{ ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'][date('n')-1] }} de {{ date('Y') }}
        </p>
    </div>
</div>

@endsection
