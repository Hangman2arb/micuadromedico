<?php

namespace App\Providers;

use App\Models\Insurer;
use App\Models\SpecialGroup;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceRootUrl('https://micuadromedico.es');
        URL::forceScheme('https');

        // Share navigation data with all views
        View::composer('*', function ($view) {
            // Avoid recursive calls and only share if not already set
            if (! isset($view->navInsurers)) {
                static $navInsurers = null;
                static $navSpecialGroups = null;

                if ($navInsurers === null) {
                    $navInsurers = Insurer::where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->get(['id', 'name', 'slug', 'logo_url']);
                }

                if ($navSpecialGroups === null) {
                    $navSpecialGroups = SpecialGroup::orderBy('name')
                        ->get(['id', 'name', 'slug']);
                }

                $view->with('navInsurers', $navInsurers);
                $view->with('navSpecialGroups', $navSpecialGroups);
            }
        });
    }
}
