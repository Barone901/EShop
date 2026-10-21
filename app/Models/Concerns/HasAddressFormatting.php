<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasAddressFormatting
{
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
