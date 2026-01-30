<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition(): array
    {
        $categories = ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Toys', 'Food & Beverage', 'Beauty', 'Automotive', 'Health'];
        $name = fake()->randomElement($categories) . ' ' . fake()->word();
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'image' => 'category-' . fake()->numberBetween(1, 10) . '.png',
            'parent_id' => null,
        ];
    }
}
