<?php

// [AI] Vygenerovane s pomocou AI (Claude).

declare(strict_types=1);

namespace App\Enums;

/**
 * Nazvy roli (Spatie Laravel Permission). Opravnenia roli nastavuje RolesAndPermissionsSeeder.
 */
enum UserRole: string
{
    case Admin = 'admin';
    case Employee = 'employee';
    case Designer = 'designer';
    case Customer = 'customer';

    /**
     * Role s pristupom do admin panelu.
     *
     * @return list<string>
     */
    public static function panelRoles(): array
    {
        return [self::Admin->value, self::Employee->value, self::Designer->value];
    }
}
