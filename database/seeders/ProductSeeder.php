<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Tenant;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Tenant $tenant): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'tenant_id' => $tenant->id,
                'category_id' => Category::inRandomOrder()->first()->id,
                'name' => "Test Product $i",
                'slug' => Str::slug("Test Product $i"),
                'description' => "Beschrijving voor product $i",
                'price' => rand(10, 100),
                'images' => json_encode(['uploads/products/dummy.jpg']),
            ]);
        }
    }
}
