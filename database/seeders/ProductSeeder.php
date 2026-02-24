<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID'); 

        $categories = Category::pluck('id');

        if ($categories->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'category_id' => $categories->random(),
                'name'        => $faker->words(3, true),
                'barcode'     => $faker->unique()->ean13(),
                'sku'         => strtoupper(Str::random(8)),
                'price'       => $faker->numberBetween(10, 500) * 1000, 
                'stock'       => $faker->numberBetween(0, 100),
                'cost'        => $faker->numberBetween(5, 250) * 1000,
            ]);
        }
    }
}
