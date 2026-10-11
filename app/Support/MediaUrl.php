<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Support;

/**
 * Prevod cesty k obrazku na verejnu URL.
 * Obrazky su bud absolutne URL (http...), alebo subory na "public" disku (storage/...).
 */
final class MediaUrl
{
    public static function from(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return str_starts_with($path, 'http')
            ? $path
            : asset('storage/' . $path);
    }
}
