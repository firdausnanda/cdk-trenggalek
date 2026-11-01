<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class StatisticsConsent
{
    public const COOKIE_NAME = 'cdk_statistics_consent';

    public const ACCEPT = 'accept';

    public const REJECT = 'reject';

    public const LIFETIME_MINUTES = 525600;

    public static function choice(Request $request): ?string
    {
        $choice = $request->cookie(self::COOKIE_NAME);

        return in_array($choice, [self::ACCEPT, self::REJECT], true) ? $choice : null;
    }

    public static function accepts(Request $request): bool
    {
        return self::choice($request) === self::ACCEPT;
    }
}
