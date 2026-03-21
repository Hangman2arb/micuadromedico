<?php

namespace App\Http\Controllers;

use App\Models\Insurer;
use App\Models\InsurerProvince;
use App\Models\SpecialGroup;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate the XML sitemap with all public pages.
     */
    public function index(): Response
    {
        $insurers = Insurer::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $insurerProvinces = InsurerProvince::with(['insurer', 'province'])
            ->whereHas('insurer', fn ($q) => $q->where('is_active', true))
            ->get();

        $specialGroups = SpecialGroup::all();

        $urls = [];

        // Home page
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toDateString(),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ];

        // Insurer pages
        foreach ($insurers as $insurer) {
            $urls[] = [
                'loc' => route('insurer.show', $insurer),
                'lastmod' => $insurer->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Insurer + Province pages
        foreach ($insurerProvinces as $ip) {
            $urls[] = [
                'loc' => route('insurer.province', [$ip->insurer->slug, $ip->province->slug]),
                'lastmod' => ($ip->last_updated_at ?? $ip->updated_at)->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Special group pages
        foreach ($specialGroups as $group) {
            $urls[] = [
                'loc' => url("/{$group->slug}"),
                'lastmod' => $group->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // Static pages
        $staticPages = [
            ['loc' => route('legal'), 'priority' => '0.2'],
            ['loc' => route('contact'), 'priority' => '0.3'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $page['loc'],
                'lastmod' => now()->toDateString(),
                'changefreq' => 'yearly',
                'priority' => $page['priority'],
            ];
        }

        $content = view('sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
