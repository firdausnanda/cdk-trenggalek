<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Commenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

/**
 * Google sign-in for visitors who want to comment. Signs them in on the `commenter` guard, which is
 * separate from the admin `web` guard.
 */
class CommenterAuthController extends Controller
{
    private const RETURN_KEY = 'commenter.return_to';

    private const AVATAR_DIR = 'uploads/commenters';

    public function redirect(Request $request): RedirectResponse|SymfonyRedirect
    {
        if (! Commenter::signInAvailable()) {
            return redirect()->to($this->safeReturn($request->query('kembali')));
        }

        $request->session()->put(self::RETURN_KEY, $this->safeReturn($request->query('kembali')));

        // Google's driver already asks for the openid, profile and email scopes.
        $driver = Socialite::driver('google');
        if ($driver instanceof AbstractProvider) {
            // Let visitors with several Google accounts pick one instead of silently using the last.
            $driver->with(['prompt' => 'select_account']);
        }

        return $driver->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $returnTo = $this->safeReturn($request->session()->pull(self::RETURN_KEY));

        if ($request->filled('error')) {
            // The visitor pressed cancel on Google's screen.
            return redirect()->to($returnTo);
        }

        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Google sign-in for comments failed', ['error' => $e->getMessage()]);

            return redirect()->to($returnTo)->with('komentar_error', __('public.comments.login_failed'));
        }

        if (! $google->getEmail()) {
            return redirect()->to($returnTo)->with('komentar_error', __('public.comments.login_failed'));
        }

        $commenter = Commenter::updateOrCreate(
            ['google_id' => (string) $google->getId()],
            [
                'name' => mb_substr(trim((string) ($google->getName() ?: $google->getNickname() ?: strtok($google->getEmail(), '@'))), 0, 255),
                'email' => $google->getEmail(),
                'last_login_at' => now(),
            ],
        );

        $this->storeAvatar($commenter, $google->getAvatar());

        Auth::guard('commenter')->login($commenter, remember: true);
        $request->session()->regenerate();

        return redirect()->to($returnTo);
    }

    public function logout(Request $request): RedirectResponse
    {
        // Only the commenter guard: an admin signed in on the same browser stays signed in.
        Auth::guard('commenter')->logout();
        $request->session()->regenerateToken();

        return redirect()->to($this->safeReturn($request->input('kembali')));
    }

    /**
     * Copy the Google profile photo to our own server. Public pages then never load images from
     * Google, so readers are not exposed to a third party (see the privacy policy).
     */
    private function storeAvatar(Commenter $commenter, ?string $url): void
    {
        if (! $url || ! str_starts_with($url, 'https://')) {
            return;
        }

        try {
            $response = Http::timeout(5)->get($url);
            $type = (string) $response->header('Content-Type');
            $body = $response->body();

            if (! $response->successful() || ! str_starts_with($type, 'image/') || strlen($body) > 1024 * 1024) {
                return;
            }

            $extension = match (true) {
                str_contains($type, 'png') => 'png',
                str_contains($type, 'webp') => 'webp',
                default => 'jpg',
            };
            $relative = self::AVATAR_DIR.'/'.$commenter->id.'.'.$extension;

            File::ensureDirectoryExists(public_path(self::AVATAR_DIR));
            foreach (File::glob(public_path(self::AVATAR_DIR.'/'.$commenter->id.'.*')) as $old) {
                File::delete($old);
            }
            File::put(public_path($relative), $body);

            // The version query busts browser caches when the photo changes.
            $commenter->update(['avatar' => '/'.$relative.'?v='.substr(md5($body), 0, 8)]);
        } catch (Throwable $e) {
            Log::info('Could not copy commenter avatar', ['commenter' => $commenter->id, 'error' => $e->getMessage()]);
        }
    }

    /** Only paths on this site, so the sign-in flow cannot be used as an open redirect. */
    private function safeReturn(mixed $path): string
    {
        $path = is_string($path) ? $path : '';

        if ($path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//') || str_contains($path, '\\')) {
            return route('berita');
        }

        return url($path);
    }
}
