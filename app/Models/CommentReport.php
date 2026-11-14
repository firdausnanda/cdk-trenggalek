<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentReport extends Model
{
    public const REASONS = ['spam', 'abusive', 'sara', 'other'];

    protected $fillable = [
        'comment_id',
        'commenter_id',
        'reason',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function commenter(): BelongsTo
    {
        return $this->belongsTo(Commenter::class);
    }
}
