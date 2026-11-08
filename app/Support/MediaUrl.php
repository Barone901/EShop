<?php

// [AI] Vygenerovane s pomocou AI (Claude).
// [AI] Upravene s pomocou AI (Claude): URL suboru vytvara Laravel Storage (disk "public").

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Prevod cesty k obrazku na verejnu URL.
 * Obrazky su bud absolutne URL (http...), alebo subory na disku "public" (upload z administracie),
 * ktorych URL vytvori Storage::disk('public')->url() podla config/filesystems.php.
 */
final class MediaUrl
{
    public static function from(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }
}
