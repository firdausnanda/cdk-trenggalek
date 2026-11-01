{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach (['home', 'berita', 'tentang-kami', 'kontak', 'aksesibilitas', 'kebijakan-privasi'] as $halaman)
        <url>
            <loc>{{ route($halaman) }}</loc>
            @if ($halaman === 'berita' && $posts)
                <lastmod>{{ $posts[0]['lastmod'] }}</lastmod>
            @endif
        </url>
    @endforeach
    @foreach ($kategori as $slug)
        <url>
            <loc>{{ route('berita.kategori', $slug) }}</loc>
        </url>
    @endforeach
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('berita.show', $post['slug']) }}</loc>
            @if ($post['lastmod'])
                <lastmod>{{ $post['lastmod'] }}</lastmod>
            @endif
            @if ($post['gambar'])
                <image:image>
                    <image:loc>{{ url($post['gambar']) }}</image:loc>
                </image:image>
            @endif
        </url>
    @endforeach
</urlset>
