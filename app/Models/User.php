<?php

// [AI] Upravene s pomocou AI (Claude): opravy typov a anotacii (upozornenia PhpStorm/PHPStan).

declare(strict_types=1);

namespace App\Models;

use App\Enums\AddressType;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

/**
 * Model pouzivatela aplikacie.
 * Relacie: Order (1:N), UserAddress (1:N)
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Polia povolene pre hromadne priradenie.
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
    ];

    /**
     * Polia skryte pri serializacii.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Pretypovanie atributov.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELACIE
    // =========================================================================

    /**
     * Pouzivatel ma viacero objednavok.
     *
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Pouzivatel ma viacero ulozenych adries.
     *
     * @return HasMany<UserAddress, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    // =========================================================================
    // ACCESSORY
    // =========================================================================

    /**
     * Vrati cele meno (krstne + priezvisko alebo username).
     */
    public function getFullNameAttribute(): string
    {
        if (!empty($this->first_name) || !empty($this->last_name)) {
            return trim("{$this->first_name} {$this->last_name}");
        }

        return (string) $this->name;
    }

    /**
     * Vrati adresy daneho typu, predvolena je prva.
     * Pouziva nacitanu relaciu $this->addresses, takze vsetky adresy sa nacitaju jednym dotazom.
     *
     * @return Collection<int, UserAddress>
     */
    public function addressesOfType(AddressType $type): Collection
    {
        return $this->addresses
            ->where('type', $type)
            ->sortByDesc('is_default')
            ->values();
    }

    /**
     * Vrati predvolenu dorucovaciu adresu (alebo prvu dorucovaciu, ak ziadna nie je predvolena).
     */
    public function getDefaultShippingAddressAttribute(): ?UserAddress
    {
        return $this->addressesOfType(AddressType::Shipping)->first();
    }

    /**
     * Vrati predvolenu fakturacnu adresu (alebo prvu fakturacnu, ak ziadna nie je predvolena).
     */
    public function getDefaultBillingAddressAttribute(): ?UserAddress
    {
        return $this->addressesOfType(AddressType::Billing)->first();
    }

    // =========================================================================
    // FILAMENT A AUTORIZACIA
    // =========================================================================

    /**
     * Urci ci pouzivatel moze pristupit do admin panelu.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(UserRole::panelRoles());
    }
}
