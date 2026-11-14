<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Comment;
use App\Models\Commenter;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Term;
use App\Support\StatisticsConsent;
use App\Services\SmartHutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Jorenvh\Share\ShareFacade as Share;

class HomeController extends Controller
{
    /** Columns needed to render post cards; leaves out the (large) content column. */
    private const CARD_COLUMNS = ['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'created_at'];

    /** Contact form anti-spam: successful sends per IP per window, form fill-time bounds, and link cap. */
    private const CONTACT_MAX_ATTEMPTS = 3;
    private const CONTACT_DECAY_SECONDS = 600;
    private const CONTACT_MIN_SECONDS = 3;
    private const CONTACT_MAX_SECONDS = 7200;
    private const CONTACT_MAX_LINKS = 2;

    public function index(SmartHutService $smartHut)
    {
        $posts = Post::type('post')
            ->published()
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get(self::CARD_COLUMNS);

        $kehutanan = $smartHut->summary();

        return view('public.pages.beranda', compact('posts', 'kehutanan'));
    }

    public function aksesibilitas()
    {
        return view('public.pages.aksesibilitas');
    }

    public function kebijakanPrivasi()
    {
        return view('public.pages.kebijakan-privasi');
    }

    public function tentangKami()
    {
        return view('public.pages.tentang-kami');
    }

    public function berita(Request $request)
    {
        // Category filters used to be a query string (?kategori=slug); send them to their own URL.
        if ($slug = $request->query('kategori')) {
            return redirect()->route('berita.kategori', ['slug' => $slug, ...$request->except('kategori')], 301);
        }

        return $this->daftarBerita($request);
    }

    public function beritaKategori(Request $request, string $slug)
    {
        $kategori = Term::where('taxonomy', 'category')->where('slug', $slug)->firstOrFail(['id', 'name', 'slug']);

        return $this->daftarBerita($request, kategori: $kategori);
    }

    public function beritaTag(Request $request, string $slug)
    {
        $tag = Term::where('taxonomy', 'tag')->where('slug', $slug)->firstOrFail(['id', 'name', 'slug']);

        return $this->daftarBerita($request, tag: $tag);
    }

    /**
     * The news list, optionally narrowed to one category or one tag and/or a search.
     */
    private function daftarBerita(Request $request, ?Term $kategori = null, ?Term $tag = null)
    {
        $cari = trim((string) $request->query('search', ''));
        $kategoriAktif = $kategori?->slug;
        $kategoriNama = $kategori?->name;
        $tagAktif = $tag;
        $filterTerm = $kategori ?? $tag;

        $searchableColumns = Post::getSearchableColumns();
        $posts = Post::type('post')
            ->select(self::CARD_COLUMNS)
            ->with('categories:terms.id,terms.name,terms.slug')
            ->orderBy('published_at', 'desc')
            ->published()
            ->when($filterTerm, function ($query) use ($filterTerm) {
                $query->whereHas('terms', fn ($query) => $query->whereKey($filterTerm->id));
            })
            ->when($cari !== '', function ($query) use ($cari, $searchableColumns) {
                $query->where(function ($query) use ($cari, $searchableColumns) {
                    foreach ($searchableColumns as $column) {
                        $query->orWhere($column, 'like', '%' . $cari . '%');
                    }
                });
            })
            ->paginate(9)
            ->withQueryString();

        $kategori = $this->kategoriBerita();

        return view('public.pages.berita', compact('posts', 'kategori', 'cari', 'kategoriAktif', 'kategoriNama', 'tagAktif'));
    }

    /**
     * News categories that have at least one post, most used first.
     */
    private function kategoriBerita()
    {
        return Term::where('taxonomy', 'category')
            ->has('posts')
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->get(['id', 'name', 'slug']);
    }

    public function beritaDetail(Request $request, $slug)
    {
        $post = Post::type('post')
            ->published()
            ->with(['categories:terms.id,terms.name,terms.slug', 'tags:terms.id,terms.name,terms.slug'])
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = Post::type('post')
            ->published()
            ->whereKeyNot($post->id)
            ->with('categories:terms.id,terms.name,terms.slug')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get(self::CARD_COLUMNS);

        $share = Share::page(url()->current(), $post->title)
            ->whatsapp()
            ->facebook()
            ->twitter()
            ->telegram()
            ->getRawLinks();

        $viewer = Auth::guard('commenter')->user();
        $viewer = $viewer instanceof Commenter ? $viewer : null;
        $comments = Comment::where('post_id', $post->id)
            ->visibleTo($viewer)
            ->with(['commenter:id,name,avatar,blocked_at', 'parent.commenter:id,name'])
            ->oldest()
            ->get();

        if (StatisticsConsent::accepts($request)) {
            views($post)->cooldown(5)->record();
        }

        return view('public.pages.berita-show', compact('post', 'posts', 'share', 'comments', 'viewer'));
    }

    public function kontak()
    {
        return view('public.pages.kontak');
    }

    public function kontak_store(Request $request)
    {
        $backToForm = fn () => redirect()->to(route('kontak') . '#form-kontak');

        // Honeypot: people never see the "website" field, bots fill it. Pretend success so they don't adapt.
        if ($request->filled('website')) {
            return $backToForm()->with('success', __('public.contact.sent'));
        }

        // Time trap: a person needs more than a few seconds to fill the form; a stale/missing token is a replay.
        try {
            $elapsed = now()->timestamp - (int) decrypt((string) $request->input('_ts'));
        } catch (\Throwable) {
            $elapsed = -1;
        }
        if ($elapsed < self::CONTACT_MIN_SECONDS || $elapsed > self::CONTACT_MAX_SECONDS) {
            return $backToForm()->withInput()->withErrors(['form' => __('public.contact.retry')]);
        }

        // Per-IP limit with a readable message (the route's throttle middleware is only a hard backstop).
        $key = 'kontak:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, self::CONTACT_MAX_ATTEMPTS)) {
            $minutes = max(1, (int) ceil(RateLimiter::availableIn($key) / 60));

            return $backToForm()->withInput()->withErrors(['form' => __('public.contact.too_many', ['minutes' => $minutes])]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', "regex:/^[\\pL\\pM\\s.,'\\-]+$/u"],
            'email' => 'required|email:rfc,dns|max:255',
            'message' => [
                'required', 'string', 'min:10', 'max:5000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (preg_match_all('~(https?://|www\.)~i', (string) $value) > self::CONTACT_MAX_LINKS) {
                        $fail(__('public.validation.message_links', ['max' => self::CONTACT_MAX_LINKS]));
                    }
                },
            ],
        ], [
            'name.required' => __('public.validation.name_required'),
            'name.max' => __('public.validation.name_max'),
            'name.regex' => __('public.validation.name_invalid'),
            'email.required' => __('public.validation.email_required'),
            'email.email' => __('public.validation.email_invalid'),
            'email.max' => __('public.validation.email_max'),
            'message.required' => __('public.validation.message_required'),
            'message.min' => __('public.validation.message_min'),
            'message.max' => __('public.validation.message_max'),
        ]);

        RateLimiter::hit($key, self::CONTACT_DECAY_SECONDS);

        ContactMessage::create($validated);

        // Kirim email
        // Mail::to('cdkwilayahtrenggalek@jatimprov.go.id')->send(new ContactFormMail($validated));

        return redirect()->to(route('kontak') . '#form-kontak')
            ->with('success', __('public.contact.sent'));
    }
}
