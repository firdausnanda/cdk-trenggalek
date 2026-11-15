<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Commenter;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sample news comments from Google commenters, with replies between readers and official replies from the admin.
 * Safe to run again: it first removes the commenters (and their threads) it created before.
 */
class CommentSeeder extends Seeder
{
    private const GOOGLE_ID_PREFIX = 'seed-';

    /** @var list<array{0: string, 1: string}> */
    private const COMMENTERS = [
        ['Budi Santoso', 'budi.santoso'],
        ['Siti Rahayu', 'siti.rahayu'],
        ['Agus Prasetyo', 'agus.prasetyo'],
        ['Dewi Lestari', 'dewi.lestari'],
        ['Hendra Wijaya', 'hendra.wijaya'],
        ['Rina Kurniawati', 'rina.kurniawati'],
        ['Joko Susilo', 'joko.susilo'],
        ['Nur Aini', 'nur.aini'],
        ['Fajar Nugroho', 'fajar.nugroho'],
        ['Lina Marlina', 'lina.marlina'],
    ];

    private const TOP_LEVEL = [
        'Kegiatan yang sangat bermanfaat. Semoga program seperti ini bisa terus dilanjutkan dan menjangkau desa-desa lain di Trenggalek.',
        'Terima kasih CDK Wilayah Trenggalek atas informasinya. Sangat membantu kami para petani hutan untuk mengetahui program yang sedang berjalan.',
        'Apakah masyarakat umum bisa ikut serta dalam kegiatan berikutnya? Kalau bisa, ke mana kami harus mendaftar?',
        'Mantap, semoga kelestarian hutan di Trenggalek tetap terjaga untuk anak cucu kita.',
        'Mohon info jadwal penyuluhan berikutnya untuk wilayah Kecamatan Watulimo.',
        'Program yang bagus. Saran saya, hasilnya juga dipantau secara berkala supaya bibit yang ditanam benar-benar tumbuh.',
        'Kelompok tani hutan di desa kami sangat terbantu dengan pendampingan dari CDK. Matur nuwun.',
        'Apakah ada bantuan bibit untuk lahan milik pribadi? Kami punya lahan kosong sekitar setengah hektare.',
        'Senang melihat kolaborasi antara pemerintah, swasta, dan masyarakat seperti ini. Semoga menjadi contoh bagi daerah lain.',
        'Beritanya informatif. Kalau boleh, lain kali ditambahkan dokumentasi video kegiatannya.',
        'Bagaimana cara mengajukan izin pemanfaatan hasil hutan bukan kayu untuk kelompok kami?',
        'Semoga kegiatan ini juga melibatkan generasi muda, terutama pelajar, agar mereka peduli lingkungan sejak dini.',
    ];

    private const READER_REPLIES = [
        'Setuju sekali, Pak. Di desa kami juga sangat membutuhkan program serupa.',
        'Saya juga ingin tahu soal ini. Semoga segera ada jawaban dari admin.',
        'Betul, kemarin saya ikut kegiatannya dan memang sangat bermanfaat.',
        'Bisa coba datang langsung ke kantor CDK, Bu. Waktu itu saya dilayani dengan baik.',
    ];

    private const ADMIN_REPLIES = [
        'Terima kasih atas dukungannya. Program ini akan terus kami lanjutkan dan perluas ke wilayah lain secara bertahap.',
        'Terima kasih atas pertanyaannya. Informasi kegiatan berikutnya akan kami umumkan melalui website dan media sosial resmi CDK Wilayah Trenggalek.',
        'Silakan menghubungi kantor CDK Wilayah Trenggalek pada jam kerja atau melalui halaman Kontak untuk informasi lebih lanjut.',
        'Terima kasih atas sarannya, akan menjadi bahan evaluasi kami untuk kegiatan berikutnya.',
    ];

    /** Pending review: contains a link, as the moderation filter would flag. */
    private const PENDING = 'Info lengkap soal bantuan bibit bisa dilihat di https://contoh-tautan.test, silakan cek.';

    public function run(): void
    {
        if (! Schema::hasTable('comments') || ! Schema::hasTable('commenters')) {
            $this->command?->info('Comment tables not yet migrated. Skipping comment seeding.');

            return;
        }

        $posts = Post::query()->where('post_type', 'post')->where('status', 'publish')->latest('published_at')->get();
        if ($posts->isEmpty()) {
            $this->command?->info('No published posts. Skipping comment seeding.');

            return;
        }

        mt_srand(2026);
        $admin = User::query()->orderBy('id')->first();

        DB::transaction(function () use ($posts, $admin): void {
            $this->clearPrevious();
            $commenters = $this->createCommenters();
            $total = 0;

            foreach ($posts as $index => $post) {
                // The newest posts get the liveliest threads; a few older posts stay without comments.
                $jumlah = match (true) {
                    $index < 3 => mt_rand(4, 6),
                    $index < 12 => mt_rand(1, 4),
                    default => mt_rand(0, 2),
                };
                $total += $this->seedPost($post, $commenters, $admin, $jumlah);
            }

            $this->command?->info("Seeded {$total} comments on {$posts->count()} posts.");
        });
    }

    private function clearPrevious(): void
    {
        $ids = Commenter::query()->where('google_id', 'like', self::GOOGLE_ID_PREFIX.'%')->pluck('id');
        if ($ids->isEmpty()) {
            return;
        }

        $roots = Comment::query()->whereIn('commenter_id', $ids)->pluck('id');
        // Replies (including admin replies) to the seeded comments go first.
        Comment::query()->whereIn('parent_id', $roots)->delete();
        Comment::query()->whereIn('id', $roots)->delete();
        Commenter::query()->whereIn('id', $ids)->delete();
    }

    /** @return list<Commenter> */
    private function createCommenters(): array
    {
        return array_map(fn (array $c) => Commenter::query()->create([
            'google_id' => self::GOOGLE_ID_PREFIX.$c[1],
            'name' => $c[0],
            'email' => $c[1].'@example.test',
            'avatar' => null,
            'last_login_at' => now()->subDays(mt_rand(0, 14)),
        ]), self::COMMENTERS);
    }

    /** @param list<Commenter> $commenters */
    private function seedPost(Post $post, array $commenters, ?User $admin, int $jumlah): int
    {
        if ($jumlah === 0) {
            return 0;
        }

        $mulai = Carbon::parse($post->published_at ?? $post->created_at)->max(now()->subDays(30));
        $waktu = $mulai->copy()->addHours(mt_rand(1, 6));
        $dibuat = 0;

        foreach ($this->pick(self::TOP_LEVEL, $jumlah) as $isi) {
            $penulis = $commenters[array_rand($commenters)];
            $waktu = $this->next($waktu);
            $induk = $this->comment($post, $penulis, $isi, $waktu);
            $dibuat++;

            // Another reader chimes in.
            if (mt_rand(1, 100) <= 30) {
                $lain = $this->other($commenters, $penulis);
                $waktu = $this->next($waktu);
                $this->comment($post, $lain, self::READER_REPLIES[array_rand(self::READER_REPLIES)], $waktu, $induk->id);
                $dibuat++;
            }

            // Official answer from the admin.
            if ($admin && mt_rand(1, 100) <= 45) {
                $waktu = $this->next($waktu);
                $this->adminReply($post, $admin, $induk->id, $waktu);
                $dibuat++;
            }
        }

        // An occasional comment still waiting for moderation (only its author sees it).
        if (mt_rand(1, 100) <= 15) {
            $waktu = $this->next($waktu);
            $this->comment($post, $commenters[array_rand($commenters)], self::PENDING, $waktu, null, Comment::STATUS_PENDING_REVIEW);
            $dibuat++;
        }

        return $dibuat;
    }

    private function comment(Post $post, Commenter $c, string $isi, Carbon $waktu, ?int $parentId = null, string $status = Comment::STATUS_PUBLISHED): Comment
    {
        $comment = new Comment([
            'post_id' => $post->id,
            'commenter_id' => $c->id,
            'parent_id' => $parentId,
            'name' => $c->name,
            'email' => $c->email,
            'content' => $isi,
            'status' => $status,
        ]);
        $comment->created_at = $waktu;
        $comment->updated_at = $waktu;
        $comment->save();

        return $comment;
    }

    private function adminReply(Post $post, User $admin, int $parentId, Carbon $waktu): void
    {
        $comment = new Comment([
            'post_id' => $post->id,
            'user_id' => $admin->id,
            'is_official' => true,
            'parent_id' => $parentId,
            'name' => $admin->name,
            'email' => $admin->email,
            'content' => self::ADMIN_REPLIES[array_rand(self::ADMIN_REPLIES)],
            'status' => Comment::STATUS_PUBLISHED,
        ]);
        $comment->created_at = $waktu;
        $comment->updated_at = $waktu;
        $comment->save();
    }

    /** Move forward a little in time, never past now. */
    private function next(Carbon $waktu): Carbon
    {
        return $waktu->copy()->addMinutes(mt_rand(20, 60 * 20))
            ->min(now()->subMinutes(mt_rand(1, 30)))
            ->max($waktu->copy()->addMinute());
    }

    /** @param list<Commenter> $commenters */
    private function other(array $commenters, Commenter $kecuali): Commenter
    {
        do {
            $c = $commenters[array_rand($commenters)];
        } while ($c->is($kecuali));

        return $c;
    }

    /**
     * @param  list<string>  $items
     * @return list<string>
     */
    private function pick(array $items, int $n): array
    {
        shuffle($items);

        return array_slice($items, 0, min($n, count($items)));
    }
}
