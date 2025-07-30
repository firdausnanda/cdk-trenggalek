<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Jorenvh\Share\ShareFacade as Share;

use function PHPSTORM_META\map;

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

        // KTH
        $filePath = public_path('data/kth.csv');
        $lines = file($filePath);
        $kth = count($lines) - 1;

        // Jumlah Anggota terlibat
        $csv = file_get_contents($filePath);
        $lines = explode("\n", $csv);
        $totalAnggota = 0;

        foreach ($lines as $key => $row) {
            if ($key === 0 || trim($row) === '') {
                continue;
            }
            $columns = str_getcsv($row);
            if (isset($columns[6]) && is_numeric($columns[6])) {
                $totalAnggota += (int)$columns[6];
            }
        }

        // PBPHH
        $filePath = public_path('data/pbphh.csv');
        $lines = file($filePath);
        $pbphh = count($lines) - 1;
        
        // PS
        $filePath = public_path('data/ps.csv');
        $lines = file($filePath);
        $ps = count($lines) - 1;

        return view('pages.tentang-kami', compact('posts', 'kth', 'totalAnggota', 'pbphh', 'ps'));
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

        if (!$post) {
            abort(404);
        }

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

    public function kontak()
    {
        return view('pages.kontak');
    }

    public function kontak_store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Jika ingin menyimpan ke database
        ContactMessage::create($validated);

        // Kirim email
        // Mail::to('cdkwilayahtrenggalek@jatimprov.go.id')->send(new ContactFormMail($validated));

        return back()->with('success', '<div class="fw-bold">Pesan Anda telah terkirim! <br><span class="fw-normal">Kami akan segera menghubungi Anda.</span></div>');
    }

    public function kth(Request $request)
    {
        $csv = file_get_contents(public_path('data/kth.csv'));
        $lines = explode("\n", $csv);
        $datas = [];

        foreach ($lines as $key => $row) {
            if ($key === 0 || trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);

            if (count($columns) >= 8) {
                $datas[] = [
                    'nama' => $columns[1] ?? '',
                    'alamat' => $columns[2] ?? '',
                    'ketua' => $columns[3] ?? '',
                    'jenis_usaha' => $columns[4] ?? '',
                    'no_registrasi' => $columns[5] ?? '',
                    'wilayah' => $columns[7] ?? ''
                ];
            }
        }

        if ($request->has('wilayah_filter') && $request->wilayah_filter != null) {
            $datas = array_values(array_filter($datas, function ($item) use ($request) {
                return $item['wilayah'] == $request->wilayah_filter;
            }));
        }

        return ResponseFormatter::success($datas, 'Data berhasil diambil');
    }

    public function masyarakat(Request $request)
    {
        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
            'values' => [65, 59, 80, 81],
            'colors' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
        ];

        // Jumlah Anggota terlibat
        $csv = file_get_contents(public_path('data/kth.csv'));
        $lines = explode("\n", $csv);
        $anggotaPerWilayah = [];
        $header = [];
        $wilayahIndex = null;
        $anggotaIndex = null;

        $mappingWilayah = [
            '1' => 'Trenggalek',
            '2' => 'Tulungagung',
            '3' => 'Kediri',
            'default' => 'Lainnya',
        ];

        foreach ($lines as $key => $row) {
            if (trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);

            if ($key === 0) {
                $header = $columns;
                $wilayahIndex = array_search('wilker', $header);
                $anggotaIndex = array_search('anggota', $header);
                continue;
            }

            if ($wilayahIndex !== false && $anggotaIndex !== false) {
                $wilayahAsli = trim($columns[$wilayahIndex]);
                $jumlah = is_numeric($columns[$anggotaIndex]) ? (int)$columns[$anggotaIndex] : 0;

                $wilayahBaru = $mappingWilayah[$wilayahAsli] ?? $mappingWilayah['default'] ?? $wilayahAsli;

                if (!isset($anggotaPerWilayah[$wilayahBaru])) {
                    $anggotaPerWilayah[$wilayahBaru] = 0;
                }

                $anggotaPerWilayah[$wilayahBaru] += $jumlah;
            }
        }

        return response()->json([
            'labels' => array_keys($anggotaPerWilayah),
            'values' => array_values($anggotaPerWilayah),
            'colors' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
        ]);
    }

    public function pbphh(Request $request)
    {
        $csv = file_get_contents(public_path('data/pbphh.csv'));
        $lines = explode("\n", $csv);
        $datas = [];

        foreach ($lines as $key => $row) {
            if ($key === 0 || trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);

            if (count($columns) >= 8) {
                $datas[] = [
                    'nama_pbphh' => $columns[1] ?? '',
                    'kantor' => $columns[2] ?? '',
                    'pabrik' => $columns[3] ?? '',
                    'no_izin' => $columns[4] ?? '',
                    'direktur' => $columns[5] ?? '',
                    'skala' => $columns[6] ?? '',
                    'wilayah' => $columns[7] ?? ''
                ];
            }
        }

        if ($request->has('wilayah_filter') && $request->wilayah_filter != null) {
            $datas = array_values(array_filter($datas, function ($item) use ($request) {
                return $item['wilayah'] == $request->wilayah_filter;
            }));
        }

        return ResponseFormatter::success($datas, 'Data berhasil diambil');
    }

    public function ps(Request $request)
    {
        $csv = file_get_contents(public_path('data/ps.csv'));
        $lines = explode("\n", $csv);
        $datas = [];

        foreach ($lines as $key => $row) {
            if ($key === 0 || trim($row) === '') {
                continue;
            }

            $columns = str_getcsv($row);

            if (count($columns) >= 9) {
                $datas[] = [
                    'nama' => $columns[1] ?? '',
                    'skema' => $columns[2] ?? '',
                    'khdpk' => $columns[3] ?? '',
                    'no_sk' => $columns[4] ?? '',
                    'alamat' => $columns[5] ?? '',
                    'jumlah_kk' => $columns[6] ?? '',
                    'luas' => $columns[7] ?? '',
                    'ketua' => $columns[8] ?? '',
                    'wilayah' => $columns[9] ?? ''
                ];
            }
        }

        if ($request->has('wilayah_filter') && $request->wilayah_filter != null) {
            $datas = array_values(array_filter($datas, function ($item) use ($request) {
                return $item['wilayah'] == $request->wilayah_filter;
            }));
        }

        return ResponseFormatter::success($datas, 'Data berhasil diambil');
    }
}
