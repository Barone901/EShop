<?php

declare(strict_types=1);

namespace App\Policies;

use App\Policies\Concerns\AuthorizesByPermission;

/**
 * Pristup k admin sekcii: produkty.
 */
class ProductPolicy
{
    use AuthorizesByPermission;

    private const PERMISSION = 'manage products';
}
