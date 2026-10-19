<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
     * Urci ci pouzivatel moze pristupit do admin panelu.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['designer', 'employee', 'admin']);
    }
}
