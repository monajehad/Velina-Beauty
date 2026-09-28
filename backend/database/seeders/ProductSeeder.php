<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $accessories = Category::where('slug', 'accessories')->first();
        $makeup = Category::where('slug', 'makeup-tools')->first();

        $products = [
            ['category_id' => $accessories->id, 'name' => 'Hair Clips - Pastel Set', 'barcode' => '00101', 'price' => 9.600, 'sort_order' => 1],
            ['category_id' => $accessories->id, 'name' => 'Bow Hair Clips', 'barcode' => '00102', 'price' => 9.600, 'sort_order' => 2],
            ['category_id' => $makeup->id, 'name' => 'Makeup Brush Set', 'barcode' => '00103', 'price' => 9.600, 'sort_order' => 3],
            ['category_id' => $makeup->id, 'name' => 'Beauty Blender Sponges', 'barcode' => '00104', 'price' => 9.600, 'sort_order' => 4],
            ['category_id' => $accessories->id, 'name' => 'Velvet Scrunchies', 'barcode' => '00105', 'price' => 9.600, 'sort_order' => 5],
            ['category_id' => $makeup->id, 'name' => 'Matte Lip Gloss Set', 'barcode' => '00106', 'price' => 9.600, 'sort_order' => 6],
            ['category_id' => $accessories->id, 'name' => 'Pearl Hair Clips', 'barcode' => '00107', 'price' => 9.600, 'sort_order' => 7],
            ['category_id' => $makeup->id, 'name' => 'Eyeshadow Palette', 'barcode' => '00108', 'price' => 9.600, 'sort_order' => 8],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                ['barcode' => $product['barcode']],
                $product + ['default_qty' => 50, 'is_active' => true]
            );
        }
    }
}
