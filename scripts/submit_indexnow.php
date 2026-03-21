<?php
/**
 * Submit all micuadromedico.es URLs to IndexNow.
 * Run once after site is live and DNS has propagated.
 *
 * Usage: php scripts/submit_indexnow.php
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$INDEXNOW_KEY = '96a4e0c629bb2ffc91460e910f6e402f';
$HOST = 'micuadromedico.es';
$BASE = "https://{$HOST}";

// Collect all URLs
$urls = [$BASE . '/'];

// Insurer pages
$insurers = DB::table('insurers')->where('is_active', true)->pluck('slug');
foreach ($insurers as $slug) {
    $urls[] = "{$BASE}/{$slug}";
}

// Insurer+Province pages
$pivots = DB::table('insurer_province')
    ->join('insurers', 'insurers.id', '=', 'insurer_province.insurer_id')
    ->join('provinces', 'provinces.id', '=', 'insurer_province.province_id')
    ->where('insurers.is_active', true)
    ->select('insurers.slug as insurer_slug', 'provinces.slug as province_slug')
    ->get();

foreach ($pivots as $p) {
    $urls[] = "{$BASE}/{$p->insurer_slug}/{$p->province_slug}";
}

// Special groups
foreach (['muface', 'mugeju', 'isfas'] as $slug) {
    $urls[] = "{$BASE}/{$slug}";
}

// Static
$urls[] = "{$BASE}/aviso-legal";
$urls[] = "{$BASE}/contacto";

echo "Total URLs: " . count($urls) . "\n\n";

// Submit in batches of 100 (IndexNow limit)
$batches = array_chunk($urls, 100);
$submitted = 0;

foreach ($batches as $i => $batch) {
    $payload = json_encode([
        'host' => $HOST,
        'key' => $INDEXNOW_KEY,
        'keyLocation' => "{$BASE}/{$INDEXNOW_KEY}.txt",
        'urlList' => $batch,
    ]);

    $ch = curl_init('https://api.indexnow.org/IndexNow');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $count = count($batch);
    $submitted += $count;
    echo "Batch " . ($i + 1) . "/" . count($batches) . ": {$count} URLs → HTTP {$code}\n";
}

echo "\nDone! {$submitted} URLs submitted to IndexNow.\n";
