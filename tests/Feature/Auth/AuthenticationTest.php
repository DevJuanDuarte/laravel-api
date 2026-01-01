<?php

use App\Models\User;

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['user', 'token']);
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
    
    // Autenticar usando Sanctum (simular token API)
    $token = $user->createToken('test')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/logout');

    // Verificar que el logout fue exitoso
    $response->assertNoContent();
    
    // Verificar que el token fue eliminado
    expect($user->tokens()->count())->toBe(0);
});
