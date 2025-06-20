<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Category::class; // Verbindt deze factory aan het Category model

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        // Genereert een unieke string van 2 willekeurige woorden als de naam van de categorie
        // Bijvoorbeeld: "sport nieuws"

        return [
            'name' => $name,
            'slug' => Str::slug($name), // Maakt een slug-versie van de naam
            'description' => $this->faker->paragraph(),
        ];

    }
}
