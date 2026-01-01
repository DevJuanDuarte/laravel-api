<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->category = Category::factory()->create();
});

test('can list products', function () {
    Product::factory()->count(5)->create(['category_id' => $this->category->id]);

    $response = $this->getJson('/api/v1/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'sku', 'price', 'stock', 'category']
            ]
        ]);
});

test('can create product', function () {
    $productData = [
        'category_id' => $this->category->id,
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'description' => 'Test Description',
        'price' => 99.99,
        'cost' => 50.00,
        'stock' => 100,
        'min_stock' => 10,
        'is_active' => true,
    ];

    $response = $this->postJson('/api/v1/products', $productData);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Test Product', 'sku' => 'TEST-001']);

    $this->assertDatabaseHas('products', ['sku' => 'TEST-001']);
});

test('can filter products by category', function () {
    $category2 = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $this->category->id]);
    Product::factory()->count(2)->create(['category_id' => $category2->id]);

    $response = $this->getJson("/api/v1/products?category_id={$this->category->id}");

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(3);
});

test('can search products', function () {
    Product::factory()->create(['name' => 'Laptop Dell', 'sku' => 'LAP-001', 'category_id' => $this->category->id]);
    Product::factory()->create(['name' => 'Mouse Logitech', 'sku' => 'MOU-001', 'category_id' => $this->category->id]);

    $response = $this->getJson('/api/v1/products?search=Laptop');

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Laptop Dell']);
});

test('can filter low stock products', function () {
    // Limpiar productos existentes para este test
    Product::query()->delete();
    
    Product::factory()->create(['stock' => 5, 'min_stock' => 10, 'category_id' => $this->category->id]);
    Product::factory()->create(['stock' => 50, 'min_stock' => 10, 'category_id' => $this->category->id]);

    $response = $this->getJson('/api/v1/products?low_stock=1');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
});

test('can update product', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $response = $this->putJson("/api/v1/products/{$product->id}", [
        'name' => 'Updated Product',
        'price' => 149.99,
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Product']);
});

test('can delete product', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id]);

    $response = $this->deleteJson("/api/v1/products/{$product->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('sku must be unique', function () {
    Product::factory()->create(['sku' => 'UNIQUE-001', 'category_id' => $this->category->id]);

    $response = $this->postJson('/api/v1/products', [
        'category_id' => $this->category->id,
        'name' => 'Another Product',
        'sku' => 'UNIQUE-001',
        'price' => 99.99,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sku']);
});

test('price must be positive', function () {
    $response = $this->postJson('/api/v1/products', [
        'category_id' => $this->category->id,
        'name' => 'Test Product',
        'sku' => 'TEST-001',
        'price' => -10,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['price']);
});
