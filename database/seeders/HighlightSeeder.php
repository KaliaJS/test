<?php

namespace Database\Seeders;

use App\Models\Highlight;
use App\Models\Product;
use Illuminate\Database\Seeder;

class HighlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Highlight::factory(3)->create()->each(function ($highlight) {
            $products = Product::inRandomOrder()
                ->take(rand(2, 4))
                ->get();
            $highlight->products()->attach(
                $products->pluck('id')->toArray(),
                ['quantity' => 1]
            );
        });
    }
}
