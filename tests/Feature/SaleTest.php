<?php

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->category = Category::factory()->create();
});

test('can list sales', function () {
    Sale::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/api/v1/sales');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'invoice_number', 'total', 'status', 'payment_method']
            ]
        ]);
});

test('can create sale with products', function () {
    $product1 = Product::factory()->create(['category_id' => $this->category->id, 'price' => 50.00, 'stock' => 100]);
    $product2 = Product::factory()->create(['category_id' => $this->category->id, 'price' => 30.00, 'stock' => 50]);

    $saleData = [
        'payment_method' => 'cash',
        'tax' => 10.00,
        'discount' => 5.00,
        'items' => [
            ['product_id' => $product1->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 1],
        ],
    ];

    $response = $this->postJson('/api/v1/sales', $saleData);

    $response->assertStatus(201)
        ->assertJsonPath('data.subtotal', '130.00')
        ->assertJsonPath('data.total', '135.00');

    $this->assertDatabaseHas('sales', ['payment_method' => 'cash']);
    $this->assertDatabaseHas('sale_items', ['product_id' => $product1->id, 'quantity' => 2]);
});

test('sale reduces product stock', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock' => 100]);

    $saleData = [
        'payment_method' => 'cash',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5],
        ],
    ];

    $this->postJson('/api/v1/sales', $saleData);

    $product->refresh();
    expect($product->stock)->toBe(95);
});

test('cannot create sale with insufficient stock', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock' => 2]);

    $saleData = [
        'payment_method' => 'cash',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5],
        ],
    ];

    $response = $this->postJson('/api/v1/sales', $saleData);

    $response->assertStatus(422);
});

test('can create sale with customer', function () {
    $customer = Customer::factory()->create();
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock' => 50]);

    $saleData = [
        'customer_id' => $customer->id,
        'payment_method' => 'card',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ];

    $response = $this->postJson('/api/v1/sales', $saleData);

    $response->assertStatus(201)
        ->assertJsonPath('data.customer.id', $customer->id);
});

test('sale generates invoice number', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock' => 50]);

    $saleData = [
        'payment_method' => 'cash',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ];

    $response = $this->postJson('/api/v1/sales', $saleData);

    $response->assertStatus(201);
    expect($response->json('data.invoice_number'))->toStartWith('INV-');
});

test('can filter sales by status', function () {
    Sale::factory()->create(['user_id' => $this->user->id, 'status' => 'completed']);
    Sale::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

    $response = $this->getJson('/api/v1/sales?status=completed');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
});

test('can filter sales by customer', function () {
    $customer = Customer::factory()->create();
    Sale::factory()->create(['user_id' => $this->user->id, 'customer_id' => $customer->id]);
    Sale::factory()->create(['user_id' => $this->user->id, 'customer_id' => null]);

    $response = $this->getJson("/api/v1/sales?customer_id={$customer->id}");

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(1);
});

test('can update sale status', function () {
    $sale = Sale::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);

    $response = $this->putJson("/api/v1/sales/{$sale->id}", [
        'status' => 'completed',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'completed');
});

test('can delete sale and restore stock', function () {
    $product = Product::factory()->create(['category_id' => $this->category->id, 'stock' => 100]);
    
    $sale = Sale::factory()->create(['user_id' => $this->user->id]);
    $sale->items()->create([
        'product_id' => $product->id,
        'quantity' => 10,
        'unit_price' => 50.00,
        'subtotal' => 500.00,
    ]);

    $product->update(['stock' => 90]);

    $response = $this->deleteJson("/api/v1/sales/{$sale->id}");

    $response->assertStatus(204);
    
    $product->refresh();
    expect($product->stock)->toBe(100);
});

test('sale requires at least one item', function () {
    $response = $this->postJson('/api/v1/sales', [
        'payment_method' => 'cash',
        'items' => [],
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['items']);
});
