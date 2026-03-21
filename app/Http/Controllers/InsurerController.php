<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use App\Models\Province;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InsurerController extends Controller
{
    /**
     * Display an insurer page with its products, provinces, and FAQs.
     */
    public function show(Insurer $insurer): View
    {
        if (! $insurer->is_active) {
            throw new NotFoundHttpException();
        }

        $insurer->load([
            'products' => fn ($query) => $query->where('is_active', true),
            'provinces' => fn ($query) => $query->orderBy('name'),
            'faqs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $allProvinces = Province::orderBy('name')->get();

        $metaTitle = $insurer->meta_title
            ?? "Cuadro Médico {$insurer->name} - Todas las provincias";
        $metaDescription = $insurer->meta_description
            ?? "Consulta el cuadro médico de {$insurer->name}. Encuentra especialistas, centros y hospitales en todas las provincias de España.";

        return view('insurer.show', [
            'insurer' => $insurer,
            'allProvinces' => $allProvinces,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonicalUrl' => route('insurer.show', $insurer),
        ]);
    }

    /**
     * Display the insurer+province detail page with specialties,
     * localities, and PDF info.
     */
    public function province(string $insurerSlug, string $provinceSlug): View
    {
        $insurer = Insurer::where('slug', $insurerSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $province = Province::where('slug', $provinceSlug)->firstOrFail();

        // Get the pivot data for this insurer+province combination
        $insurerProvince = $insurer->provinces()
            ->where('provinces.id', $province->id)
            ->first();

        // Load FAQs for the insurer
        $insurer->load([
            'faqs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        // Get all provinces (show all, even without pivot data)
        $allProvinces = Province::orderBy('name')->get();

        $pivotData = $insurerProvince?->pivot;

        $metaTitle = $pivotData->meta_title
            ?? "Cuadro Médico {$insurer->name} en {$province->name}";
        $metaDescription = $pivotData->meta_description
            ?? "Cuadro médico de {$insurer->name} en {$province->name}. Consulta especialistas, centros médicos, hospitales y localidades con cobertura.";

        return view('insurer.province', [
            'insurer' => $insurer,
            'province' => $province,
            'pivotData' => $pivotData,
            'insurerProvinces' => $allProvinces,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonicalUrl' => route('insurer.province', [$insurer->slug, $province->slug]),
        ]);
    }
}
