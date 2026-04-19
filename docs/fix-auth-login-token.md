# Fix: Login endpoint devolvía respuesta incorrecta en lugar del token

**Fecha:** 2026-04-19  
**Archivos modificados:**
- `app/Http/Controllers/Auth/LoginController.php`
- `routes/auth.php`
- `routes/api.php`
- `routes/web.php`
- `compose.yaml`
- `.env`

---

## Problema

Al ejecutar `POST /api/login` con credenciales válidas, el endpoint devolvía:

```json
{
  "Laravel": "12.39.0",
  "env": "local"
}
```

En lugar de devolver el usuario y el token de autenticación.

## Causa raíz

Dos problemas combinados:

### 1. Rutas de auth registradas bajo el middleware `web`

El archivo `auth.php` se incluía desde `web.php`, lo que causaba que todas las rutas de autenticación usaran el middleware group **web** (sesiones, CSRF). Al tener una sesión activa de un intento previo, el middleware `guest` (`RedirectIfAuthenticated`) detectaba al usuario autenticado por sesión y redirigía la petición a `GET /`, que devuelve la información de Laravel y el entorno.

### 2. LoginController no generaba token de Sanctum

El controlador usaba `$request->session()->regenerate()` (autenticación por sesión) en lugar de generar un token de API con `$user->createToken()`.

## Solución

### Mover rutas de auth al middleware group `api`

Se eliminó el `require auth.php` de `routes/web.php` y se incluyó en `routes/api.php`. Se removió el prefijo `api` redundante y el endpoint de CSRF que ya no es necesario con tokens.

### Generar token de Sanctum en el login

Se actualizó `LoginController@store` para crear un token de Sanctum y devolverlo en la respuesta:

```php
public function store(LoginRequest $request)
{
    $request->authenticate();

    $user = $request->user();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
}
```

### Revocar token en el logout

Se actualizó `LoginController@destroy` para revocar el token actual en lugar de invalidar la sesión:

```php
public function destroy(Request $request): Response
{
    $request->user()->currentAccessToken()->delete();
    return response()->noContent();
}
```

### Configuración de Mailpit para emails en desarrollo

Se agregó el servicio Mailpit al `compose.yaml` para capturar correos localmente (forgot-password, verificación de email, etc.). Dashboard disponible en `http://localhost:8025`.

## Verificación

```bash
# Ver rutas y confirmar middleware api
./vendor/bin/sail artisan route:list --path=api/login -v

# Resultado esperado:
# POST api/login → Auth\LoginController@store
#   ⇂ api
#   ⇂ RedirectIfAuthenticated
```

Respuesta esperada de `POST /api/login`:

```json
{
  "user": {
    "id": 1,
    "name": "Juan Duarte",
    "email": "juan.devcontact@gmail.com",
    ...
  },
  "token": "1|abc123..."
}
```
