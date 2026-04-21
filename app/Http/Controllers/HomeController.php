<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use App\Models\Province;
use App\Models\Specialty;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with all active insurers + province/specialty hubs for SEO discovery.
     */
    public function index(): View
    {
        $insurers = Insurer::where('is_active', true)
            ->withCount('provinces')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Provinces grouped by autonomous community — powers the /provincias hub block
        $provincesByRegion = Province::orderBy('autonomous_community')
            ->orderBy('name')
            ->get()
            ->groupBy('autonomous_community');

        // Top specialties for quick discovery (limit to keep the homepage scannable)
        $topSpecialties = Specialty::orderBy('sort_order')
            ->orderBy('name')
            ->limit(24)
            ->get();

        return view('home', [
            'insurers' => $insurers,
            'provincesByRegion' => $provincesByRegion,
            'topSpecialties' => $topSpecialties,
            'metaTitle' => 'Mi Cuadro Médico - Consulta cuadros médicos de aseguradoras en España',
            'metaDescription' => 'Encuentra y consulta los cuadros médicos de las principales aseguradoras de salud en España. Busca por aseguradora y provincia.',
            'canonicalUrl' => route('home'),
        ]);
    }
}
