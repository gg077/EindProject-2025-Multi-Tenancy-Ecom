<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;


use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    // Traits funtionaliteit
    use HasFactory, SoftDeletes, HasRoles, BelongsToTenant, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'is_admin'
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
    
    /**
     * Boot the model and set up event listeners.
     */
    protected static function booted()
    {
        // wanneer de model geupdated wordt, update de gegevens van de gebruiker met zelfde e-mail
        static::updated(function (User $user) {
            self::withoutTenancy()
                ->where('email', $user->getOriginal('email'))
                ->where('id', '!=', $user->id)
                ->update([
                    'email' => $user->email,
                    'name' => $user->name,
                    'password' => $user->password,
                    'email_verified_at' => $user->email_verified_at
                ]);
        });
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get all tenants where this user exists (based on email).
     * This handles the case where a user can have different tenant_id values across tenants.
     */
    public function tenants()
    {
        // haal alle tenants op waar deze gebruiker bestaat
        return Tenant::whereHas('users', function ($query) {
            $query->withoutTenancy()->where('email', $this->email);
        });
    }

    /**
     * Redirect to the appropriate route after login, register, etc.
     */
    // Bepaal naar welke route de gebruiker moet worden doorgestuurd na login/registratie
    public function getRedirectRoute()
    {
        if (!$this->tenant_id) { // als er geen tenant_id is
            return route('dashboard'); // stuur naar dashboard
        }

        return $this->is_admin ? route('admin.dashboard') : route('home'); // anders stuur naar admin dashboard of home
    }

    /**
     * Check if the user is a tenant admin or from the team of tenant admin
     *
     * @return bool
     */
    // Controleer of de gebruiker tenant admin is of teamlid van een admin
    public function isAdmin(): bool
    {
        return $this->is_admin && 1; // let op: && 1 is redundant, zie opmerking onderaan
    }

    /**
     * Check if the user is a tenant super admin
     *
     * @return bool
     */
    // Controleer of dit een tenant super admin is (zonder rollen)
    public function isTenantSuperAdmin(): bool
    {
        return $this->is_admin && $this->tenant_id != null && $this->roles->isEmpty();
    }

    /**
     * Check if the user is a platform super admin (central admin)
     *
     * @return bool
     */
    // Controleer of dit een centrale super admin is (zonder tenant)
    public function isSuperAdmin(): bool
    {
        return $this->tenant_id == null && $this->is_admin && $this->roles->isEmpty();
    }

    /**
     * Guess the role name of the user.
     */
    // Geef een gok naar de rol van de gebruiker (nuttig voor UI-weergave)
    public function guessRoleName(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }

        return $this->roles->isEmpty() ? (
        $this->is_admin ? config('tenant.admin_role') : config('tenant.default_role')
        ) : $this->roles[0]->name;
    }

    /**
     * Get the reviews for the user.
     */
    // Relatie: gebruiker heeft meerdere reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope the query to load only users of super admin (centeral admin) not from tenants
     */
    // Query scope: alleen gebruikers zonder tenant (centrale admins)
    public function scopeOfSuperAdminOnly($q)
    {
        return $q->whereNull('tenant_id');
    }

    /**
     * Search for users by name or email
     */
    // Query scope: zoeken op naam of e-mail
    public function scopeSearch($query, $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where(function ($q) use ($term) {
                $q->where('name', 'like', "%$term%")
                    ->orWhere('email', 'like', "%$term%");
            });
        });
    }
}
