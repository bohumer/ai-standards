<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $skName = fake()->unique()->words(3, true);
        $enName = fake()->unique()->words(3, true);

        return [
            'user_id' => User::factory(),
            'name' => [
                'sk' => ucfirst($skName),
                'en' => ucfirst($enName),
            ],
            'slug' => [
                'sk' => Str::slug($skName),
                'en' => Str::slug($enName),
            ],
            'description' => [
                'sk' => fake()->sentence(12),
                'en' => fake()->sentence(12),
            ],
            'category' => 'electronics',
            'price' => fake()->randomFloat(2, 5, 500),
            'is_active' => true,
            'published_at' => now(),
        ];
    }
}
