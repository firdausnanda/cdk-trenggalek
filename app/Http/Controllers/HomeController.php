<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;
use Jorenvh\Share\ShareFacade as Share;

class HomeController extends Controller
{

    public function index()
    {
        $posts = Post::type('post')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($post) {
                $post->image = $post->featured_image;
                return $post;
            });
        return view('pages.index', compact('posts'));
    }

    public function tentangKami()
    {
        $posts = Post::type('post')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($post) {
                $post->image = $post->featured_image;
                return $post;
            });

        return view('pages.tentang-kami', compact('posts'));
    }

    public function berita(Request $request)
    {
        $datas = $request->search;

        $searchableColumns = Post::getSearchableColumns();
        $posts = Post::type('post')
            ->orderBy('published_at', 'desc')
            ->published()
            ->when($request->kategori, function ($query) use ($request) {
                $query->whereHas('categories', function ($query) use ($request) {
                    $query->where('slug', $request->kategori);
                });
            })
            ->where(function ($query) use ($datas, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $datas . '%');
                }
            })
            ->take(100)
            ->paginate(9);

        return view('pages.berita', compact('posts'));
    }

    public function beritaDetail($slug)
    {
        $post = Post::type('post')
            ->where('slug', $slug)
            ->first();
        
        $posts = Post::type('post')
            ->orderBy('published_at', 'desc')
            ->published()
            ->take(4)
            ->get(['title', 'slug', 'published_at']);

        $kategori = Term::where('taxonomy', 'category')->take(5)->get();

        $share = Share::page(url()->current(), $post->title)
                            ->facebook()
                            ->twitter()
                            ->linkedin()
                            ->whatsapp()
                            ->telegram()
                            ->reddit()
                            ->getRawLinks();
        
        views($post)->cooldown(5)->record();

        return view('pages.berita-show', compact('post', 'kategori', 'posts', 'share'));
    }
}
