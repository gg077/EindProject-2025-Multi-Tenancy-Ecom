<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(), // Markeer als geverifieerd
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10), // Random token (voor "remember me" sessies)
        ];
    }

    /**
     * Extra configuratie na het aanmaken van het model.
     * Hier wordt standaard de "viewer" rol toegekend aan de gebruiker,
     * tenzij het om de super-admin gaat.
     */
    public function configure()
    {
        return $this->afterCreating(function ($user) {
            if($user->email != 'super-admin@example.com') {
                $role = Role::firstOrCreate(['name' => 'viewer']);
                $user->assignRole($role); // Laravel Spatie Role-permission
            }
        });
    }

    /**
     * Maakt een gebruiker aan met een niet-geverifieerd e-mailadres.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }


    /**
     * Maakt een super-admin gebruiker aan.
     * Gebruikt `afterCreating` om bestaande rollen te verwijderen.
     * Zet specifieke attributen zoals naam, e-mail, en adminstatus.
     */
    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->syncRoles([]); // Verwijdert alle bestaande rollen met permissies
        })->state(fn () => [
            'name' => 'Admin Gebruiker',
            'email' => 'super-admin@example.com',
            'email_verified_at' => now(),
            'is_admin' => true,
            'tenant_id' => null,
            'password' => Hash::make('password'),
        ]);
    }
}
