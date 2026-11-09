<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetlocaleMiddleware
{
    /**
     * Apply the language stored in the session (see LocaleController), falling back to app.locale.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (! array_key_exists($locale, get_languages())) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        // Dates on the public pages use translatedFormat(), so month names follow the language too.
        Carbon::setLocale($locale);

        return $next($request);
    }
}
