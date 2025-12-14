<?php

use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/v1/categories');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description', 'is_active', 'created_at']
            ]
        ]);
});

test('can create category', function () {
    $categoryData = [
        'name' => 'Test Category',
        'description' => 'Test Description',
        'is_active' => true,
    ];

    $response = $this->postJson('/api/v1/categories', $categoryData);

    $response->assertStatus(201)
        ->assertJsonFragment(['name' => 'Test Category']);

    $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
});

test('can show category', function () {
    $category = Category::factory()->create();

    $response = $this->getJson("/api/v1/categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => $category->name]);
});

test('can update category', function () {
    $category = Category::factory()->create();

    $response = $this->putJson("/api/v1/categories/{$category->id}", [
        'name' => 'Updated Category',
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['name' => 'Updated Category']);

    $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
});

test('can delete category', function () {
    $category = Category::factory()->create();

    $response = $this->deleteJson("/api/v1/categories/{$category->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('category name is required', function () {
    $response = $this->postJson('/api/v1/categories', [
        'description' => 'Test',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated users cannot access categories', function () {
    auth()->logout();

    $response = $this->getJson('/api/v1/categories');

    $response->assertStatus(401);
});
