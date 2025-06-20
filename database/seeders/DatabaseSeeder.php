<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\TenantSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Eerst de rollen aanmaken
        $this->call(RoleSeeder::class);

        // Dan de permissions aanmaken en toekennen
        $this->call(PermissionSeeder::class);

        // Dan de super-admin gebruiker via de UserSeeder
        $this->call(UserSeeder::class);

        $this->call(TenantSeeder::class);
    }
}
