<?php

use App\Models\User;

test('new users can register', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNoContent();
    
    // Verificar que el usuario fue creado
    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
