<?php

declare(strict_types=1);

namespace App\Policies;

use App\Policies\Concerns\AuthorizesByPermission;

/**
 * Pristup k admin sekcii: bannery (sekcia media).
 */
class BannerPolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage media';
}
