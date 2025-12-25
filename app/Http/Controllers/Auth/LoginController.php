<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();
        
        // Crear token con fecha de expiración
        $expirationMinutes = (int) config('sanctum.expiration', 1440);
        $expiresAt = now()->addMinutes($expirationMinutes);
        
        $token = $user->createToken('main', ['*'], $expiresAt)->plainTextToken;

        return [
            'user' => new UserResource($user),
            'token' => $token,
        ];
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    { 
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->noContent();
    }
}
