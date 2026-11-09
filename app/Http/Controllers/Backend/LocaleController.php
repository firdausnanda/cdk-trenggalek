<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * Switches the interface language for the visitor's session. Used by the admin header and by the
 * language toggle on the public site; only languages that have a folder in resources/lang are accepted.
 */
class LocaleController extends Controller
{
    public function switch(string $lang): RedirectResponse
    {
        abort_unless(array_key_exists($lang, get_languages()), 404);

        session()->put('locale', $lang);

        $previous = url()->previous();

        // The public pages show their own form feedback through the same flash key, so only the
        // admin panel gets a confirmation message.
        if (Str::startsWith(parse_url($previous, PHP_URL_PATH) ?? '', '/admin')) {
            session()->flash('success', __('Language changed successfully!'));
        }

        return redirect()->to($previous);
    }
}
