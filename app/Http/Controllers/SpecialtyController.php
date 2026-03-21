<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use App\Models\Insurer;
use App\Models\InsurerProvince;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function index(): View
    {
        $specialties = Specialty::orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('specialty.index', [
            'specialtiesByCategory' => $specialties,
            'metaTitle' => 'Especialidades Médicas — Cuadros Médicos España ' . date('Y'),
            'metaDescription' => 'Consulta todas las especialidades médicas cubiertas por las aseguradoras de salud en España. Encuentra la aseguradora con la especialidad que necesitas.',
            'canonicalUrl' => url('/especialidades'),
        ]);
    }

    public function show(string $slug): View
    {
        $specialty = Specialty::where('slug', $slug)->firstOrFail();

        // Find insurers that offer this specialty (check JSON array in pivot)
        $insurerIds = InsurerProvince::whereJsonContains('specialties_available', $specialty->name)
            ->distinct()
            ->pluck('insurer_id');

        $insurers = Insurer::whereIn('id', $insurerIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Count provinces per insurer for this specialty
        foreach ($insurers as $insurer) {
            $insurer->province_count_for_specialty = InsurerProvince::where('insurer_id', $insurer->id)
                ->whereJsonContains('specialties_available', $specialty->name)
                ->count();
        }

        $year = date('Y');
        $categoryLabels = ['medica' => 'Médica', 'dental' => 'Dental', 'pruebas' => 'Pruebas Diagnósticas'];

        return view('specialty.show', [
            'specialty' => $specialty,
            'insurers' => $insurers,
            'categoryLabel' => $categoryLabels[$specialty->category] ?? $specialty->category,
            'metaTitle' => "{$specialty->name} — Cuadros Médicos con esta Especialidad {$year}",
            'metaDescription' => "Aseguradoras que incluyen {$specialty->name} en su cuadro médico. Compara {$insurers->count()} aseguradoras con cobertura de {$specialty->name} en toda España.",
            'canonicalUrl' => url("/especialidades/{$specialty->slug}"),
        ]);
    }
}
