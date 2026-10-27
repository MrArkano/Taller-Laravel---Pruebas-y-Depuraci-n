# Documentación del proyecto — Taller Laravel: Pruebas y Depuración

Documento con todo lo realizado en este repositorio (Laravel 13.26.1 · PHP 8.5.9 · Sail/Docker · MySQL 8 en desarrollo · SQLite en memoria en pruebas).

## 1. Resumen

El repositorio contiene dos módulos sobre la misma instalación de Laravel:

| Módulo | Propósito |
|---|---|
| **Post / Comment** (código entregado por el usuario, estructura conservada) | Demostración y depuración de un problema N+1, notificación por correo y pruebas con SQLite en memoria. |
| **MediCitas** | Aplicación de gestión de citas médicas: web (Blade), API REST (Sanctum), correo, seeders y pruebas. |

## 2. Limpieza de proyectos anteriores

Se eliminó todo lo que pertenecía a proyectos previos (mangas, restaurante/platos, productos, cursos, ventas, Stripe/SendGrid) para evitar errores y conflictos de migraciones:

- **Modelos:** `Manga`, `Category`, `Publisher`, `Order`, `OrderItem`, `Plato`, `Product`, `Producto`, `Sale`, `SaleItem`, `Curso`.
- **Controladores:** Cart, Catalog, Checkout, Curso, Dashboard, Email, Manga, Menu, Payment, Plato, Product, User (y el AuthController antiguo, reemplazado).
- **Servicios y correo:** `CartService`, `OrderService`, `SendGridService`, `TestSendgridMail`.
- **Migraciones, seeders y factories** de esos módulos.
- **Vistas y assets:** `resources/views/{analytics,cart,catalog,checkout,components,customers,dashboard,errors,help,mangas,menu,orders,platos,products,settings}`, `layouts/app.blade.php` y los archivos `public/css|js/nerdvault.*` e imágenes `nerdvault-*`.
- **Configuración:** bloques `sendgrid` y `stripe` de `config/services.php`, exclusión CSRF del webhook de Stripe en `bootstrap/app.php`, variables de `.env.example`.
- **Dependencia:** `stripe/stripe-php` (`composer remove`).
- Se reescribió `README.md` (describía el proyecto de mangas).

## 3. Módulo Post / Comment (código del usuario)

Implementado tal como se entregó: `phpunit.xml` (SQLite `:memory:`), migraciones `posts` y `comments`, modelos `Post`, `Comment`, relación `User::posts()`, factories, `NewCommentNotification`, `PostController`, `CommentController`, `routes/api.php` y `tests/Feature/PostApiTest.php`.

### Errores encontrados y corregidos

| # | Error | Causa | Corrección |
|---|---|---|---|
| 1 | `PostApiTest::test_busca_el_problema_n_mas_1` fallaba (11 consultas) | `PostController@index` consultaba `$post->user` por cada post (N+1) | `Post::with('user')->where(...)`. Resultado: **2 consultas** (verificado). |
| 2 | `NewCommentNotification` no podía renderizarse con correo real | El constructor no recibía el comentario pero `content()` usaba `$this->comment->body` (propiedad inexistente) | El constructor ahora recibe `public Comment $comment`. Se añadió `NewCommentNotificationTest` que renderiza el correo. |
| 3 | Aviso de PSR-4 en `composer dump-autoload` | `app/Models/Posts.php` declaraba la clase `Post` (duplicado de `Post.php`) | Se eliminó `Posts.php`; el modelo vive en `app/Models/Post.php`. |
| 4 | Migración de `users` sin columnas estándar | Quedaba la migración del proyecto de cursos (columna `role`, sin `email_verified_at` ni `remember_token`) que rompía `UserFactory` | Migración de `users` restaurada al esquema estándar de Laravel. |
| 5 | `ExampleTest` fallaba | `/` ahora redirige a `/citas` | La prueba verifica la redirección. |

## 4. Módulo MediCitas

### Datos
- `users.rol` (`paciente` | `medico`) — migración `2026_10_28_000001_add_rol_to_users_table`.
- `citas` — migración `2026_10_28_000002_create_citas_table`: `patient_id`, `doctor_id` (FK a `users`, `restrictOnDelete`), `especialidad`, `fecha_hora`, `estado` (default `pendiente`), `motivo`, timestamps.
- Modelos `Cita` (relaciones `patient()`, `doctor()`, cast de fecha, scope `visiblePara`) y `User` (`citasComoPaciente()`, `citasComoMedico()`, helpers de rol).
- `CitaFactory`, `UserFactory` (estados `paciente()`/`medico()`), `MediCitasSeeder` (idempotente con `updateOrCreate`).

### Seguridad y reglas
- `CitaPolicy`: solo pacientes crean; solo involucrados ven; el médico cambia a cualquier estado, el paciente solo cancela.
- `StoreCitaRequest`: valida médico existente con rol médico, fecha futura, motivo; **ignora** `patient_id` y `estado` del cliente.
- Cada usuario solo ve sus propias citas (web y API).
- `CitaResource` expone solo `id`, `name`, especialidad, fecha, estado y motivo (nunca correo/contraseña/token).
- Errores JSON coherentes (401, 403, 404, 422).

### Web (Blade)
`/login`, `/citas` (listado con eager loading y etiquetas de estado), `/citas/crear` (formulario con `@csrf`, `old()`, errores por campo), `/logout`.

### API REST
`POST /api/login`, `GET /api/citas?estado=`, `POST /api/citas`, `GET /api/citas/{id}`, `PATCH /api/citas/{id}`.

### Correo
`CitaAgendadaMail` al médico asignado. `CitaService` guarda primero la cita y después envía; si el envío falla se registra el error y la cita se conserva (sin duplicados al reintentar).

## 5. Pruebas (resultado real de la última ejecución)

Comando: `./vendor/bin/sail artisan test` → **32 pruebas pasan (201 aserciones), 0 fallan.**

| Clase | Pruebas | Resultado |
|---|---|---|
| `CitasApiTest` (incluye `test_muestra_citas_confirmadas`, `test_agendar_cita_y_envio_correo`, `test_detecta_problema_n_mas_1`) | 19 | PASS |
| `CitasWebTest` | 7 | PASS |
| `PostApiTest` | 3 | PASS |
| `NewCommentNotificationTest` | 1 | PASS |
| `ExampleTest` (Feature y Unit) | 2 | PASS |

**Diagnóstico N+1 (MediCitas):** con `with(['patient','doctor'])` la petición `GET /api/citas` ejecuta 3 consultas con 3 citas y 3 con 15 citas; el acceso perezoso de contraste pasa de 7 a 31 consultas (1 + 2N).

**Base de datos de pruebas:** SQLite `:memory:` + `RefreshDatabase` + `MAIL_MAILER=array` (verificado por una prueba; también se verifica que las claves foráneas funcionan en SQLite).

## 6. Verificación en servidor real
Con el servidor de Sail (consultas `curl` dentro del contenedor): login web → 302 a `/citas`; listado → 200 con 3 citas y etiquetas de estado; `/citas/crear` → 200; token API obtenido; filtro `?estado=confirmada` → 1 cita del paciente; estado inválido → 422; sin token → 401. Seeder ejecutado dos veces: 4 usuarios y 5 citas (sin duplicados).

## 7. Limitaciones y pendientes
- No se pudo verificar la interfaz en un navegador real (el navegador integrado no inició por un error de descarga de Playwright); se verificó por HTTP y pruebas automatizadas.
- `composer` reporta 8 avisos de seguridad en 5 paquetes (`composer audit`); conviene actualizar dependencias.
- El `.env` local contiene una clave real de SendGrid: está en `.gitignore` y **no** se versiona; se recomienda rotarla.
- El historial de git conserva commits de proyectos anteriores (se revisó: no contiene claves reales).

## 8. Cómo reproducir
```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed
php artisan serve
php artisan test
```
Credenciales de demo (solo local, contraseña `password`): `paciente@medicitas.test`, `maria.gomez@medicitas.test`, `laura.mendoza@medicitas.test`, `andres.ruiz@medicitas.test`.
