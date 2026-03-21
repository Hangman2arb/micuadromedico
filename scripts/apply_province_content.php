<?php
/**
 * Apply province-specific editorial content from Qwen batch results.
 * Usage: php scripts/apply_province_content.php [--dry-run]
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$dry_run = in_array('--dry-run', $argv);
$results_file = __DIR__ . '/../storage/batch/content_results.jsonl';

if (!file_exists($results_file)) {
    $batchIdFile = __DIR__ . '/../storage/batch/content_batch_id.txt';
    if (!file_exists($batchIdFile)) { echo "No batch submitted\n"; exit(1); }
    
    $batchId = trim(file_get_contents($batchIdFile));
    $QWEN_ENDPOINT = 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1';
    $QWEN_API_KEY = 'sk-26dc747172b045389000a4d54595f290';
    
    $ch = curl_init("$QWEN_ENDPOINT/batches/$batchId");
    curl_setopt_array($ch, [CURLOPT_HTTPHEADER => ["Authorization: Bearer $QWEN_API_KEY"], CURLOPT_RETURNTRANSFER => true]);
    $resp = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $status = $resp['status'] ?? 'unknown';
    $completed = $resp['request_counts']['completed'] ?? 0;
    $total = $resp['request_counts']['total'] ?? 0;
    echo "Batch status: $status ($completed/$total)\n";
    
    if ($status !== 'completed') { echo "Not ready yet.\n"; exit(0); }
    
    $outputId = $resp['output_file_id'];
    $ch = curl_init("$QWEN_ENDPOINT/files/$outputId/content");
    curl_setopt_array($ch, [CURLOPT_HTTPHEADER => ["Authorization: Bearer $QWEN_API_KEY"], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 300]);
    $content = curl_exec($ch);
    curl_close($ch);
    file_put_contents($results_file, $content);
    echo "Downloaded results\n";
}

echo "Applying province content...\n";
$lines = file($results_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$insurerIds = DB::table('insurers')->pluck('id', 'slug')->toArray();
$provinceIds = DB::table('provinces')->pluck('id', 'slug')->toArray();

$applied = 0;
$skipped = 0;

foreach ($lines as $line) {
    $data = json_decode($line, true);
    $customId = $data['custom_id'] ?? '';
    if (!preg_match('/^content_(.+?)_(.+)$/', $customId, $m)) continue;
    
    $insurerSlug = $m[1];
    $provinceSlug = $m[2];
    
    if (!isset($insurerIds[$insurerSlug]) || !isset($provinceIds[$provinceSlug])) {
        $skipped++;
        continue;
    }
    
    $content = $data['response']['body']['choices'][0]['message']['content'] ?? '';
    $content = trim($content);
    $content = preg_replace('/^```html\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);
    
    if (strlen($content) < 100) {
        $skipped++;
        continue;
    }
    
    if ($dry_run) {
        if ($applied < 5) echo "  [$insurerSlug/$provinceSlug] " . strlen($content) . " chars\n";
    } else {
        DB::table('insurer_province')
            ->where('insurer_id', $insurerIds[$insurerSlug])
            ->where('province_id', $provinceIds[$provinceSlug])
            ->update(['content_html' => $content]);
    }
    $applied++;
}

echo "Content applied: $applied (skipped: $skipped)" . ($dry_run ? " (dry run)" : "") . "\n";
