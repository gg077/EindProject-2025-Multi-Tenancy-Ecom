<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = config('tenancy.central_domains')[0]; // gebruik eerste domain (of kies er één bewust)

        $tenant1 = Tenant::firstOrCreate(
            ['id' => 'tenant1'],
            ['website_name' => 'Tenant 1']
        );
        $tenant1->domains()->firstOrCreate(['domain' => 'tenant1.' . $domain]);

        $tenant2 = Tenant::firstOrCreate(
            ['id' => 'tenant2'],
            ['website_name' => 'Tenant 2']
        );
        $tenant2->domains()->firstOrCreate(['domain' => 'tenant2.' . $domain]);

        Tenant::all()->runForEach(function ($tenant) {
            tenancy()->runForMultiple([$tenant], function ($tenant) {
                User::factory()->create([
                    'name' => 'Test User __ ' . $tenant->id,
                    'email' => 'super-admin@example.com',
                    'password' => Hash::make('12345678'),
                    'is_admin' => true
                ]);

                $this->call(CategorySeeder::class, false, ['tenant' => $tenant]);
                $this->call(ProductSeeder::class, false, ['tenant' => $tenant]);
            });
        });
    }
}
