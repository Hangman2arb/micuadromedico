<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $canonicalHost = 'micuadromedico.es';

        if ($request->getHost() !== $canonicalHost) {
            $url = 'https://' . $canonicalHost . $request->getRequestUri();

            return redirect()->away($url, 301);
        }

        return $next($request);
    }
}
