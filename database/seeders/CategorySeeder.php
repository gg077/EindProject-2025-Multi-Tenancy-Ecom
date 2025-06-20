<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Tenant $tenant): void
    {
        $categories = [
            'Courses',
            'Accounts',
            'Design',
            'Web Development',
            'Mobile Development',
            'Outreach Data',
        ];

        foreach ($categories as $category) {
            Category::create([
                'tenant_id' => $tenant->id,
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
