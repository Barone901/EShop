<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model adresy objednavky (snapshot).
 * Relacia: Order (N:1)
 */
class OrderAddress extends Model
{
    use HasAddressFormatting;

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

    /**
     * Vytvori atributy adresy z checkout formulara.
     */
    public static function attributesFromCheckout(array $data, AddressType $type): array
    {
        $prefix = $type->value;
        $nameParts = explode(' ', trim((string) ($data["{$prefix}_name"] ?? '')), 2);

        return [
            'type' => $type,
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'email' => (string) ($data["{$prefix}_email"] ?? ''),
            'phone' => $data["{$prefix}_phone"] ?? null,

            'street' => (string) ($data["{$prefix}_address"] ?? ''),
            'city' => (string) ($data["{$prefix}_city"] ?? ''),
            'postal_code' => (string) ($data["{$prefix}_postal_code"] ?? ''),
            'country' => (string) ($data["{$prefix}_country"] ?? config('shop.default_country')),

            'company_name' => $data["{$prefix}_company_name"] ?? null,
            'ico' => $data["{$prefix}_ico"] ?? null,
            'dic' => $data["{$prefix}_dic"] ?? null,
            'ic_dph' => $data["{$prefix}_ic_dph"] ?? null,
        ];
    }
}
