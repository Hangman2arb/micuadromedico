<?php
/**
 * Scrape ALL PDFs from cuadromedico.de using ScraperAPI for JS rendering.
 * 
 * Strategy:
 * 1. For each insurer, fetch ONE province page via ScraperAPI to discover the PDF naming pattern
 * 2. Then use that pattern to download all provinces with simple curl (no ScraperAPI needed)
 * 
 * This minimizes ScraperAPI credits while getting all PDFs.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$SCRAPER_KEY = 'adf5b2df7520d10470b168002716fc2e';
$PDF_DIR = public_path('pdfs');
@mkdir($PDF_DIR, 0755, true);

// Province mapping: our slug → cuadromedico.de slug
$provMap = [
    'a-coruna'=>'acoruna','alava'=>'alava','albacete'=>'albacete','alicante'=>'alicante',
    'almeria'=>'almeria','asturias'=>'asturias','avila'=>'avila','badajoz'=>'badajoz',
    'baleares'=>'baleares','barcelona'=>'barcelona','burgos'=>'burgos','caceres'=>'caceres',
    'cadiz'=>'cadiz','cantabria'=>'cantabria','castellon'=>'castellon','ceuta'=>'ceuta',
    'ciudad-real'=>'ciudadreal','cordoba'=>'cordoba','cuenca'=>'cuenca','girona'=>'girona',
    'granada'=>'granada','guadalajara'=>'guadalajara','guipuzcoa'=>'guipuzcoa','huelva'=>'huelva',
    'huesca'=>'huesca','jaen'=>'jaen','la-rioja'=>'larioja','las-palmas'=>'laspalmas',
    'leon'=>'leon','lleida'=>'lleida','lugo'=>'lugo','madrid'=>'madrid','malaga'=>'malaga',
    'melilla'=>'melilla','murcia'=>'murcia','navarra'=>'navarra','ourense'=>'ourense',
    'palencia'=>'palencia','pontevedra'=>'pontevedra','salamanca'=>'salamanca',
    'santa-cruz-de-tenerife'=>'santacruzdetenerife','segovia'=>'segovia','sevilla'=>'sevilla',
    'soria'=>'soria','tarragona'=>'tarragona','teruel'=>'teruel','toledo'=>'toledo',
    'valencia'=>'valencia','valladolid'=>'valladolid','vizcaya'=>'vizcaya',
    'zamora'=>'zamora','zaragoza'=>'zaragoza',
];

// Province display names for PDF URL construction
$provDisplayNames = [
    'acoruna'=>'A Coruña','alava'=>'Álava','albacete'=>'Albacete','alicante'=>'Alicante',
    'almeria'=>'Almería','asturias'=>'Asturias','avila'=>'Ávila','badajoz'=>'Badajoz',
    'baleares'=>'Baleares','barcelona'=>'Barcelona','burgos'=>'Burgos','caceres'=>'Cáceres',
    'cadiz'=>'Cádiz','cantabria'=>'Cantabria','castellon'=>'Castellón','ceuta'=>'Ceuta',
    'ciudadreal'=>'Ciudad Real','cordoba'=>'Córdoba','cuenca'=>'Cuenca','girona'=>'Girona',
    'granada'=>'Granada','guadalajara'=>'Guadalajara','guipuzcoa'=>'Guipúzcoa','huelva'=>'Huelva',
    'huesca'=>'Huesca','jaen'=>'Jaén','larioja'=>'La Rioja','laspalmas'=>'Las Palmas',
    'leon'=>'León','lleida'=>'Lleida','lugo'=>'Lugo','madrid'=>'Madrid','malaga'=>'Málaga',
    'melilla'=>'Melilla','murcia'=>'Murcia','navarra'=>'Navarra','ourense'=>'Ourense',
    'palencia'=>'Palencia','pontevedra'=>'Pontevedra','salamanca'=>'Salamanca',
    'santacruzdetenerife'=>'Santa Cruz de Tenerife','segovia'=>'Segovia','sevilla'=>'Sevilla',
    'soria'=>'Soria','tarragona'=>'Tarragona','teruel'=>'Teruel','toledo'=>'Toledo',
    'valencia'=>'Valencia','valladolid'=>'Valladolid','vizcaya'=>'Vizcaya',
    'zamora'=>'Zamora','zaragoza'=>'Zaragoza',
];

$insurers = DB::table('insurers')->where('is_active', true)->orderBy('sort_order')->pluck('slug')->toArray();

// For each insurer, use ScraperAPI to discover the PDF name pattern from ONE page
$patterns = [];
$totalNew = 0;
$totalFail = 0;
$totalSkip = 0;

foreach ($insurers as $iSlug) {
    // Check if we already have most PDFs for this insurer
    $insurerId = DB::table('insurers')->where('slug', $iSlug)->value('id');
    $existing = DB::table('insurer_province')
        ->where('insurer_id', $insurerId)
        ->whereNotNull('pdf_local_path')
        ->count();
    
    if ($existing >= 35) {
        echo "  {$iSlug}: already have {$existing}/52 PDFs, skipping discovery\n";
        $totalSkip += 52;
        continue;
    }
    
    // Use ScraperAPI to fetch one province page and discover PDF pattern
    echo "  {$iSlug}: discovering PDF pattern via ScraperAPI... ";
    
    $testProv = 'madrid';
    $pageUrl = "https://cuadromedico.de/{$iSlug}-{$testProv}";
    $scraperUrl = "https://api.scraperapi.com?api_key={$SCRAPER_KEY}&url=" . urlencode($pageUrl) . "&render=true&ultra_premium=true";
    
    $ch = curl_init($scraperUrl);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 90]);
    $html = curl_exec($ch);
    curl_close($ch);
    
    // Extract PDF path
    preg_match('/file=\/([^"]+\.pdf)/i', $html, $m);
    $pdfPattern = $m[1] ?? null;
    
    if (!$pdfPattern) {
        // Try Barcelona
        $pageUrl = "https://cuadromedico.de/{$iSlug}-barcelona";
        $scraperUrl = "https://api.scraperapi.com?api_key={$SCRAPER_KEY}&url=" . urlencode($pageUrl) . "&render=true&ultra_premium=true";
        $ch = curl_init($scraperUrl);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 90]);
        $html = curl_exec($ch);
        curl_close($ch);
        preg_match('/file=\/([^"]+\.pdf)/i', $html, $m);
        $pdfPattern = $m[1] ?? null;
    }
    
    if (!$pdfPattern) {
        echo "NO PDF FOUND on cuadromedico.de\n";
        $totalFail += 52;
        continue;
    }
    
    // Extract the template: "Cuadro Médico Cigna Privado Madrid.pdf" → "Cuadro Médico Cigna Privado {PROVINCE}.pdf"
    // Remove the province name from the pattern to get the template
    $template = $pdfPattern;
    foreach ($provDisplayNames as $slug => $display) {
        // Try replacing the province name (with and without accents)
        $clean = strtr($display, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','ñ'=>'n','ü'=>'u']);
        if (strpos($template, $display) !== false) {
            $template = str_replace($display, '{PROVINCE}', $template);
            break;
        }
        if (strpos($template, $clean) !== false) {
            $template = str_replace($clean, '{PROVINCE}', $template);
            break;
        }
    }
    
    echo "pattern: {$template}\n";
    $patterns[$iSlug] = $template;
    
    // Now download ALL provinces using this pattern
    foreach ($provMap as $ourSlug => $cmSlug) {
        $localFile = Str::slug("cuadro-medico-{$iSlug}-{$ourSlug}") . '.pdf';
        $fullPath = "{$PDF_DIR}/{$localFile}";
        
        if (file_exists($fullPath) && filesize($fullPath) > 10000) {
            $totalSkip++;
            continue;
        }
        
        $provDisplay = $provDisplayNames[$cmSlug] ?? ucfirst($cmSlug);
        $pdfFile = str_replace('{PROVINCE}', $provDisplay, $template);
        $pdfUrl = "https://cuadromedico.de/" . rawurlencode($pdfFile);
        
        // Also try without accents
        $provClean = strtr($provDisplay, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','ñ'=>'n','ü'=>'u']);
        $pdfFileClean = str_replace('{PROVINCE}', $provClean, $template);
        $pdfUrlClean = "https://cuadromedico.de/" . rawurlencode($pdfFileClean);
        
        $success = false;
        foreach ([$pdfUrl, $pdfUrlClean] as $tryUrl) {
            $ch = curl_init($tryUrl);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 60]);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($code === 200 && strlen($data) > 10000) {
                file_put_contents($fullPath, $data);
                $provinceId = DB::table('provinces')->where('slug', $ourSlug)->value('id');
                if ($provinceId) {
                    DB::table('insurer_province')
                        ->where('insurer_id', $insurerId)
                        ->where('province_id', $provinceId)
                        ->update(['pdf_local_path' => "/pdfs/{$localFile}", 'pdf_url' => "/pdfs/{$localFile}"]);
                }
                $totalNew++;
                $success = true;
                break;
            }
        }
        
        if (!$success) $totalFail++;
        usleep(150000);
    }
    
    echo "    → {$totalNew} new so far\n";
}

echo "\n=== SUMMARY ===\n";
echo "New downloads: {$totalNew}\n";
echo "Already existed: {$totalSkip}\n";
echo "Not found: {$totalFail}\n";
echo "Total PDFs: " . count(glob("{$PDF_DIR}/*.pdf")) . "\n";
