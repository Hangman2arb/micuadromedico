<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsurerController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SpecialGroupController;
use App\Http\Controllers\SpecialtyController;
use Illuminate\Support\Facades\Route;

// Home - list all insurers
Route::get('/', [HomeController::class, 'index'])->name('home');

// Special groups (MUFACE, MUGEJU, ISFAS)
Route::get('/muface', [SpecialGroupController::class, 'show'])->name('special-group.show')->defaults('slug', 'muface');
Route::get('/mugeju', [SpecialGroupController::class, 'show'])->name('special-group.mugeju')->defaults('slug', 'mugeju');
Route::get('/isfas', [SpecialGroupController::class, 'show'])->name('special-group.isfas')->defaults('slug', 'isfas');

// Static pages
Route::get('/aviso-legal', fn () => view('pages.legal', [
    'metaTitle' => 'Aviso Legal - Mi Cuadro Médico',
    'metaDescription' => 'Aviso legal y condiciones de uso de micuadromedico.es.',
    'canonicalUrl' => route('legal'),
]))->name('legal');

Route::get('/contacto', fn () => view('pages.contact', [
    'metaTitle' => 'Contacto - Mi Cuadro Médico',
    'metaDescription' => 'Contacta con nosotros para cualquier consulta sobre cuadros médicos de aseguradoras en España.',
    'canonicalUrl' => route('contact'),
]))->name('contact');

// Province pages
Route::get('/provincias', [ProvinceController::class, 'index'])->name('province.index');
Route::get('/provincias/{slug}', [ProvinceController::class, 'show'])->name('province.show');

// Specialty pages
Route::get('/especialidades', [SpecialtyController::class, 'index'])->name('specialty.index');
Route::get('/especialidades/{slug}', [SpecialtyController::class, 'show'])->name('specialty.show');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Insurer pages (must be last - catch-all slug patterns)
Route::get('/{insurer}', [InsurerController::class, 'show'])->name('insurer.show');
Route::get('/{insurer}/{province}', [InsurerController::class, 'province'])->name('insurer.province');
