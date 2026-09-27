<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.available_locales', ['en', 'fa']);

        $locale = $request->segment(1);

        if (in_array($locale, $availableLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        } elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            $locale = $request->getPreferredLanguage($availableLocales) ?? config('app.locale');
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}