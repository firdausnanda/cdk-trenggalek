<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A comment on a news post. New comments come from a Google-signed-in visitor (commenter) or a
 * replying admin (user); older ones only carry a typed name and email.
 *
 * @property-read Post|null $post
 * @property-read Commenter|null $commenter
 * @property-read User|null $user
 * @property-read Comment|null $parent
 */
class Comment extends Model
{
    /** Live on the site. */
    public const STATUS_PUBLISHED = 'published';

    /** Taken off the site, by reaching the report threshold or by an admin. */
    public const STATUS_HIDDEN = 'hidden';

    /** Held back by the word/link filter until an admin looks at it. */
    public const STATUS_PENDING_REVIEW = 'pending_review';

    public const STATUSES = [self::STATUS_PUBLISHED, self::STATUS_HIDDEN, self::STATUS_PENDING_REVIEW];

    protected $fillable = [
        'post_id',
        'commenter_id',
        'user_id',
        'parent_id',
        'name',
        'email',
        'content',
        'status',
        'reports_count',
    ];

    protected $table = 'comments';

    protected function casts(): array
    {
        return [
            'reports_count' => 'integer',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function commenter(): BelongsTo
    {
        return $this->belongsTo(Commenter::class);
    }

    /** The admin who wrote this reply, if any. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /** Comments that need an admin: held back, hidden, or reported while still live. */
    public function scopeNeedsReview(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereIn('status', [self::STATUS_HIDDEN, self::STATUS_PENDING_REVIEW])
                ->orWhere('reports_count', '>', 0);
        });
    }

    /**
     * What one news page shows: live comments from visitors who are not blocked, plus every comment
     * of the visitor viewing it (so they can see their own held-back or hidden comment and its status).
     */
    public function scopeVisibleTo(Builder $query, ?Commenter $viewer): Builder
    {
        return $query->where(function (Builder $query) use ($viewer) {
            $query->where(function (Builder $query) {
                $query->where('status', self::STATUS_PUBLISHED)->where(function (Builder $query) {
                    $query->whereNull('commenter_id')
                        ->orWhereHas('commenter', fn (Builder $query) => $query->whereNull('blocked_at'));
                });
            });

            if ($viewer) {
                $query->orWhere('commenter_id', $viewer->id);
            }
        });
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isFromAdmin(): bool
    {
        return $this->user_id !== null;
    }

    public function isOwnedBy(?Commenter $commenter): bool
    {
        return $commenter !== null && $this->commenter_id === $commenter->id;
    }

    public function authorName(): string
    {
        if ($this->isFromAdmin()) {
            return (string) config('cdk.nama_singkat', 'Admin');
        }

        return (string) ($this->commenter->name ?? $this->name ?? '');
    }

    public function authorEmail(): ?string
    {
        return $this->commenter->email ?? $this->user->email ?? $this->email;
    }

    public function authorAvatar(): ?string
    {
        return $this->commenter?->avatar;
    }

    public function authorInitial(): string
    {
        return mb_strtoupper(mb_substr(trim($this->authorName()), 0, 1)) ?: '?';
    }
}
