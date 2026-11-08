<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model adresy objednavky (snapshot).
 * Relacia: Order (N:1)
 */
class OrderAddress extends Model
{
    /** @use HasFactory<\Database\Factories\OrderAddressFactory> */
    use HasAddressFormatting, HasFactory;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'order_id',
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'street',
        'city',
        'postal_code',
        'country',
        'company_name',
        'ico',
        'dic',
        'ic_dph',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'type' => AddressType::class,
        ];
    }

    /**
     * Adresa patri do objednavky.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
