<?php

declare(strict_types=1);

namespace App\Models;

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

    // Konstanty pre typy adries
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_BILLING  = 'billing';

    public const DEFAULT_COUNTRY = 'Slovakia';

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
     * Adresa patri do objednavky.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Vytvori atributy adresy z checkout formulara.
     */
    public static function attributesFromCheckout(array $data, string $type): array
    {
        $nameParts = explode(' ', trim((string) ($data["{$type}_name"] ?? '')), 2);

        return [
            'type' => $type,
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'email' => (string) ($data["{$type}_email"] ?? ''),
            'phone' => $data["{$type}_phone"] ?? null,

            'street' => (string) ($data["{$type}_address"] ?? ''),
            'city' => (string) ($data["{$type}_city"] ?? ''),
            'postal_code' => (string) ($data["{$type}_postal_code"] ?? ''),
            'country' => (string) ($data["{$type}_country"] ?? self::DEFAULT_COUNTRY),

            'company_name' => $data["{$type}_company_name"] ?? null,
            'ico' => $data["{$type}_ico"] ?? null,
            'dic' => $data["{$type}_dic"] ?? null,
            'ic_dph' => $data["{$type}_ic_dph"] ?? null,
        ];
    }
}
