<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Term;
use App\Rules\NotEmptyHtml;
use App\Support\StatisticsConsent;
use App\Services\SmartHutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
        $cari = trim((string) $request->query('search', ''));
        $kategoriAktif = $request->query('kategori');

        $searchableColumns = Post::getSearchableColumns();
        $posts = Post::type('post')
            ->select(self::CARD_COLUMNS)
            ->with('categories:terms.id,terms.name,terms.slug')
            ->orderBy('published_at', 'desc')
            ->published()
            ->when($kategoriAktif, function ($query) use ($kategoriAktif) {
                $query->whereHas('categories', fn ($query) => $query->where('slug', $kategoriAktif));
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

        return view('public.pages.berita', compact('posts', 'kategori', 'cari', 'kategoriAktif'));
    }

    /**
     * News categories that have at least one post, most used first.
     */
    private function kategoriBerita()
    {
        return Term::where('taxonomy', 'category')
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get(['id', 'name', 'slug']);
    }

    public function beritaDetail(Request $request, $slug)
    {
        $post = Post::type('post')
            ->published()
            ->with(['categories:terms.id,terms.name,terms.slug', 'tags:terms.id,terms.name'])
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

        $comment = Comment::where('is_approved', true)
            ->where('post_id', $post->id)
            ->oldest()
            ->get();

        if (StatisticsConsent::accepts($request)) {
            views($post)->cooldown(5)->record();
        }

        return view('public.pages.berita-show', compact('post', 'posts', 'share', 'comment'));
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
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 5.000 karakter.',
        ]);

        ContactMessage::create($validated);

        // Kirim email
        // Mail::to('cdkwilayahtrenggalek@jatimprov.go.id')->send(new ContactFormMail($validated));

        return redirect()->to(route('kontak') . '#form-kontak')
            ->with('success', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda melalui email.');
    }

    public function commentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'comment' => ['required', 'string', 'max:1000', new NotEmptyHtml()],
            'post_id' => 'required|exists:posts,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'comment.required' => 'Komentar tidak boleh kosong.',
            'post_id.exists' => 'Postingan tidak ditemukan.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->withFragment('comment-form');
        }

        $validated = $validator->validated();

        Comment::create([
            'post_id' => $validated['post_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            // Comments are plain text; drop any markup so it can never run in the site or the admin panel.
            'content' => trim(strip_tags($validated['comment'])),
            'is_approved' => false,
        ]);

        return back()
            ->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan.')
            ->withFragment('comment-form');
    }
}
