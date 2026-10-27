# MediCitas — Gestión de citas médicas

MediCitas es una aplicación de gestión de citas médicas para una clínica, desarrollada con **Laravel 13**, **Blade**, **Eloquent** y una **API REST** (JSON, autenticada con Laravel Sanctum). Forma parte de un taller de pruebas y depuración de software.

## Requisitos previos

| Herramienta | Versión usada/verificada |
|---|---|
| PHP | 8.5.9 (extensiones `pdo_sqlite` y `pdo_mysql`) |
| Composer | 2.x |
| Laravel | 13.26.1 |
| Base de datos de desarrollo | MySQL 8 (Sail) o SQLite |
| Base de datos de pruebas | SQLite en memoria (`:memory:`) |
| Docker + WSL2 (opcional) | Para usar Laravel Sail |

## Instalación

```bash
composer install
cp .env.example .env        # Windows (PowerShell): Copy-Item .env.example .env
php artisan key:generate
```

> Con Sail (Docker): anteponer `./vendor/bin/sail` a cada comando `php artisan ...` (p. ej. `./vendor/bin/sail artisan migrate`) y levantar los contenedores con `./vendor/bin/sail up -d`.

## Base de datos

Configure `DB_*` en `.env` (MySQL con Sail, o `DB_CONNECTION=sqlite` con un archivo `database/database.sqlite`). Luego:

```bash
php artisan migrate
```

> No use `migrate:fresh` sobre una base con datos que desee conservar.

Se crean `users` (con la columna `rol`: `paciente` | `medico`) y `citas` (claves foráneas `patient_id` y `doctor_id` hacia `users.id`).

## Seeders (datos de demostración, ficticios)

```bash
php artisan db:seed                       # ejecuta DatabaseSeeder -> MediCitasSeeder
php artisan db:seed --class=MediCitasSeeder
```

El seeder es **idempotente** (`updateOrCreate`): puede ejecutarse varias veces sin duplicar datos.

Credenciales **solo para el entorno local de demostración** (contraseña `password`):

| Rol | Correo |
|---|---|
| Paciente | `paciente@medicitas.test` |
| Paciente | `maria.gomez@medicitas.test` |
| Médico (Cardiología) | `laura.mendoza@medicitas.test` |
| Médico (Pediatría) | `andres.ruiz@medicitas.test` |

## Ejecución de la aplicación

```bash
php artisan serve        # http://127.0.0.1:8000
```

(Con Sail: `http://localhost`.)

- Inicio de sesión: `/login`
- Listado de citas: `/citas`
- Formulario de agendamiento (solo pacientes): `/citas/crear`

Cada usuario ve únicamente sus propias citas (como paciente o como médico). El paciente agenda siempre a su nombre y el estado inicial (`pendiente`) lo fija el backend.

## API REST

Todas las rutas (excepto `/api/login`) exigen `Authorization: Bearer <token>` (Sanctum) y `Accept: application/json`. Los errores se devuelven en JSON.

| Método | Ruta | Descripción | Códigos |
|---|---|---|---|
| POST | `/api/login` | Emite un token Bearer (`email`, `password`) | 200, 422 |
| GET | `/api/citas?estado=` | Lista las citas del usuario; filtro opcional `pendiente\|confirmada\|cancelada` | 200, 401, 422 |
| POST | `/api/citas` | Crea una cita (solo pacientes) y notifica al médico | 201, 401, 403, 422 |
| GET | `/api/citas/{id}` | Detalle de una cita (solo paciente o médico involucrados) | 200, 401, 403, 404 |
| PATCH | `/api/citas/{id}` | Cambia el estado. El médico asignado: cualquiera; el paciente: solo `cancelada` | 200, 401, 403, 404, 422 |

Cuerpo de `POST /api/citas`: `doctor_id`, `especialidad`, `fecha_hora` (futura), `motivo`. Los campos `patient_id` y `estado` se **ignoran**: el paciente es el usuario autenticado y el estado inicial es `pendiente`.

Estructura real de respuesta (`CitaResource`; no expone correos, contraseñas ni tokens):

```json
{ "data": [ { "id": 1,
  "patient": { "id": 1, "name": "Carlos Paciente" },
  "doctor": { "id": 3, "name": "Dra. Laura Mendoza" },
  "especialidad": "Cardiología", "fecha_hora": "2026-11-05T10:30:00",
  "estado": "confirmada", "motivo": "Consulta de seguimiento" } ] }
```

Ejemplos:

```bash
# 1) Obtener token
curl -X POST http://localhost/api/login -H "Accept: application/json" \
  -d "email=paciente@medicitas.test" -d "password=password"

# 2) Filtrar citas confirmadas
curl "http://localhost/api/citas?estado=confirmada" \
  -H "Accept: application/json" -H "Authorization: Bearer <TOKEN>"

# 3) Crear una cita
curl -X POST http://localhost/api/citas -H "Accept: application/json" \
  -H "Authorization: Bearer <TOKEN>" \
  -d "doctor_id=3" -d "especialidad=Cardiología" \
  -d "fecha_hora=2026-12-01 10:30:00" -d "motivo=Control anual"
```

### Correo y manejo de fallos

`CitaService::agendar()` guarda primero la cita y luego envía `App\Mail\CitaAgendadaMail` al correo del médico (paciente, médico, especialidad, fecha/hora, motivo y estado inicial). Si el envío falla, el error se registra (`report()`) y **la cita se conserva**, por lo que un reintento del cliente no crea duplicados.

## Ejecución de las pruebas

```bash
php artisan test                          # suite completa
php artisan test --filter=CitasApiTest    # solo la API de citas
php artisan test --filter=CitasWebTest    # solo la interfaz web
```

Las pruebas usan **SQLite en memoria** (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` en `phpunit.xml`), `RefreshDatabase` y `MAIL_MAILER=array` con `Mail::fake()`, por lo que no tocan la base de desarrollo ni envían correos reales. Una prueba (`test_pruebas_usan_sqlite_en_memoria_y_correo_array`) lo verifica, y otra comprueba que las claves foráneas se aplican en SQLite.

> El repositorio incluye también el módulo Post/Comment (`PostApiTest`); su problema N+1 ya fue corregido con `Post::with('user')`. Consulte `DOCUMENTACION.md` para el detalle completo.

## Resultados esperados (pruebas obligatorias)

| Prueba | Qué verifica |
|---|---|
| `test_muestra_citas_confirmadas` | `GET /api/citas?estado=confirmada` → 200, estructura JSON correcta, solo citas confirmadas del usuario (excluye pendientes, canceladas y ajenas), con paciente y médico sin campos sensibles. |
| `test_agendar_cita_y_envio_correo` | Paciente autenticado hace `POST /api/citas` → 201, cita persistida con estado `pendiente`, `patient_id`/`doctor_id` correctos y correo enviado exactamente al médico con los datos de la cita. Complementada con pruebas de: sin autenticación (401), datos inválidos (422), suplantación de paciente y médico inexistente. |
| `test_detecta_problema_n_mas_1` | Mide solo las consultas de `GET /api/citas` con 3 y con 15 citas: con `with(['patient','doctor'])` son constantes (3); como contraste, el acceso perezoso crece (1 + 2N), lo que valida el criterio. |

Última ejecución real de `php artisan test`: 32 pruebas pasaron (201 aserciones). Detalle en `DOCUMENTACION.md`.

## Repositorio Git público

```bash
git add .
git commit -m "MediCitas: citas médicas con API, pruebas y README"
git remote add origin <URL-DE-SU-REPOSITORIO>
git push -u origin main
```

`.env` ya está en `.gitignore`; no suba credenciales, tokens ni claves. Si alguna credencial real (p. ej. de un servicio de correo) estuvo alguna vez en `.env` o en el historial, **rótela** antes de publicar.
