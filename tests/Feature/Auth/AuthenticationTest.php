<?php

use App\Models\User;

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user']);  // Ya no devuelve token - usa cookie HttpOnly
    
    // Verificar que el usuario está autenticado
    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();
    
    // Autenticar usando sesión (como SPA)
    $this->actingAs($user);

    $response = $this->postJson('/api/logout');

    // Verificar que el logout fue exitoso
    $response->assertNoContent();
    
    // Verificar que el usuario ya no está autenticado
    $this->assertGuest();
});
