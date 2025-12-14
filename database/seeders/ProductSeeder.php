<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'name' => 'Wireless Mouse', 'sku' => 'ELEC-001', 'description' => 'Ergonomic wireless mouse', 'price' => 29.99, 'cost' => 15.00, 'stock' => 50, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 1, 'name' => 'USB-C Cable', 'sku' => 'ELEC-002', 'description' => '2m USB-C charging cable', 'price' => 12.99, 'cost' => 5.00, 'stock' => 100, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 1, 'name' => 'Bluetooth Speaker', 'sku' => 'ELEC-003', 'description' => 'Portable bluetooth speaker', 'price' => 49.99, 'cost' => 25.00, 'stock' => 30, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Coffee Beans 500g', 'sku' => 'FOOD-001', 'description' => 'Premium arabica coffee beans', 'price' => 18.99, 'cost' => 10.00, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Organic Green Tea', 'sku' => 'FOOD-002', 'description' => 'Box of 20 tea bags', 'price' => 8.99, 'cost' => 4.00, 'stock' => 60, 'min_stock' => 15, 'is_active' => true],
            ['category_id' => 2, 'name' => 'Energy Drink', 'sku' => 'FOOD-003', 'description' => '250ml energy drink', 'price' => 2.99, 'cost' => 1.00, 'stock' => 200, 'min_stock' => 50, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Cotton T-Shirt', 'sku' => 'CLTH-001', 'description' => '100% cotton casual t-shirt', 'price' => 24.99, 'cost' => 12.00, 'stock' => 75, 'min_stock' => 15, 'is_active' => true],
            ['category_id' => 3, 'name' => 'Denim Jeans', 'sku' => 'CLTH-002', 'description' => 'Classic fit denim jeans', 'price' => 59.99, 'cost' => 30.00, 'stock' => 40, 'min_stock' => 10, 'is_active' => true],
            ['category_id' => 4, 'name' => 'LED Light Bulb', 'sku' => 'HOME-001', 'description' => '10W LED bulb warm white', 'price' => 6.99, 'cost' => 3.00, 'stock' => 150, 'min_stock' => 30, 'is_active' => true],
            ['category_id' => 4, 'name' => 'Garden Hose 15m', 'sku' => 'HOME-002', 'description' => 'Expandable garden hose', 'price' => 34.99, 'cost' => 18.00, 'stock' => 25, 'min_stock' => 5, 'is_active' => true],
            ['category_id' => 5, 'name' => 'Notebook A5', 'sku' => 'BOOK-001', 'description' => 'Ruled notebook 200 pages', 'price' => 4.99, 'cost' => 2.00, 'stock' => 120, 'min_stock' => 25, 'is_active' => true],
            ['category_id' => 5, 'name' => 'Ballpoint Pen Set', 'sku' => 'BOOK-002', 'description' => 'Pack of 10 blue pens', 'price' => 7.99, 'cost' => 3.50, 'stock' => 80, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 6, 'name' => 'Hand Sanitizer 500ml', 'sku' => 'HLTH-001', 'description' => 'Antibacterial hand sanitizer', 'price' => 9.99, 'cost' => 4.50, 'stock' => 90, 'min_stock' => 20, 'is_active' => true],
            ['category_id' => 6, 'name' => 'Face Cream 50ml', 'sku' => 'HLTH-002', 'description' => 'Moisturizing face cream', 'price' => 22.99, 'cost' => 11.00, 'stock' => 45, 'min_stock' => 10, 'is_active' => true],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
