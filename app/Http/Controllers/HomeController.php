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
use Jorenvh\Share\ShareFacade as Share;

class HomeController extends Controller
{
    /** Columns needed to render post cards; leaves out the (large) content column. */
    private const CARD_COLUMNS = ['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'created_at'];

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => __('public.validation.name_required'),
            'name.max' => __('public.validation.name_max'),
            'email.required' => __('public.validation.email_required'),
            'email.email' => __('public.validation.email_invalid'),
            'email.max' => __('public.validation.email_max'),
            'message.required' => __('public.validation.message_required'),
            'message.max' => __('public.validation.message_max'),
        ]);

        ContactMessage::create($validated);

        // Kirim email
        // Mail::to('cdkwilayahtrenggalek@jatimprov.go.id')->send(new ContactFormMail($validated));

        return redirect()->to(route('kontak') . '#form-kontak')
            ->with('success', __('public.contact.sent'));
    }
}
