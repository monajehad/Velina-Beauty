<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sets the dashboard language (English or Arabic) from its own session key,
 * so it doesn't follow the storefront language.
 */
class AdminLocaleMiddleware
{
    public const LOCALES = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('admin_locale');

        App::setLocale(in_array($locale, self::LOCALES, true) ? $locale : 'en');

        return $next($request);
    }
}
