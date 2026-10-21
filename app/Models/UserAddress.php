<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model adresy pouzivatela.
 * Relacia: User (N:1)
 */
class UserAddress extends Model
{
    use HasAddressFormatting;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'user_id',
        'type',
        'is_default',
        'first_name',
        'last_name',
        'phone',
        'email',
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
            'is_default' => 'boolean',
        ];
    }

    // Konstanty pre typy adries
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_BILLING  = 'billing';

    public const TYPES = [
        self::TYPE_SHIPPING => 'Shipping',
        self::TYPE_BILLING => 'Billing',
    ];

    public const DEFAULT_COUNTRY = 'Slovakia';

    /**
     * Adresa patri pouzivatelovi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vrati nazov typu adresy pre UI ("Shipping" / "Billing").
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? (string) $this->type;
    }

    /**
     * Nastavi adresu ako predvolenu a zrusi predvolenu u ostatnych adries rovnakeho typu.
     */
    public function makeDefault(): void
    {
        static::where('user_id', $this->user_id)
            ->where('type', $this->type)
            ->whereKeyNot($this->getKey())
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }
}
