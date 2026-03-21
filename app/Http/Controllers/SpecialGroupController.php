<?php

namespace App\Http\Controllers;

use App\Models\SpecialGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialGroupController extends Controller
{
    /**
     * Display a special group page (MUFACE, MUGEJU, ISFAS).
     */
    public function show(Request $request): View
    {
        $slug = $request->route()->defaults['slug'] ?? null;

        $specialGroup = SpecialGroup::where('slug', $slug)->firstOrFail();

        $specialGroup->load([
            'insurers' => fn ($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name'),
            'faqs' => fn ($query) => $query->orderBy('sort_order'),
        ]);

        $metaTitle = $specialGroup->meta_title
            ?? "Cuadro Médico {$specialGroup->name} - Aseguradoras y coberturas";
        $metaDescription = $specialGroup->meta_description
            ?? "Consulta las aseguradoras que ofrecen cobertura {$specialGroup->name}. Compara opciones, descarga cuadros médicos y encuentra la mejor opción.";

        return view('special-group.show', [
            'specialGroup' => $specialGroup,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'canonicalUrl' => url("/{$slug}"),
        ]);
    }
}
