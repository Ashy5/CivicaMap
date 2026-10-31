<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

## API REST v1

La API devuelve siempre JSON con la estructura `success`, `message` y `data`.
Los tokens Bearer se emiten con Laravel Sanctum y vencen en 60 minutos por
defecto (`SANCTUM_EXPIRATION` permite ajustar los minutos). Envíe el token en
`Authorization: Bearer <token>`. Las validaciones responden `422`; una sesión
faltante o inválida responde `401`; falta de permisos `403`; recurso inexistente
`404`; las creaciones responden `201`.

### Cliente y ciudadano

| Método | Ruta | Acceso | Descripción |
|---|---|---|---|
| POST | `/api/v1/cliente/registro` | Público | Registrar ciudadano |
| POST | `/api/v1/cliente/login` | Público | Iniciar sesión y obtener token |
| GET | `/api/v1/cliente/perfil` | Bearer ciudadano | Consultar perfil propio |
| POST | `/api/v1/cliente/logout` | Bearer ciudadano | Revocar token actual |
| GET/POST | `/api/v1/cliente/incidencias` | Bearer ciudadano | Listar o crear incidencias propias |
| GET/PUT/PATCH/DELETE | `/api/v1/cliente/incidencias/{id}` | Bearer ciudadano | Consultar, actualizar o eliminar incidencia propia |

El registro requiere `nombre`, `correo`, `password` y `password_confirmation`.
El login requiere `correo` y `password`. Para crear incidencias se requiere
`id_categoria`, `descripcion` (10–2000 caracteres), `latitud_aprox` y
`longitud_aprox`; `anonimizado` es opcional. El propietario se toma siempre
del token y no se acepta desde el cuerpo.

### Administración

| Método | Ruta | Permiso mínimo | Descripción |
|---|---|---|---|
| POST | `/api/v1/admin/login` | Público | Iniciar sesión administrativa y obtener token |
| GET | `/api/v1/admin/perfil` | Nivel 1 | Perfil del administrador autenticado |
| GET | `/api/v1/admin/incidencias` y `/{id}` | Nivel 1 | Consultar incidencias |
| POST | `/api/v1/admin/incidencias` | Nivel 2 | Crear incidencia |
| PUT/PATCH | `/api/v1/admin/incidencias/{id}` | Nivel 2 | Actualizar incidencia |
| DELETE | `/api/v1/admin/incidencias/{id}` | Nivel 3 | Eliminar incidencia |
| GET/POST/PUT/PATCH/DELETE | `/api/v1/admin/categorias[/{id}]` | Nivel 3 | Consultar y gestionar categorías |
| GET/POST/PUT/PATCH/DELETE | `/api/v1/admin/usuarios[/{id}]` | Nivel 3 | Consultar y gestionar usuarios ciudadanos |

El login administrativo requiere `username` y `password`. Los niveles se
aplican a los administradores existentes: 1 consulta, 2 crea/edita incidencias
y 3 administra incidencias, categorías y usuarios. DELETE en categorías y
usuarios desactiva el registro para conservar incidencias y relaciones
históricas; DELETE de una incidencia la elimina.

### Roles y permisos dinámicos

El proyecto ya tenía las tablas `roles` y `usuario_rol`; la API las reutiliza
para evitar duplicar roles. Una nueva migración añade `permissions`,
`role_permission` y `administrador_rol`. Los permisos se guardan por nombre y
se incluyen al crear el rol:

| Método | Ruta | Acceso |
|---|---|---|
| GET/POST | `/api/v1/admin/roles` | Administrador nivel 3 o rol Admin/SuperAdmin |
| POST | `/api/v1/admin/usuarios/{id}/roles` | Administrador autorizado; sincroniza el arreglo `role_ids` |
| DELETE | `/api/v1/admin/usuarios/{id}/roles/{roleId}` | Administrador autorizado; remueve el rol |
| POST | `/api/v1/admin/administradores/{id}/roles` | SuperAdmin/nivel 3; sincroniza roles de una cuenta administrativa |
| DELETE | `/api/v1/admin/administradores/{id}/roles/{roleId}` | SuperAdmin/nivel 3; remueve un rol administrativo |
| GET | `/api/v1/cliente/categorias` | Ciudadano con rol Seguridad, Admin o SuperAdmin |

Ejemplo para crear un rol:

```json
{
  "nombre": "Gestor Operativo",
  "descripcion": "Seguimiento de incidencias",
  "permissions": ["incidencias.viewAny", "categorias.viewAny"]
}
```

Los administradores existentes de nivel 3 pueden gestionar roles como acceso
de bootstrap. La relación `administrador_rol` permite asignar roles dinámicos
a cuentas administrativas existentes; los ciudadanos reciben roles por
`usuario_rol`. La Policy `IncidenciaPolicy` permite al rol Seguridad consultar
todas las incidencias del mapa, mientras que los demás ciudadanos siguen
limitados a las propias. `CategoriaPolicy` limita el catálogo completo a
Seguridad, Admin y SuperAdmin. Los permisos de escritura continúan controlados
por las políticas administrativas existentes.

## Evidencias A3.4: entorno de demostración

Ejecute las migraciones y cargue los datos de demostración **solo en una base
local/de pruebas**:

```powershell
php artisan migrate
php artisan db:seed --class=Database\Seeders\ApiTestSeeder
```

El seeder es repetible y crea dos cuentas administrativas (`administradores`),
dos cuentas ciudadanas (`usuarios`), tres categorías y cinco incidencias.
No utilice estas contraseñas de demostración en producción.

| Tipo de cuenta | Usuario/correo | Contraseña |
|---|---|---|
| SuperAdmin | `superadmin` (campo `username`) | `CivicaSuper2026!` |
| Administrador/Gestor | `gestor` (campo `username`) | `CivicaGestor2026!` |
| Cliente/Ciudadano | `ciudadano@civicamap.test` | `CivicaCiudadano2026!` |
| Seguridad | `seguridad@civicamap.test` | `CivicaSeguridad2026!` |

Para obtener el token ciudadano, envíe `POST /api/v1/cliente/login` con
`{"correo":"ciudadano@civicamap.test","password":"CivicaCiudadano2026!"}`.
El endpoint administrativo usa `POST /api/v1/admin/login` con
`{"username":"superadmin","password":"CivicaSuper2026!"}`. Ambos devuelven el
token en `data.token`.

### Solicitudes de Postman

Configure `http://127.0.0.1:8000` como URL base, use `Accept: application/json`
en todas las solicitudes y en las privadas agregue
`Authorization: Bearer {{token}}`. En Postman puede guardar el token recibido
con un script de Tests: `pm.environment.set("token", pm.response.json().data.token)`.

| Caso | Método y ruta | Autenticación | Body |
|---|---|---|---|
| a) Sin autenticar, respuesta 401 | `GET /api/v1/cliente/perfil` | Sin Bearer | Sin body |
| b) Login ciudadano, respuesta 200 | `POST /api/v1/cliente/login` | Pública | `{"correo":"ciudadano@civicamap.test","password":"CivicaCiudadano2026!"}` |
| c) Crear incidencia, respuesta 201 | `POST /api/v1/cliente/incidencias` | Token ciudadano | Ver JSON de creación abajo |
| d) Ciudadano en ruta admin, respuesta 403 | `GET /api/v1/admin/incidencias` | Token ciudadano | Sin body |
| e) Cambiar estado, respuesta 200 | `PATCH /api/v1/cliente/incidencias/1/estado` | Token Seguridad | `{"estado":"En Proceso"}` |

Para crear incidencia, use un `id_categoria` existente. Los IDs varían según la
base; en una base nueva con el seeder suelen ser 1, 2 y 3. Compruebe los IDs
con `php artisan tinker --execute="dump(App\Models\CategoriaReporte::all(['id_categoria', 'nombre'])->toArray());"`.

```json
{
  "id_categoria": 1,
  "descripcion": "Bache de prueba para la evidencia de A3.4.",
  "latitud_aprox": 20.6597,
  "longitud_aprox": -103.3496,
  "anonimizado": false
}
```

El método e) está limitado al rol `Seguridad` o al permiso
`incidencias.updateStatus`. Para tomar la misma evidencia con el administrador
Gestor, puede usar `PATCH /api/v1/admin/incidencias/1` y el body
`{"estado":"En Proceso"}` con su token. Ambos ejemplos requieren un ID real de
incidencia; el seeder crea cinco.

Ejemplo de autenticación:

```http
POST /api/v1/cliente/login
Accept: application/json
Content-Type: application/json

{"correo":"ciudadano@example.com","password":"contraseña"}
```

La respuesta incluye `data.token` y `data.token_type` (`Bearer`). Use ese token
en las rutas privadas; las contraseñas no se incluyen en las respuestas. Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
