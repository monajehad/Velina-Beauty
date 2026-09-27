<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makeup Tools', 'slug' => 'makeup-tools', 'icon' => '💄', 'sort_order' => 1],
            ['name' => 'Accessories', 'slug' => 'accessories', 'icon' => '🎀', 'sort_order' => 2],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
