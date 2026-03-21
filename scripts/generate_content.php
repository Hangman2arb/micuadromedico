<?php
/**
 * Generate AI content for micuadromedico.es using Qwen Batch API.
 *
 * Batches:
 *   1. insurer_faq    — 8-10 FAQs per insurer (30 requests)
 *   2. group_faq      — 10 FAQs per special group (3 requests)
 *   3. insurer_desc   — Rich descriptions per insurer (30 requests)
 *
 * Usage:
 *   php scripts/generate_content.php generate         # Create JSONL batch files
 *   php scripts/generate_content.php submit            # Upload & submit to Qwen API
 *   php scripts/generate_content.php status            # Check progress & download
 *   php scripts/generate_content.php apply             # Apply results to database
 *   php scripts/generate_content.php apply --dry-run   # Preview changes
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$QWEN_ENDPOINT = 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1';
$QWEN_API_KEY = 'sk-26dc747172b045389000a4d54595f290';
$QWEN_MODEL = 'qwen-flash';

$STORAGE = __DIR__ . '/../storage/batch';
@mkdir($STORAGE, 0755, true);

$action = $argv[1] ?? 'help';
$dry_run = in_array('--dry-run', $argv);

switch ($action) {

// ============================================================
// GENERATE — Create JSONL batch files
// ============================================================
case 'generate':
    $insurers = DB::table('insurers')->where('is_active', true)->orderBy('sort_order')->get();
    $specialGroups = DB::table('special_groups')->get();

    echo "Insurers: " . $insurers->count() . "\n";
    echo "Special groups: " . $specialGroups->count() . "\n\n";

    // --- Batch 1: Insurer FAQs ---
    $file = "{$STORAGE}/insurer_faq_batch.jsonl";
    $fp = fopen($file, 'w');
    $count = 0;

    foreach ($insurers as $insurer) {
        $prompt = <<<PROMPT
Genera 8-10 preguntas frecuentes (FAQ) sobre el cuadro médico de {$insurer->name}.

CONTEXTO:
- {$insurer->name} es una aseguradora de salud en España
- {$insurer->description}
- Los usuarios buscan información sobre cómo consultar el cuadro médico, especialidades disponibles, cobertura por provincia, descarga de PDF, urgencias, telemedicina, cambio de médico, autorizaciones, etc.

REGLAS:
- Las preguntas deben ser las que haría un usuario real buscando en Google
- Las respuestas deben ser informativas, útiles y en 2-4 frases
- Usa lenguaje natural en español de España
- Menciona "{$insurer->name}" en las preguntas para SEO
- Incluye preguntas sobre: consultar cuadro médico online, especialidades principales, cobertura provincial, PDF del cuadro, urgencias, telemedicina/app, cambio de médico
- NO inventes datos específicos (números exactos de médicos, precios, etc.)

Responde SOLO con JSON array:
[{"q": "¿Pregunta sobre {$insurer->name}?", "a": "Respuesta informativa."}, ...]
PROMPT;

        $line = json_encode([
            'custom_id' => "insurer_faq_{$insurer->id}",
            'method' => 'POST',
            'url' => '/v1/chat/completions',
            'body' => [
                'model' => $QWEN_MODEL,
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un experto en seguros de salud en España y SEO. Respondes SOLO con JSON válido.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2000,
            ],
        ], JSON_UNESCAPED_UNICODE);

        fwrite($fp, $line . "\n");
        $count++;
    }
    fclose($fp);
    echo "Batch 1 (Insurer FAQs): {$count} requests -> {$file}\n";

    // --- Batch 2: Special Group FAQs ---
    $file = "{$STORAGE}/group_faq_batch.jsonl";
    $fp = fopen($file, 'w');
    $count = 0;

    $groupContext = [
        'muface' => 'MUFACE (Mutualidad General de Funcionarios Civiles del Estado) — gestiona la protección social de funcionarios civiles. Concertadas: Adeslas, Asisa, DKV.',
        'mugeju' => 'MUGEJU (Mutualidad General Judicial) — protección social del personal judicial: jueces, fiscales, letrados. Concertadas: Adeslas, Asisa, DKV.',
        'isfas' => 'ISFAS (Instituto Social de las Fuerzas Armadas) — protección social de militares y Guardia Civil. Concertadas: Adeslas, Asisa, DKV.',
    ];

    foreach ($specialGroups as $group) {
        $context = $groupContext[$group->slug] ?? $group->description;
        $prompt = <<<PROMPT
Genera 10 preguntas frecuentes (FAQ) sobre el cuadro médico de {$group->name}.

CONTEXTO:
{$context}

TEMAS A CUBRIR:
- ¿Quién puede acceder a {$group->name}?
- ¿Cómo elegir/cambiar aseguradora?
- ¿Qué aseguradoras están concertadas?
- Plazo de elección (enero de cada año)
- Diferencia entre sanidad pública y privada con {$group->name}
- Recetas y prescripciones farmacéuticas
- Beneficiarios (cónyuge, hijos, familiares dependientes)
- Cobertura fuera de la provincia habitual
- Urgencias con {$group->name}
- Cómo consultar el cuadro médico de cada aseguradora

REGLAS:
- Preguntas reales que buscaría un funcionario/militar en Google
- Respuestas informativas en 2-4 frases, en español de España
- Menciona "{$group->name}" en las preguntas para SEO
- NO inventes datos específicos (fechas exactas, números de teléfono)

Responde SOLO con JSON array:
[{"q": "¿Pregunta sobre {$group->name}?", "a": "Respuesta informativa."}, ...]
PROMPT;

        $line = json_encode([
            'custom_id' => "group_faq_{$group->id}",
            'method' => 'POST',
            'url' => '/v1/chat/completions',
            'body' => [
                'model' => $QWEN_MODEL,
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un experto en seguros de salud para funcionarios en España. Respondes SOLO con JSON válido.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 2500,
            ],
        ], JSON_UNESCAPED_UNICODE);

        fwrite($fp, $line . "\n");
        $count++;
    }
    fclose($fp);
    echo "Batch 2 (Group FAQs): {$count} requests -> {$file}\n";

    // --- Batch 3: Insurer Rich Descriptions ---
    $file = "{$STORAGE}/insurer_desc_batch.jsonl";
    $fp = fopen($file, 'w');
    $count = 0;

    foreach ($insurers as $insurer) {
        $prompt = <<<PROMPT
Escribe una descripción rica y detallada (500-800 palabras) sobre {$insurer->name} y su cuadro médico, en formato HTML.

CONTEXTO ACTUAL:
{$insurer->description}

ESTRUCTURA HTML requerida (usa estos headings exactos):
<h3>Historia y Trayectoria de {$insurer->name}</h3>
<p>2-3 párrafos sobre la historia, grupo empresarial, presencia en España...</p>

<h3>Red de Centros y Profesionales</h3>
<p>Información sobre la red médica, hospitales propios o concertados, número de profesionales...</p>

<h3>Especialidades Médicas Destacadas</h3>
<p>Principales especialidades cubiertas, áreas de excelencia...</p>

<h3>Ventajas del Cuadro Médico de {$insurer->name}</h3>
<ul>
<li><strong>Ventaja 1:</strong> descripción</li>
<li><strong>Ventaja 2:</strong> descripción</li>
...
</ul>

<h3>Cómo Consultar el Cuadro Médico</h3>
<p>Instrucciones para consultar online, app, teléfono...</p>

<h3>Servicios Digitales y Telemedicina</h3>
<p>App, videoconsultas, gestiones online...</p>

REGLAS:
- Solo HTML limpio (h3, p, ul, li, strong). No uses h1, h2 ni clases CSS.
- Escribe en español de España, tono profesional pero accesible
- NO inventes datos numéricos específicos que no conozcas (usa "miles de", "amplia red", etc.)
- Si conoces datos reales de {$insurer->name}, úsalos
- Enfocado en SEO: menciona "cuadro médico de {$insurer->name}" varias veces naturalmente
- NO incluyas precios

Responde SOLO con el HTML, sin markdown ni explicaciones.
PROMPT;

        $line = json_encode([
            'custom_id' => "insurer_desc_{$insurer->id}",
            'method' => 'POST',
            'url' => '/v1/chat/completions',
            'body' => [
                'model' => $QWEN_MODEL,
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un redactor experto en seguros de salud en España. Escribes contenido SEO en HTML limpio.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.4,
                'max_tokens' => 3000,
            ],
        ], JSON_UNESCAPED_UNICODE);

        fwrite($fp, $line . "\n");
        $count++;
    }
    fclose($fp);
    echo "Batch 3 (Insurer Descriptions): {$count} requests -> {$file}\n";

    echo "\nTotal: ~63 requests. Run 'php scripts/generate_content.php submit' to upload batches.\n";
    break;

// ============================================================
// SUBMIT — Upload files and create batches
// ============================================================
case 'submit':
    $batchFiles = ['insurer_faq_batch.jsonl', 'group_faq_batch.jsonl', 'insurer_desc_batch.jsonl'];

    foreach ($batchFiles as $filename) {
        $filepath = "{$STORAGE}/{$filename}";
        if (! file_exists($filepath)) {
            echo "Skip {$filename} — file not found\n";
            continue;
        }

        $lines = count(file($filepath));
        echo "Uploading {$filename} ({$lines} requests)...\n";

        // Upload file
        $ch = curl_init("{$QWEN_ENDPOINT}/files");
        $cfile = new CURLFile($filepath, 'application/jsonl', $filename);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['file' => $cfile, 'purpose' => 'batch'],
            CURLOPT_HTTPHEADER => ["Authorization: Bearer {$QWEN_API_KEY}"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($resp, true);
        if ($code !== 200 || empty($data['id'])) {
            echo "  Upload failed (HTTP {$code}): " . ($data['error']['message'] ?? $resp) . "\n";
            continue;
        }
        $file_id = $data['id'];
        echo "  Uploaded: {$file_id}\n";

        // Create batch
        $ch = curl_init("{$QWEN_ENDPOINT}/batches");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'input_file_id' => $file_id,
                'endpoint' => '/v1/chat/completions',
                'completion_window' => '24h',
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$QWEN_API_KEY}",
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($resp, true);
        if (empty($data['id'])) {
            echo "  Batch creation failed: " . ($data['error']['message'] ?? $resp) . "\n";
            continue;
        }

        $batch_id = $data['id'];
        echo "  Batch created: {$batch_id}\n";

        // Save batch ID
        $type = str_replace('_batch.jsonl', '', $filename);
        file_put_contents("{$STORAGE}/{$type}_batch_id.txt", $batch_id);
    }
    echo "\nRun 'php scripts/generate_content.php status' to check progress.\n";
    break;

// ============================================================
// STATUS — Check batch progress and download results
// ============================================================
case 'status':
    $types = ['insurer_faq', 'group_faq', 'insurer_desc'];

    foreach ($types as $type) {
        $batch_file = "{$STORAGE}/{$type}_batch_id.txt";
        if (! file_exists($batch_file)) {
            echo strtoupper($type) . ": no batch submitted\n\n";
            continue;
        }
        $batch_id = trim(file_get_contents($batch_file));

        $ch = curl_init("{$QWEN_ENDPOINT}/batches/{$batch_id}");
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ["Authorization: Bearer {$QWEN_API_KEY}"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($resp, true);
        $status = $data['status'] ?? 'unknown';
        $total = $data['request_counts']['total'] ?? 0;
        $completed = $data['request_counts']['completed'] ?? 0;
        $failed = $data['request_counts']['failed'] ?? 0;

        echo strtoupper($type) . " ({$batch_id}):\n";
        echo "  Status:    {$status}\n";
        echo "  Progress:  {$completed}/{$total}" . ($failed > 0 ? " ({$failed} failed)" : '') . "\n";

        if ($status === 'completed' && ! empty($data['output_file_id'])) {
            $output_id = $data['output_file_id'];
            echo "  Output:    {$output_id}\n";

            // Download results
            echo "  Downloading results...\n";
            $ch = curl_init("{$QWEN_ENDPOINT}/files/{$output_id}/content");
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => ["Authorization: Bearer {$QWEN_API_KEY}"],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 120,
            ]);
            $content = curl_exec($ch);
            curl_close($ch);

            file_put_contents("{$STORAGE}/{$type}_results.jsonl", $content);
            $result_lines = substr_count($content, "\n");
            echo "  Saved: {$STORAGE}/{$type}_results.jsonl ({$result_lines} results)\n";
        }
        echo "\n";
    }
    break;

// ============================================================
// APPLY — Process results and update database
// ============================================================
case 'apply':
    $stats = ['insurer_faqs' => 0, 'group_faqs' => 0, 'descriptions' => 0];

    // --- Apply Insurer FAQs ---
    $results_file = "{$STORAGE}/insurer_faq_results.jsonl";
    if (file_exists($results_file)) {
        echo "Applying insurer FAQs...\n";
        $lines = file($results_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            $custom_id = $data['custom_id'] ?? '';
            if (! preg_match('/^insurer_faq_(\d+)$/', $custom_id, $m)) continue;
            $insurer_id = (int) $m[1];

            $content = $data['response']['body']['choices'][0]['message']['content'] ?? '';
            $content = trim($content);
            $content = preg_replace('/^```json\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);

            $faqs = json_decode($content, true);
            if (! is_array($faqs) || empty($faqs)) {
                echo "  [insurer:{$insurer_id}] Invalid JSON, skipping\n";
                continue;
            }

            $sortOrder = 0;
            foreach ($faqs as $qa) {
                $q = trim($qa['q'] ?? $qa['question'] ?? '');
                $a = trim($qa['a'] ?? $qa['answer'] ?? '');
                if (empty($q) || empty($a)) continue;

                if ($dry_run) {
                    echo "  [insurer:{$insurer_id}] Q: {$q}\n";
                } else {
                    DB::table('faq_items')->updateOrInsert(
                        [
                            'faqable_type' => 'App\\Models\\Insurer',
                            'faqable_id' => $insurer_id,
                            'question' => $q,
                        ],
                        [
                            'answer' => $a,
                            'sort_order' => $sortOrder++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
                $stats['insurer_faqs']++;
            }
        }
        echo "Insurer FAQs: {$stats['insurer_faqs']}" . ($dry_run ? " (dry run)" : "") . "\n\n";
    }

    // --- Apply Special Group FAQs ---
    $results_file = "{$STORAGE}/group_faq_results.jsonl";
    if (file_exists($results_file)) {
        echo "Applying special group FAQs...\n";
        $lines = file($results_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            $custom_id = $data['custom_id'] ?? '';
            if (! preg_match('/^group_faq_(\d+)$/', $custom_id, $m)) continue;
            $group_id = (int) $m[1];

            $content = $data['response']['body']['choices'][0]['message']['content'] ?? '';
            $content = trim($content);
            $content = preg_replace('/^```json\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);

            $faqs = json_decode($content, true);
            if (! is_array($faqs) || empty($faqs)) {
                echo "  [group:{$group_id}] Invalid JSON, skipping\n";
                continue;
            }

            $sortOrder = 0;
            foreach ($faqs as $qa) {
                $q = trim($qa['q'] ?? $qa['question'] ?? '');
                $a = trim($qa['a'] ?? $qa['answer'] ?? '');
                if (empty($q) || empty($a)) continue;

                if ($dry_run) {
                    echo "  [group:{$group_id}] Q: {$q}\n";
                } else {
                    DB::table('faq_items')->updateOrInsert(
                        [
                            'faqable_type' => 'App\\Models\\SpecialGroup',
                            'faqable_id' => $group_id,
                            'question' => $q,
                        ],
                        [
                            'answer' => $a,
                            'sort_order' => $sortOrder++,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
                $stats['group_faqs']++;
            }
        }
        echo "Group FAQs: {$stats['group_faqs']}" . ($dry_run ? " (dry run)" : "") . "\n\n";
    }

    // --- Apply Insurer Descriptions ---
    $results_file = "{$STORAGE}/insurer_desc_results.jsonl";
    if (file_exists($results_file)) {
        echo "Applying insurer descriptions...\n";
        $lines = file($results_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            $custom_id = $data['custom_id'] ?? '';
            if (! preg_match('/^insurer_desc_(\d+)$/', $custom_id, $m)) continue;
            $insurer_id = (int) $m[1];

            $content = $data['response']['body']['choices'][0]['message']['content'] ?? '';
            $content = trim($content);
            // Remove markdown code fences if present
            $content = preg_replace('/^```html\s*/i', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);

            if (strlen($content) < 200) {
                echo "  [insurer:{$insurer_id}] Content too short, skipping\n";
                continue;
            }

            $insurer_name = DB::table('insurers')->where('id', $insurer_id)->value('name');

            if ($dry_run) {
                echo "  [insurer:{$insurer_id}] {$insurer_name}: " . strlen($content) . " chars\n";
            } else {
                DB::table('insurers')->where('id', $insurer_id)->update([
                    'description' => $content,
                    'updated_at' => now(),
                ]);
            }
            $stats['descriptions']++;
        }
        echo "Descriptions updated: {$stats['descriptions']}" . ($dry_run ? " (dry run)" : "") . "\n\n";
    }

    $total = array_sum($stats);
    if ($total === 0) {
        echo "No results found. Run 'status' first to download completed results.\n";
    } else {
        echo "Done!" . ($dry_run ? " (dry run — no changes applied)" : "") . "\n";
    }
    break;

default:
    echo "micuadromedico.es — AI Content Generator (Qwen Batch API)\n\n";
    echo "Usage:\n";
    echo "  php scripts/generate_content.php generate         # Create JSONL batch files\n";
    echo "  php scripts/generate_content.php submit            # Upload & submit to Qwen API\n";
    echo "  php scripts/generate_content.php status            # Check progress & download results\n";
    echo "  php scripts/generate_content.php apply             # Apply results to database\n";
    echo "  php scripts/generate_content.php apply --dry-run   # Preview changes\n";
    echo "\nBatches:\n";
    echo "  1. insurer_faq  — 8-10 FAQs per insurer (30 requests)\n";
    echo "  2. group_faq    — 10 FAQs per special group (3 requests)\n";
    echo "  3. insurer_desc — Rich HTML descriptions per insurer (30 requests)\n";
    echo "  Total: ~63 requests\n";
    break;
}
