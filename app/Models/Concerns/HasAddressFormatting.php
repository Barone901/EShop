<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Spolocne pre UserAddress a OrderAddress: cele meno z mena a priezviska ($address->full_name).
 */
trait HasAddressFormatting
{
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim("{$this->first_name} {$this->last_name}"));
    }
}
