<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'is_active' => true],
            ['name' => 'Food & Beverages', 'description' => 'Food items and drinks', 'is_active' => true],
            ['name' => 'Clothing', 'description' => 'Apparel and fashion items', 'is_active' => true],
            ['name' => 'Home & Garden', 'description' => 'Home improvement and garden supplies', 'is_active' => true],
            ['name' => 'Books & Stationery', 'description' => 'Books, office supplies, and stationery', 'is_active' => true],
            ['name' => 'Health & Beauty', 'description' => 'Health and beauty products', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
