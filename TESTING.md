# Testing

## Configuración de Base de Datos para Tests

Este proyecto utiliza una **base de datos separada para tests** para no afectar los datos de desarrollo.

### Base de Datos de Tests

- **Desarrollo**: `laravel_api` (tu base de datos principal)
- **Testing**: `laravel_api_test` (base de datos exclusiva para tests)

### Crear Base de Datos de Tests

```bash
mysql -u tu_usuario -p -e "CREATE DATABASE IF NOT EXISTS laravel_api_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

O con tu usuario actual:
```bash
mysql -u dbeaver -p'Dbeaver_2025!' -e "CREATE DATABASE IF NOT EXISTS laravel_api_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Ejecutar Tests

```bash
php artisan test
```

Los tests utilizarán automáticamente la base de datos `laravel_api_test` definida en `phpunit.xml` y `.env.testing`.

## Configuración

### phpunit.xml
Define variables de entorno para tests, incluyendo:
```xml
<env name="DB_DATABASE" value="laravel_api_test"/>
```

### .env.testing
Configuración específica para el entorno de testing:
- Base de datos: `laravel_api_test`
- Session driver: `array` (sin persistencia)
- Cache: `array` (en memoria)
- Mail: `array` (no envía emails reales)

## CI/CD (GitHub Actions)

GitHub Actions está configurado para:
1. Crear un servicio MySQL temporal
2. Ejecutar migraciones en la base de datos de tests
3. Ejecutar todos los tests
4. Solo si los tests pasan, proceder con el deploy (opcional)

Ver: `.github/workflows/hostinger-deploy.yml`

## Notas Importantes

- ✅ **Los tests NO afectan tu base de datos de desarrollo** (`laravel_api`)
- ✅ **Usa `RefreshDatabase`** para limpiar la DB entre tests
- ✅ **API pura con tokens Sanctum** - no usa sesiones web
- ⚠️ Si los tests locales fallan con "Connection refused", verifica que MySQL esté corriendo y que la DB `laravel_api_test` exista

## Estructura de Tests

```
tests/
├── Feature/          # Tests de integración
│   ├── Auth/         # Autenticación (login, register, logout, etc.)
│   ├── CategoryTest.php
│   ├── CustomerTest.php
│   ├── ProductTest.php
│   └── SaleTest.php
└── Unit/             # Tests unitarios
    └── ExampleTest.php
```

## Tests de Autenticación

Los tests de autenticación utilizan tokens API (Sanctum):
- Login retorna `{ user, token }`
- Logout elimina el token
- Register crea usuario y retorna 204

No usan sesiones web ya que la API es stateless.
