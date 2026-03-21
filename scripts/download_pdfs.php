<?php
/**
 * Download ALL cuadro medico PDFs from cuadromedico.de and update DB with local paths.
 * 
 * Usage:
 *   php scripts/download_pdfs.php              # Download all
 *   php scripts/download_pdfs.php --dry-run    # Preview what would be downloaded
 *   php scripts/download_pdfs.php --status     # Show download progress
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$dry_run = in_array('--dry-run', $argv);
$status_only = in_array('--status', $argv);
$PDF_DIR = public_path('pdfs');
@mkdir($PDF_DIR, 0755, true);

// Insurer name variations for the PDF URL (cuadromedico.de uses display names)
$insurerDisplayNames = [
    'adeslas' => 'Adeslas',
    'sanitas' => 'Sanitas',
    'asisa' => 'Asisa',
    'dkv' => 'DKV',
    'mapfre' => 'Mapfre',
    'aegon' => 'Aegon',
    'asefa' => 'Asefa',
    'axa' => 'AXA',
    'antares' => 'Antares',
    'caser' => 'Caser',
    'generali' => 'Generali',
    'cigna' => 'Cigna',
    'agrupacio-mutua' => 'Agrupacio Mutua',
    'catalana-occidente' => 'Catalana Occidente',
    'cosalud' => 'Cosalud',
    'fiatc' => 'FIATC',
    'hna' => 'HNA',
    'igualatorio-cantabria' => 'Igualatorio Cantabria',
    'imq' => 'IMQ',
    'musa' => 'MUSA',
    'nectar' => 'Nectar',
    'nortehispana' => 'NorteHispana',
    'plus-ultra' => 'Plus Ultra',
    'previsora-general' => 'Previsora General',
    'seguros-bilbao' => 'Seguros Bilbao',
    'union-madrilena' => 'Union Madrilena',
    'vivaz' => 'Vivaz',
    'zurich' => 'Zurich',
    'allianz' => 'Allianz',
    'acunsa' => 'Acunsa',
    'dkv-la-fuencisla' => 'DKV La Fuencisla',
    'aegon-la-sanitaria' => 'Aegon La Sanitaria',
    'aegon-labor-medica' => 'Aegon Labor Medica',
    'imq-asturias' => 'IMQ Asturias',
    'divina-pastora' => 'Divina Pastora',
    'psn' => 'PSN',
];

// Province display names (without accents for URL, cuadromedico.de uses unaccented)
$provinceDisplayNames = [
    'almeria' => 'Almeria', 'cadiz' => 'Cadiz', 'cordoba' => 'Cordoba',
    'granada' => 'Granada', 'huelva' => 'Huelva', 'jaen' => 'Jaen',
    'malaga' => 'Malaga', 'sevilla' => 'Sevilla',
    'huesca' => 'Huesca', 'teruel' => 'Teruel', 'zaragoza' => 'Zaragoza',
    'asturias' => 'Asturias', 'baleares' => 'Baleares',
    'las-palmas' => 'Las Palmas', 'santa-cruz-de-tenerife' => 'Santa Cruz de Tenerife',
    'cantabria' => 'Cantabria',
    'albacete' => 'Albacete', 'ciudad-real' => 'Ciudad Real', 'cuenca' => 'Cuenca',
    'guadalajara' => 'Guadalajara', 'toledo' => 'Toledo',
    'avila' => 'Avila', 'burgos' => 'Burgos', 'leon' => 'Leon',
    'palencia' => 'Palencia', 'salamanca' => 'Salamanca', 'segovia' => 'Segovia',
    'soria' => 'Soria', 'valladolid' => 'Valladolid', 'zamora' => 'Zamora',
    'barcelona' => 'Barcelona', 'girona' => 'Girona', 'lleida' => 'Lleida',
    'tarragona' => 'Tarragona',
    'alicante' => 'Alicante', 'castellon' => 'Castellon', 'valencia' => 'Valencia',
    'badajoz' => 'Badajoz', 'caceres' => 'Caceres',
    'a-coruna' => 'A Coruna', 'lugo' => 'Lugo', 'ourense' => 'Ourense',
    'pontevedra' => 'Pontevedra',
    'la-rioja' => 'La Rioja', 'madrid' => 'Madrid', 'murcia' => 'Murcia',
    'navarra' => 'Navarra',
    'alava' => 'Alava', 'guipuzcoa' => 'Guipuzcoa', 'vizcaya' => 'Vizcaya',
    'ceuta' => 'Ceuta', 'melilla' => 'Melilla',
];

// Get all combos from DB
$combos = DB::table('insurer_province')
    ->join('insurers', 'insurers.id', '=', 'insurer_province.insurer_id')
    ->join('provinces', 'provinces.id', '=', 'insurer_province.province_id')
    ->where('insurers.is_active', true)
    ->select('insurer_province.id', 'insurers.slug as i_slug', 'provinces.slug as p_slug')
    ->get();

if ($status_only) {
    $total = count($combos);
    $downloaded = DB::table('insurer_province')->whereNotNull('pdf_local_path')->count();
    $pdfs = glob("$PDF_DIR/*.pdf");
    echo "Total combos: $total\n";
    echo "DB records with local PDF: $downloaded\n";
    echo "PDF files on disk: " . count($pdfs) . "\n";
    $totalSize = array_sum(array_map('filesize', $pdfs));
    echo "Total size: " . round($totalSize / 1048576) . " MB\n";
    exit(0);
}

echo "Starting PDF download... (" . count($combos) . " combos)\n\n";

$downloaded = 0;
$skipped = 0;
$failed = 0;
$totalBytes = 0;

foreach ($combos as $i => $combo) {
    $iName = $insurerDisplayNames[$combo->i_slug] ?? ucfirst($combo->i_slug);
    $pName = $provinceDisplayNames[$combo->p_slug] ?? ucfirst($combo->p_slug);
    
    $localFilename = Str::slug("cuadro-medico-{$combo->i_slug}-{$combo->p_slug}") . '.pdf';
    $localPath = "/pdfs/{$localFilename}";
    $fullPath = "$PDF_DIR/{$localFilename}";
    
    // Skip if already downloaded
    if (file_exists($fullPath) && filesize($fullPath) > 10000) {
        $skipped++;
        continue;
    }
    
    // Try both URL patterns (accented and unaccented)
    $urls = [
        "https://cuadromedico.de/Cuadro%20m%C3%A9dico%20" . rawurlencode($iName) . "%20" . rawurlencode($pName) . ".pdf",
        "https://cuadromedico.de/Cuadro%20Medico%20" . rawurlencode($iName) . "%20" . rawurlencode($pName) . ".pdf",
    ];
    
    if ($dry_run) {
        echo "  [{$combo->i_slug}/{$combo->p_slug}] → $localFilename\n";
        $downloaded++;
        continue;
    }
    
    $success = false;
    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; MiCuadroMedico/1.0)',
        ]);
        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($code === 200 && strlen($data) > 10000) {
            file_put_contents($fullPath, $data);
            $totalBytes += strlen($data);
            $downloaded++;
            $success = true;
            
            // Update DB with local path
            DB::table('insurer_province')->where('id', $combo->id)->update([
                'pdf_local_path' => $localPath,
                'pdf_url' => $localPath,
            ]);
            
            if ($downloaded % 50 === 0) {
                echo "  Progress: {$downloaded} downloaded, {$failed} failed, {$skipped} skipped (" . round($totalBytes / 1048576) . " MB)\n";
            }
            break;
        }
    }
    
    if (!$success) {
        $failed++;
    }
    
    // Be polite — don't hammer the server
    usleep(200000); // 200ms between requests
}

echo "\n=== Summary ===\n";
echo "Downloaded: $downloaded\n";
echo "Skipped (already existed): $skipped\n";
echo "Failed (404/403): $failed\n";
echo "Total size: " . round($totalBytes / 1048576) . " MB\n";
