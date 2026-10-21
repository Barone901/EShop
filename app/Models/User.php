<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
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
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Pouzivatel ma viacero ulozenych adries.
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
     */
    public function addressesOfType(string $type): Collection
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
        return $this->addressesOfType(UserAddress::TYPE_SHIPPING)->first();
    }

    /**
     * Vrati predvolenu fakturacnu adresu (alebo prvu fakturacnu, ak ziadna nie je predvolena).
     */
    public function getDefaultBillingAddressAttribute(): ?UserAddress
    {
        return $this->addressesOfType(UserAddress::TYPE_BILLING)->first();
    }

    /**
     * Urci ci pouzivatel moze pristupit do admin panelu.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['designer', 'employee', 'admin']);
    }
}
