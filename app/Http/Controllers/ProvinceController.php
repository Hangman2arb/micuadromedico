<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Insurer;
use App\Models\Specialty;
use Illuminate\View\View;

class ProvinceController extends Controller
{
    public function index(): View
    {
        $provinces = Province::orderBy('autonomous_community')
            ->orderBy('name')
            ->get()
            ->groupBy('autonomous_community');

        return view('province.index', [
            'provincesByRegion' => $provinces,
            'metaTitle' => 'Cuadros Médicos por Provincia — España ' . date('Y'),
            'metaDescription' => 'Consulta los cuadros médicos de todas las aseguradoras disponibles en cada provincia de España. Encuentra médicos y centros por tu zona.',
            'canonicalUrl' => url('/provincias'),
        ]);
    }

    public function show(string $slug): View
    {
        $province = Province::where('slug', $slug)->firstOrFail();

        // Get all active insurers that have pivot data for this province
        $insurersWithPivot = $province->insurers()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Get all specialties available in this province (union of all insurers)
        $allSpecialties = collect();
        foreach ($insurersWithPivot as $insurer) {
            $specs = $insurer->pivot->specialties_available ?? [];
            if (is_string($specs)) $specs = json_decode($specs, true) ?? [];
            $allSpecialties = $allSpecialties->merge($specs);
        }
        $specialtyNames = $allSpecialties->unique()->sort()->values();

        // Resolve to Specialty objects so the view can link to /especialidades/{slug}
        $specialties = Specialty::whereIn('name', $specialtyNames->all())
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        // Nearby provinces (same autonomous community) for sibling linking
        $nearbyProvinces = Province::where('autonomous_community', $province->autonomous_community)
            ->where('id', '!=', $province->id)
            ->orderBy('name')
            ->get();

        $year = date('Y');

        return view('province.show', [
            'province' => $province,
            'insurers' => $insurersWithPivot,
            'specialties' => $specialties,
            'nearbyProvinces' => $nearbyProvinces,
            'metaTitle' => "Cuadros Médicos en {$province->name} {$year} — Todas las Aseguradoras",
            'metaDescription' => "Consulta los cuadros médicos de todas las aseguradoras en {$province->name}. Compara {$insurersWithPivot->count()} aseguradoras, especialidades y centros médicos en {$province->name}.",
            'canonicalUrl' => url("/provincias/{$province->slug}"),
        ]);
    }
}
