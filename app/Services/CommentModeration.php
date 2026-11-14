<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Commenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Rules for comments that go live without prior approval: a word/link filter that holds suspicious
 * comments back, and visitor reports that take a comment off the site once enough people flag it.
 * Both settings are edited on the admin Comments page and stored in the settings table.
 */
class CommentModeration
{
    public const SETTING_BLOCKED_WORDS = 'comment_blocked_words';

    public const SETTING_REPORT_THRESHOLD = 'comment_report_threshold';

    public const DEFAULT_REPORT_THRESHOLD = 3;

    /** Max comments one visitor may send in the window below. */
    public const RATE_LIMIT = 5;

    public const RATE_LIMIT_SECONDS = 600;

    /** @return list<string> lowercase words/phrases, one per line or comma in the setting */
    public function blockedWords(): array
    {
        $raw = (string) config('settings.'.self::SETTING_BLOCKED_WORDS, '');

        return collect(preg_split('/[\r\n,]+/', $raw) ?: [])
            ->map(fn (string $word) => mb_strtolower(trim($word)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function reportThreshold(): int
    {
        $value = (int) config('settings.'.self::SETTING_REPORT_THRESHOLD, self::DEFAULT_REPORT_THRESHOLD);

        return $value > 0 ? $value : self::DEFAULT_REPORT_THRESHOLD;
    }

    /** Status a new visitor comment starts with: live, unless the filter catches it. */
    public function statusFor(string $content): string
    {
        return $this->needsReview($content) ? Comment::STATUS_PENDING_REVIEW : Comment::STATUS_PUBLISHED;
    }

    public function needsReview(string $content): bool
    {
        // Links are the usual payload of spam; hold them for a human.
        if (preg_match('~(https?://|www\.)\S+|\b[a-z0-9-]{2,}\.(com|net|org|xyz|top|ru|io|id|ly|link|click|site|online)\b~iu', $content)) {
            return true;
        }

        $text = mb_strtolower($content);

        foreach ($this->blockedWords() as $word) {
            // Whole words only, so a short word in the list does not catch longer, harmless words that
            // happen to contain it; phrases with spaces match as written.
            $pattern = '/(?<![\p{L}\p{N}])'.preg_quote($word, '/').'(?![\p{L}\p{N}])/u';
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /** True when this visitor already sent the same text recently (double send or copy-paste spam). */
    public function isDuplicate(Commenter $commenter, string $content): bool
    {
        return $commenter->comments()
            ->where('content', $content)
            ->where('created_at', '>=', now()->subHour())
            ->exists();
    }

    /**
     * Record one visitor's report. Hides the comment once reports reach the threshold.
     *
     * @return bool false when this visitor had already reported it
     */
    public function report(Comment $comment, Commenter $reporter, string $reason): bool
    {
        return DB::transaction(function () use ($comment, $reporter, $reason) {
            $report = CommentReport::firstOrCreate(
                ['comment_id' => $comment->id, 'commenter_id' => $reporter->id],
                ['reason' => $reason],
            );

            if (! $report->wasRecentlyCreated) {
                return false;
            }

            $count = $comment->reports()->count();
            $comment->reports_count = $count;

            if ($comment->isPublished() && $count >= $this->reportThreshold()) {
                $comment->status = Comment::STATUS_HIDDEN;
            }

            $comment->save();

            return true;
        });
    }

    /** An admin put the comment back: it goes live and earlier reports are settled. */
    public function publish(Comment $comment): void
    {
        DB::transaction(function () use ($comment) {
            $comment->reports()->delete();
            $comment->update(['status' => Comment::STATUS_PUBLISHED, 'reports_count' => 0]);
        });
    }

    public function hide(Comment $comment): void
    {
        $comment->update(['status' => Comment::STATUS_HIDDEN]);
    }

    /** Plain text only: markup is dropped so it can never run on the site or in the admin panel. */
    public static function clean(string $content): string
    {
        $text = trim(strip_tags($content));
        // Collapse runs of blank lines, as a chat would.
        $text = preg_replace("/(\r?\n){3,}/", "\n\n", $text) ?? $text;

        return Str::limit($text, 1000, '');
    }
}
