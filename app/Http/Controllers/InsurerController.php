<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use App\Models\Province;
use App\Models\SpecialGroup;
use App\Models\Specialty;
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

        $products = $insurer->products;

        // Group provinces by autonomous community for the region grid
        $provincesByRegion = $insurer->provinces->groupBy('autonomous_community');

        // Convert FAQs to array format for the accordion component
        $faqs = $insurer->faqs->map(fn ($f) => [
            'question' => $f->question,
            'answer' => $f->answer,
        ])->toArray();

        // Other insurers for sidebar
        $otherInsurers = Insurer::where('is_active', true)
            ->where('id', '!=', $insurer->id)
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        $allProvinces = Province::orderBy('name')->get();

        $metaTitle = $insurer->meta_title
            ?? "Cuadro Médico {$insurer->name} - Todas las provincias";
        $metaDescription = $insurer->meta_description
            ?? "Consulta el cuadro médico de {$insurer->name}. Encuentra especialistas, centros y hospitales en todas las provincias de España.";

        return view('insurer.show', [
            'insurer' => $insurer,
            'products' => $products,
            'provinces' => $insurer->provinces,
            'provincesByRegion' => $provincesByRegion,
            'faqs' => $faqs,
            'otherInsurers' => $otherInsurers,
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

        $pivotData = $insurerProvince?->pivot;

        // Load FAQs for the insurer
        $insurer->load([
            'faqs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        // Convert FAQs to array format
        $faqs = $insurer->faqs->map(fn ($f) => [
            'question' => $f->question,
            'answer' => $f->answer,
        ])->toArray();

        // PDF URL from pivot data
        $pdfUrl = $pivotData?->pdf_url;

        // Specialties: from pivot JSON if available, otherwise all specialties as fallback
        $specialtiesAvailable = $pivotData?->specialties_available ?? [];
        if (! empty($specialtiesAvailable)) {
            $specialties = Specialty::whereIn('name', $specialtiesAvailable)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get();
        } else {
            $specialties = Specialty::orderBy('category')
                ->orderBy('sort_order')
                ->get();
        }

        // Localities from pivot JSON
        $localitiesRaw = $pivotData?->localities_covered ?? [];
        $localities = collect($localitiesRaw)->map(fn ($name) => (object) ['name' => $name]);

        // Compatible products for this insurer
        $compatibleProducts = $insurer->products()
            ->where('is_active', true)
            ->get();

        // Other provinces for sidebar
        $otherProvinces = Province::orderBy('name')->get();

        $metaTitle = $pivotData?->meta_title
            ?? "Cuadro Médico {$insurer->name} en {$province->name}";
        $metaDescription = $pivotData?->meta_description
            ?? "Cuadro médico de {$insurer->name} en {$province->name}. Consulta especialistas, centros médicos, hospitales y localidades con cobertura.";

        return view('insurer.province', [
            'insurer' => $insurer,
            'province' => $province,
            'pivotData' => $pivotData,
            'pdfUrl' => $pdfUrl,
            'specialties' => $specialties,
            'localities' => $localities,
            'compatibleProducts' => $compatibleProducts,
            'faqs' => $faqs,
            'otherProvinces' => $otherProvinces,
            'insurerProvinces' => $otherProvinces,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonicalUrl' => route('insurer.province', [$insurer->slug, $province->slug]),
        ]);
    }

    /**
     * Display insurer + special group page (e.g., Adeslas MUFACE).
     */
    public function specialGroup(string $insurerSlug, string $groupSlug): View
    {
        $insurer = Insurer::where('slug', $insurerSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $group = SpecialGroup::where('slug', $groupSlug)->firstOrFail();

        // Verify this insurer is concertada with this group
        $isConcertada = $insurer->specialGroups()->where('special_groups.id', $group->id)->exists();
        if (! $isConcertada) {
            abort(404);
        }

        // Load insurer data
        $insurer->load([
            'products' => fn ($q) => $q->where('is_active', true),
            'faqs' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        // Get provinces for sidebar
        $provinces = $insurer->provinces()->orderBy('name')->get();

        // Other concertada insurers for this group
        $otherInsurers = $group->insurers()
            ->where('is_active', true)
            ->where('insurers.id', '!=', $insurer->id)
            ->orderBy('sort_order')
            ->get();

        $faqs = $insurer->faqs->map(fn ($f) => [
            'question' => $f->question,
            'answer' => $f->answer,
        ])->toArray();

        $groupNames = [
            'muface' => 'MUFACE',
            'mugeju' => 'MUGEJU',
            'isfas' => 'ISFAS',
        ];
        $groupFullNames = [
            'muface' => 'Mutualidad General de Funcionarios Civiles del Estado',
            'mugeju' => 'Mutualidad General Judicial',
            'isfas' => 'Instituto Social de las Fuerzas Armadas',
        ];

        $year = date('Y');
        $groupName = $groupNames[$groupSlug] ?? $group->name;

        return view('insurer.special-group', [
            'insurer' => $insurer,
            'group' => $group,
            'groupName' => $groupName,
            'groupFullName' => $groupFullNames[$groupSlug] ?? $group->name,
            'provinces' => $provinces,
            'otherInsurers' => $otherInsurers,
            'faqs' => $faqs,
            'metaTitle' => "Cuadro Médico {$insurer->name} {$groupName} {$year} — Funcionarios",
            'metaDescription' => "Cuadro médico de {$insurer->name} para {$groupName} ({$groupFullNames[$groupSlug]}) en {$year}. Consulta médicos, especialidades y coberturas para funcionarios.",
            'canonicalUrl' => url("/{$insurer->slug}/{$groupSlug}"),
        ]);
    }
}
