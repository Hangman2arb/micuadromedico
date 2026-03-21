<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with all active insurers.
     */
    public function index(): View
    {
        $insurers = Insurer::where('is_active', true)
            ->withCount('provinces')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('home', [
            'insurers' => $insurers,
            'metaTitle' => 'Mi Cuadro Médico - Consulta cuadros médicos de aseguradoras en España',
            'metaDescription' => 'Encuentra y consulta los cuadros médicos de las principales aseguradoras de salud en España. Busca por aseguradora y provincia.',
            'canonicalUrl' => route('home'),
        ]);
    }
}
