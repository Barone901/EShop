<?php

// [AI] Upravene s pomocou AI (Claude): MVC a Laravel standard (preklady __(), Attribute accessory, factories, policies...).

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Models\Concerns\HasAddressFormatting;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Model adresy pouzivatela.
 * Relacia: User (N:1)
 */
class UserAddress extends Model
{
    /** @use HasFactory<\Database\Factories\UserAddressFactory> */
    use HasAddressFormatting, HasFactory;

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
            'type' => AddressType::class,
            'is_default' => 'boolean',
        ];
    }

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
    protected function typeLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->type->label());
    }

    /**
     * Nastavi adresu ako predvolenu a zrusi predvolenu u ostatnych adries rovnakeho typu.
     */
    public function makeDefault(): void
    {
        DB::transaction(function () {
            static::where('user_id', $this->user_id)
                ->where('type', $this->type)
                ->whereKeyNot($this->getKey())
                ->update(['is_default' => false]);

            $this->update(['is_default' => true]);
        });
    }
}
