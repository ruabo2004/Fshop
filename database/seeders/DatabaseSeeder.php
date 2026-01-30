<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@fshop.com',
            'mobile' => '0123456789',
            'password' => bcrypt('password'),
            'utype' => 'ADM',
        ]);

        // Create Regular Users
        User::factory(10)->create();

        // Create Brands
        $brands = \App\Models\Brand::factory(10)->create();

        // Create Categories
        $categories = \App\Models\Category::factory(15)->create();

        // Create Products with relationships
        \App\Models\Product::factory(50)->create()->each(function ($product) use ($brands, $categories) {
            $product->brand_id = $brands->random()->id;
            $product->category_id = $categories->random()->id;
            $product->save();
        });
    }
}
