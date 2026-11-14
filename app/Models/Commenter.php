<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A visitor signed in with Google to comment on news. Authenticates on the `commenter` guard only.
 */
class Commenter extends Authenticatable
{
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'avatar',
        'blocked_at',
        'last_login_at',
    ];

    protected $hidden = [
        'google_id',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /** Google sign-in works only once the OAuth client is set in .env. */
    public static function signInAvailable(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    public function initial(): string
    {
        return mb_strtoupper(mb_substr(trim($this->name), 0, 1)) ?: '?';
    }
}
