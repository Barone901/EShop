<?php

declare(strict_types=1);

namespace App\Policies;

use App\Policies\Concerns\AuthorizesByPermission;

/**
 * Pristup k admin sekcii: kategorie produktov.
 */
class CategoryPolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage categories';
}
