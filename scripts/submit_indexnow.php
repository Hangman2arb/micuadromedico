<?php
/**
 * MCM URL indexing: submit to IndexNow (Bing/Yandex) + Google Indexing API.
 *
 * Priority queue (same pattern as tupoliza):
 *   1. NEW       — URL never submitted
 *   2. UPDATED   — insurer/province/specialty with updated_at > last submitted_at
 *   3. STALE     — submitted >30 days ago (gentle refresh)
 *
 * Google Indexing API: capped at --limit URLs per run (quota is 200/day/project).
 * IndexNow: up to --indexnow-limit URLs per run.
 *
 * State tracked in storage/app/indexing_state.json.
 *
 * Usage:
 *   php scripts/submit_indexnow.php [--limit=50] [--indexnow-limit=500]
 *                                    [--stale-days=30] [--dry-run] [--status] [--reset]
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// ---- Parse CLI opts ----
$opts = getopt('', ['limit:', 'indexnow-limit:', 'stale-days:', 'dry-run', 'reset', 'status']);
$limit = (int) ($opts['limit'] ?? 50);
$indexnow_limit = (int) ($opts['indexnow-limit'] ?? 500);
$stale_days = (int) ($opts['stale-days'] ?? 30);
$dry_run = isset($opts['dry-run']);
$reset = isset($opts['reset']);
$show_status = isset($opts['status']);

// ---- Config ----
$INDEXNOW_KEY = 'mcm6a1e0ceed59ed8014d677e12';
$HOST = 'micuadromedico.es';
$BASE = "https://{$HOST}";
$STATE_FILE = __DIR__ . '/../storage/app/indexing_state.json';
$GOOGLE_SA_FILE = __DIR__ . '/../storage/google-service-account.json'; // optional

// ---- Load state ----
$state = file_exists($STATE_FILE) ? json_decode(file_get_contents($STATE_FILE), true) : [];
if ($reset || empty($state)) {
    $state = ['submitted' => [], 'errors' => [], 'started' => date('Y-m-d H:i:s')];
}

// ---- Collect all URLs with updated_at metadata ----
$all_urls = []; // url => ['type' => ..., 'updated_at' => ...]

$all_urls["{$BASE}/"] = ['type' => 'home', 'updated_at' => null];

// Insurer landings
$insurers = DB::table('insurers')->where('is_active', true)
    ->select('slug', 'updated_at')->get();
foreach ($insurers as $i) {
    $all_urls["{$BASE}/{$i->slug}"] = ['type' => 'insurer', 'updated_at' => $i->updated_at];
}

// Insurer + Province pivots
$pivots = DB::table('insurer_province')
    ->join('insurers', 'insurers.id', '=', 'insurer_province.insurer_id')
    ->join('provinces', 'provinces.id', '=', 'insurer_province.province_id')
    ->where('insurers.is_active', true)
    ->select(
        'insurers.slug as insurer_slug',
        'provinces.slug as province_slug',
        'insurer_province.updated_at as updated_at'
    )->get();
foreach ($pivots as $p) {
    $all_urls["{$BASE}/{$p->insurer_slug}/{$p->province_slug}"] =
        ['type' => 'insurer_province', 'updated_at' => $p->updated_at];
}

// Special groups
foreach (['muface', 'mugeju', 'isfas'] as $slug) {
    $all_urls["{$BASE}/{$slug}"] = ['type' => 'special_group', 'updated_at' => null];
}

// Insurer + Special group (e.g., /adeslas/muface)
try {
    $special_pivots = DB::table('insurer_special_group')
        ->join('insurers', 'insurers.id', '=', 'insurer_special_group.insurer_id')
        ->join('special_groups', 'special_groups.id', '=', 'insurer_special_group.special_group_id')
        ->where('insurers.is_active', true)
        ->select(
            'insurers.slug as insurer_slug',
            'special_groups.slug as group_slug',
            'insurer_special_group.updated_at as updated_at'
        )->get();
    foreach ($special_pivots as $p) {
        $all_urls["{$BASE}/{$p->insurer_slug}/{$p->group_slug}"] =
            ['type' => 'insurer_special_group', 'updated_at' => $p->updated_at];
    }
} catch (Exception $e) {}

// Provinces index + landings
$all_urls["{$BASE}/provincias"] = ['type' => 'static', 'updated_at' => null];
$provinces = DB::table('provinces')->select('slug', 'updated_at')->get();
foreach ($provinces as $p) {
    $all_urls["{$BASE}/provincias/{$p->slug}"] = ['type' => 'province', 'updated_at' => $p->updated_at];
}

// Specialties index + landings
$all_urls["{$BASE}/especialidades"] = ['type' => 'static', 'updated_at' => null];
$specialties = DB::table('specialties')->select('slug', 'updated_at')->get();
foreach ($specialties as $s) {
    $all_urls["{$BASE}/especialidades/{$s->slug}"] = ['type' => 'specialty', 'updated_at' => $s->updated_at];
}

// Static
$all_urls["{$BASE}/aviso-legal"] = ['type' => 'static', 'updated_at' => null];
$all_urls["{$BASE}/contacto"] = ['type' => 'static', 'updated_at' => null];

$total_urls = count($all_urls);

// ---- Classify queue ----
$now = time();
$stale_threshold = $now - ($stale_days * 86400);
$new_queue = $updated_queue = $stale_queue = [];
$settled_count = 0;

foreach ($all_urls as $url => $meta) {
    $submitted_at = $state['submitted'][$url] ?? null;
    if ($submitted_at === null) {
        $new_queue[] = ['url' => $url, 'type' => $meta['type'], 'reason' => 'new'];
        continue;
    }
    $submitted_ts = strtotime($submitted_at);
    if ($meta['updated_at']) {
        $updated_ts = strtotime($meta['updated_at']);
        if ($updated_ts > $submitted_ts + 300) {
            $updated_queue[] = ['url' => $url, 'type' => $meta['type'], 'reason' => 'updated', 'submitted_at' => $submitted_at];
            continue;
        }
    }
    if ($submitted_ts < $stale_threshold) {
        $stale_queue[] = ['url' => $url, 'type' => $meta['type'], 'reason' => 'stale', 'submitted_at' => $submitted_at, 'submitted_ts' => $submitted_ts];
        continue;
    }
    $settled_count++;
}
usort($stale_queue, fn($a, $b) => $a['submitted_ts'] <=> $b['submitted_ts']);
$queue = array_merge($new_queue, $updated_queue, $stale_queue);

// ---- Status mode ----
if ($show_status) {
    echo "\n  MCM INDEXING STATUS\n";
    echo str_repeat('=', 55) . "\n";
    echo "  Total URLs:        {$total_urls}\n";
    echo "  Settled:           {$settled_count}\n";
    echo "  NEW:               " . count($new_queue) . "\n";
    echo "  UPDATED:           " . count($updated_queue) . "\n";
    echo "  STALE (>{$stale_days}d):     " . count($stale_queue) . "\n";
    echo "  Queue total:       " . count($queue) . "\n";
    echo "  Errors:            " . count($state['errors']) . "\n\n";
    $type_counts = [];
    foreach ($all_urls as $url => $meta) {
        $type_counts[$meta['type']] = ($type_counts[$meta['type']] ?? 0) + 1;
    }
    echo "  By type:\n";
    foreach ($type_counts as $type => $count) {
        echo "    {$type}: {$count}\n";
    }
    echo "\n";
    exit(0);
}

if (empty($queue)) {
    echo "All {$total_urls} URLs settled. Nothing to do.\n";
    exit(0);
}

// ---- Google Indexing API submission ----
$google_batch = array_slice($queue, 0, $limit);
$google_batch_size = count($google_batch);

echo ($dry_run ? "[DRY RUN] " : "");
echo "Queue: " . count($queue) . " URLs ("
    . count($new_queue) . " new, "
    . count($updated_queue) . " updated, "
    . count($stale_queue) . " stale)\n";
echo "Google batch: {$google_batch_size} URLs\n\n";

$google_success = 0;
$google_errors = 0;
$quota_hit = false;

// Google Indexing API via service account
function google_get_access_token_mcm($sa_path) {
    static $cache = null;
    if ($cache && $cache['expires'] > time() + 60) return $cache['token'];
    if (!file_exists($sa_path)) return null;
    $sa = json_decode(file_get_contents($sa_path), true);
    if (empty($sa['private_key']) || empty($sa['client_email'])) return null;

    $now = time();
    $b64 = fn($d) => rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
    $header = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $claims = $b64(json_encode([
        'iss' => $sa['client_email'],
        'scope' => 'https://www.googleapis.com/auth/indexing',
        'aud' => $sa['token_uri'],
        'iat' => $now,
        'exp' => $now + 3600,
    ]));
    $signing = "$header.$claims";
    $pkey = openssl_pkey_get_private($sa['private_key']);
    if (!$pkey) return null;
    openssl_sign($signing, $sig, $pkey, OPENSSL_ALGO_SHA256);
    $jwt = $signing . '.' . $b64($sig);

    $ch = curl_init($sa['token_uri']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code !== 200) return null;
    $data = json_decode($resp, true);
    $cache = ['token' => $data['access_token'], 'expires' => $now + ($data['expires_in'] ?? 3600)];
    return $cache['token'];
}

$google_token = $dry_run ? 'dry-run-token' : google_get_access_token_mcm($GOOGLE_SA_FILE);
$google_available = !empty($google_token);

if (!$google_available && !$dry_run) {
    echo "(Google Indexing API: service account not available at {$GOOGLE_SA_FILE}, skipping Google — IndexNow only)\n";
}

foreach ($google_batch as $i => $item) {
    $num = $i + 1;
    echo "[G {$num}/{$google_batch_size}] [{$item['reason']}][{$item['type']}] {$item['url']}";

    if ($dry_run) {
        echo " -> SKIP (dry run)\n";
        $google_success++;
        continue;
    }
    if (!$google_available) {
        echo " -> SKIP (no Google SA)\n";
        // Still mark as submitted so IndexNow-only counts
        $state['submitted'][$item['url']] = date('Y-m-d H:i:s');
        continue;
    }

    $ch = curl_init('https://indexing.googleapis.com/v3/urlNotifications:publish');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['url' => $item['url'], 'type' => 'URL_UPDATED']),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $google_token,
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code >= 200 && $code < 300) {
        echo " -> OK\n";
        $state['submitted'][$item['url']] = date('Y-m-d H:i:s');
        unset($state['errors'][$item['url']]);
        $google_success++;
    } else {
        echo " -> ERROR: HTTP {$code} {$resp}\n";
        $google_errors++;
        $state['errors'][$item['url']] = "HTTP {$code}: " . substr($resp, 0, 200);
        if ($code === 429 || stripos($resp, 'quota') !== false) {
            echo "  Google quota reached. Continuing with IndexNow.\n";
            $quota_hit = true;
            break;
        }
    }
    usleep(300000);
}

// ---- IndexNow submission ----
$indexnow_batch = array_slice($queue, 0, $indexnow_limit);
$indexnow_urls = array_column($indexnow_batch, 'url');
$indexnow_code = null;

if (!$dry_run && !empty($indexnow_urls)) {
    echo "\nIndexNow: submitting " . count($indexnow_urls) . " URLs to Bing/Yandex...\n";
    $payload = json_encode([
        'host' => $HOST,
        'key' => $INDEXNOW_KEY,
        'keyLocation' => "{$BASE}/{$INDEXNOW_KEY}.txt",
        'urlList' => $indexnow_urls,
    ]);
    $ch = curl_init('https://api.indexnow.org/indexnow');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    $resp = curl_exec($ch);
    $indexnow_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "IndexNow: HTTP {$indexnow_code}" . ($indexnow_code < 400 ? " OK" : " - {$resp}") . "\n";

    if ($indexnow_code < 400) {
        foreach ($indexnow_urls as $url) {
            if (!isset($state['submitted'][$url])) {
                $state['submitted'][$url] = date('Y-m-d H:i:s');
            }
        }
    }
}

// ---- Save state ----
if (!$dry_run) {
    @mkdir(dirname($STATE_FILE), 0755, true);
    file_put_contents($STATE_FILE, json_encode($state, JSON_PRETTY_PRINT));
}

$total_submitted = count($state['submitted']);
echo "\n";
echo "Google:    {$google_success} OK, {$google_errors} errors" . ($quota_hit ? " (quota hit)" : "") . "\n";
if ($indexnow_code !== null) {
    echo "IndexNow:  " . count($indexnow_urls) . " URLs, HTTP {$indexnow_code}\n";
}
echo "State:     {$total_submitted}/{$total_urls} tracked\n";
echo "\n";
