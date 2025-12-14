<?php

use App\Models\Customer;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can list customers', function () {
    Customer::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/customers');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'phone', 'is_active']
            ]
        ]);
});

test('can create customer', function () {
    $customerData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+1234567890',
        'address' => '123 Main St',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'is_active' => true,
    ];

    $response = $this->postJson('/api/v1/customers', $customerData);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'John Doe']);

    $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
});

test('can search customers', function () {
    Customer::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@example.com']);
    Customer::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

    $response = $this->getJson('/api/v1/customers?search=Alice');

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Alice Smith']);
});

test('can search customers by email', function () {
    Customer::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);

    $response = $this->getJson('/api/v1/customers?search=test@example.com');

    $response->assertStatus(200)
        ->assertJsonFragment(['email' => 'test@example.com']);
});

test('can update customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->putJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Updated Name',
        'phone' => '+0987654321',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Name']);
});

test('can delete customer', function () {
    $customer = Customer::factory()->create();

    $response = $this->deleteJson("/api/v1/customers/{$customer->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
});

test('email must be unique', function () {
    Customer::factory()->create(['email' => 'unique@example.com']);

    $response = $this->postJson('/api/v1/customers', [
        'name' => 'Another Customer',
        'email' => 'unique@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('email must be valid format', function () {
    $response = $this->postJson('/api/v1/customers', [
        'name' => 'Test Customer',
        'email' => 'invalid-email',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
