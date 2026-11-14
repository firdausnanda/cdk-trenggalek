<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires a visitor signed in with Google (commenter guard). Guests get a JSON 401 from the chat
 * script, or are sent to Google sign-in when the form was posted without JavaScript.
 */
class EnsureCommenter
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('commenter')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => __('public.comments.login_required')], 401);
        }

        $back = parse_url((string) url()->previous(), PHP_URL_PATH) ?: '/berita';

        return redirect()->route('komentar.google', ['kembali' => $back]);
    }
}
