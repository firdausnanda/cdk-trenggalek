<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\StatisticsConsent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieConsentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'choice' => 'required|in:accept,reject',
            'return_to' => 'nullable|string|max:2048',
        ]);

        Cookie::queue(StatisticsConsent::COOKIE_NAME, $validated['choice'], StatisticsConsent::LIFETIME_MINUTES);

        if ($validated['choice'] === StatisticsConsent::REJECT) {
            Cookie::expire(config('eloquent-viewable.visitor_cookie_key'));
        }

        $returnTo = $validated['return_to'] ?? '/';
        if (! str_starts_with($returnTo, '/') || str_starts_with($returnTo, '//') || str_contains($returnTo, '\\') || preg_match('/[\x00-\x1F\x7F]/', $returnTo)) {
            $returnTo = '/';
        }

        return redirect()->to($returnTo, 303);
    }
}
