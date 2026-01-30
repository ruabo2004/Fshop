<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $regularPrice = fake()->randomFloat(2, 10, 1000);
        $salePrice = fake()->boolean(70) ? fake()->randomFloat(2, $regularPrice * 0.5, $regularPrice * 0.9) : null;
        
        return [
            'name' => ucfirst($name),
            'slug' => \Illuminate\Support\Str::slug($name),
            'short_description' => fake()->sentence(10),
            'description' => fake()->paragraphs(3, true),
            'regular_price' => $regularPrice,
            'sale_price' => $salePrice,
            'SKU' => 'SKU-' . fake()->unique()->numberBetween(10000, 99999),
            'stock_status' => fake()->randomElement(['instock', 'outofstock']),
            'featured' => fake()->boolean(30),
            'quantity' => fake()->numberBetween(0, 100),
            'image' => 'assets/images/products/product_' . fake()->numberBetween(0, 10) . '.jpg',
            'images' => null,
        ];
    }
}
