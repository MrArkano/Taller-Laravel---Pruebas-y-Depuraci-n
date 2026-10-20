# Taller: Consumo e Integración de APIs en Laravel

![Laravel](https://img.shields.io/badge/Laravel-11%20%2F%2012-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Stripe](https://img.shields.io/badge/Stripe-API%20v3-635BFF?style=for-the-badge&logo=stripe&logoColor=white)
![SendGrid](https://img.shields.io/badge/SendGrid-REST%20v3-0099E5?style=for-the-badge&logo=twilio&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)

Proyecto integral desarrollado como taller práctico de **Arquitectura de Software y Consumo de APIs Externas** en el ecosistema **Laravel**. Implementa buenas prácticas de desarrollo empresarial: **Patrón MVC desacoplado con Capa de Servicios (Service Layer)**, transacciones atómicas en base de datos, persistencia en sesión, webhooks seguros y una interfaz de usuario responsiva en **Dark Mode** compilada con **Vite y Tailwind CSS**.

---

## Tabla de Contenidos
1. [Módulos y APIs del Proyecto](#-módulos-y-apis-del-proyecto)
   - [1. Consumo de API REST v3 de SendGrid](#1-consumo-de-api-rest-v3-de-sendgrid)
   - [2. Pasarela de Pagos con Stripe API & Elements](#2-pasarela-de-pagos-con-stripe-api--elements)
   - [3. Tienda Virtual E-Commerce de Mangas (MANGA・VAULT)](#3-tienda-virtual-e-commerce-de-mangas-mangavault)
2. [Requisitos Previos](#-requisitos-previos)
3. [Instalación y Puesta en Marcha](#-instalación-y-puesta-en-marcha)
4. [Variables de Entorno (.env)](#-variables-de-entorno-env)
5. [Rutas y Endpoints del Sistema](#-rutas-y-endpoints-del-sistema)
6. [Guía de Pruebas](#-guía-de-pruebas)
7. [Estructura Arquitectónica](#-estructura-arquitectónica)

---

## 🚀 Módulos y APIs del Proyecto

### 1. Consumo de API REST v3 de SendGrid
Implementación de un cliente HTTP con Laravel (`Illuminate\Support\Facades\Http`) para el envío de correos electrónicos transaccionales a través de la API REST oficial de Twilio SendGrid.
* **Capa de Servicio:** [`app/Services/SendGridService.php`](app/Services/SendGridService.php) encapsula la autenticación Bearer Token y el armado del payload JSON requerido por SendGrid (`/v3/mail/send`).
* **Controlador y Vistas:** [`EmailController`](app/Http/Controllers/EmailController.php) valida los formularios y gestiona respuestas amigables en [`envio_email.blade.php`](resources/views/envio_email.blade.php).
* **Mailable SMTP:** Soporte dual para envíos por cola o SMTP tradicional mediante el Mailable [`app/Mail/TestSendgridMail.php`](app/Mail/TestSendgridMail.php).

### 2. Pasarela de Pagos con Stripe API & Elements
Integración de pagos seguros en línea utilizando el SDK oficial `stripe/stripe-php` y la librería frontend `Stripe.js v3`.
* **PaymentIntents Dinámicos:** Generación de intenciones de pago calculadas en céntimos para evitar fraudes en el cliente.
* **Gestión de Clientes (`Customer`):** Creación y vinculación automática del nombre y correo del comprador en Stripe para que figuren directamente en el Dashboard.
* **Stripe Elements:** Formulario de tarjeta cifrado con tema oscuro (`night`) montado de forma segura sin que datos sensibles toquen el servidor backend.
* **Webhooks Seguros:** Endpoint [`/stripe/webhook`](app/Http/Controllers/PaymentController.php) con verificación de firmas criptográficas (`Stripe-Signature`) para procesar confirmaciones asíncronas de pago, excluido de la protección CSRF en [`bootstrap/app.php`](bootstrap/app.php).

### 3. Tienda Virtual E-Commerce de Mangas (MANGA・VAULT)
Un e-commerce moderno y responsivo para coleccionistas de cómics y mangas japoneses en español:
* **Catálogo y Filtros:** Búsqueda en vivo y filtrado por demografías (*Shonen, Seinen, Shojo, Dark Fantasy*) y sellos editoriales (*Panini, Ivrea, Norma, Milky Way*).
* **Ficha Técnica Detallada:** Información completa de cada tomo (ISBN-13, número de páginas, formato Tankobon/Kanzenban, sinopsis y títulos relacionados).
* **Capa de Servicios para el Carrito (`CartService`):** Persistencia temporal en sesión de Laravel, cálculo de subtotales, IVA (10%), barra de progreso interactiva para **Envío Gratis** a partir de $50 USD.
* **Servicio Transaccional de Órdenes (`OrderService`):** Ejecución atómica (`DB::transaction`) con bloqueo de filas (`lockForUpdate`), descuento de inventario en tiempo real y generación de comprobante con código único (`MNGA-YYYYMMDD-XXXXX`).
* **Integración Directa con Stripe:** El Checkout evalúa el total del pedido y solicita los datos de tarjeta cifrados mediante Stripe Elements antes de asentar la orden.

---

## 🛠 Requisitos Previos

* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (o motor de Docker en Linux/WSL2).
* [Git](https://git-scm.com/).
* Claves de prueba de [Stripe](https://stripe.com/) (`STRIPE_KEY` y `STRIPE_SECRET`).
* Clave de API de [SendGrid](https://sendgrid.com/) (`SENDGRID_API_KEY`).

---

## 💻 Instalación y Puesta en Marcha

### 1. Clonar el Repositorio
```bash
git clone https://github.com/MrArkano/TallerConsumo-de-APIs.git
cd TallerConsumo-de-APIs
```

### 2. Configurar el Archivo de Entorno
Copia la plantilla de entorno:
```bash
cp .env.example .env
```
Asegúrate de configurar tus credenciales de SendGrid y Stripe en el archivo `.env` (ver sección siguiente).

### 3. Levantar los Contenedores con Laravel Sail
Inicia los servicios en segundo plano (PHP 8.5, MySQL 8.4, Redis, Mailpit):
```bash
./vendor/bin/sail up -d
# O si usas Docker directamente:
docker compose up -d
```

### 4. Generar la Clave de Aplicación y Ejecutar Migraciones
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan db:seed --class=MangaDatabaseSeeder
```
*Esto creará la base de datos y la poblará con 10 títulos icónicos de prueba (Berserk, One Piece, Chainsaw Man, Jujutsu Kaisen, etc.).*

### 5. Compilar los Assets del Frontend (Vite + Tailwind CSS)
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

## ⚙️ Variables de Entorno (.env)

Configura las siguientes variables en tu archivo `.env`:

```env
APP_NAME="MANGA・VAULT"
APP_URL=http://localhost

# Base de Datos (Sail)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ludoteca
DB_USERNAME=sail
DB_PASSWORD=password

# Twilio SendGrid (API REST v3 y SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=TU_SENDGRID_API_KEY
MAIL_FROM_ADDRESS="tu_correo_verificado@ejemplo.com"
MAIL_FROM_NAME="${APP_NAME}"
SENDGRID_API_KEY=TU_SENDGRID_API_KEY

# Stripe (Modo Test)
STRIPE_KEY=pk_test_tu_clave_publica
STRIPE_SECRET=sk_test_tu_clave_secreta
STRIPE_WEBHOOK_SECRET=whsec_tu_webhook_secret
```

---

## 🌐 Rutas y Endpoints del Sistema

### E-Commerce de Mangas & Stripe Checkout
| Método | URL | Controlador / Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/` ó `/mangas` | `MangaController@index` | Catálogo de mangas con filtros y orden |
| `GET` | `/mangas/{slug}` | `MangaController@show` | Vista de detalle de tomo y ficha técnica |
| `GET` | `/cart` | `CartController@index` | Vista interactiva del carrito de compras |
| `POST` | `/cart/add/{manga}` | `CartController@add` | Añadir manga al carrito (control de stock) |
| `PUT` | `/cart/update/{manga}`| `CartController@update` | Modificar cantidad de un ítem |
| `DELETE`| `/cart/remove/{manga}`| `CartController@remove` | Eliminar tomo del carrito |
| `POST` | `/cart/clear` | `CartController@clear` | Vaciar todo el carrito |
| `GET` | `/order-checkout` | `CheckoutController@index` | Checkout con Stripe Elements integrado |
| `POST` | `/order-checkout/process`| `CheckoutController@process`| Procesar compra con tarjeta y generar orden |
| `GET` | `/order-checkout/success/{id}`| `CheckoutController@success`| Comprobante oficial de compra |

### Módulos de Pruebas de APIs
| Método | URL | Controlador / Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/email` | `EmailController@index` | Formulario de envío de correos vía API SendGrid |
| `POST` | `/email` | `EmailController@send` | Enviar correo mediante `SendGridService` |
| `GET` | `/checkout` | `PaymentController@showCheckout` | Formulario de prueba directa con Stripe Elements |
| `POST` | `/create-payment-intent` | `PaymentController@createPaymentIntent` | API endpoint para crear PaymentIntent |
| `POST` | `/stripe/webhook` | `PaymentController@handleWebhook` | Receptor del Webhook de Stripe (CSRF exempt) |

---

## 🧪 Guía de Pruebas

### 1. Probar el Catálogo y Compra con Tarjeta en Stripe
1. Ingresa a **[`http://localhost/mangas`](http://localhost/mangas)**.
2. Filtra por categoría (ej. *Seinen* o *Shonen*) y pulsa en **"Añadir al Carrito"**.
3. En el carrito (**[`http://localhost/cart`](http://localhost/cart)**), revisa el subtotal e impuestos, y haz clic en **"Proceder al Checkout"**.
4. En la pantalla de checkout (**[`http://localhost/order-checkout`](http://localhost/order-checkout)**), completa tus datos de envío e ingresa la tarjeta de pruebas de Stripe:
   * **Tarjeta:** `4242 4242 4242 4242`
   * **Vencimiento:** Cualquier fecha futura (ej. `12/28`)
   * **CVC:** `123`
   * **Código Postal:** `680001`
5. Haz clic en **"Pagar con Stripe"**. Verás cómo el pago se aprueba y te redirige a la pantalla con tu número de orden generado y el stock descontado.

### 2. Probar el Envío de Correo con SendGrid
1. Ingresa a **[`http://localhost/email`](http://localhost/email)**.
2. Ingresa un correo destinatario, asunto y mensaje.
3. Presiona **"Enviar Correo"**. La petición viajará vía REST v3 a la API de SendGrid devolviendo el mensaje de confirmación en verde.
4. Para probar mediante Tinker:
   ```bash
   ./vendor/bin/sail artisan tinker
   ```
   ```php
   Mail::to('destino@ejemplo.com')->send(new App\Mail\TestSendgridMail());
   ```

### 3. Probar el Webhook de Stripe
Puedes simular eventos de webhook localmente utilizando la CLI de Stripe o con curl:
```bash
curl -i -X POST http://localhost/stripe/webhook
```
*(Responderá con HTTP 400 Bad Request por ausencia de firma de seguridad, demostrando que la ruta no es bloqueada por el middleware de CSRF y llega al controlador).*

---

## 🏛 Estructura Arquitectónica

```
app/
├── Http/
│   └── Controllers/
│       ├── CartController.php          # Control del carrito en sesión
│       ├── CheckoutController.php      # Checkout de tienda con Stripe Elements
│       ├── EmailController.php         # Interfaz para consumo de SendGrid API
│       ├── MangaController.php         # Catálogo de mangas y ficha de tomos
│       └── PaymentController.php       # Controlador de prueba y Webhook Stripe
├── Mail/
│   └── TestSendgridMail.php            # Mailable SMTP para SendGrid
├── Models/
│   ├── Category.php                    # Demografías de manga (Shonen, Seinen...)
│   ├── Manga.php                       # Modelo principal de tomos
│   ├── Order.php                       # Órdenes de compra generadas
│   ├── OrderItem.php                   # Ítems asociados a cada orden
│   └── Publisher.php                   # Editoriales (Panini, Ivrea, Norma...)
└── Services/
    ├── CartService.php                 # Capa de servicio para lógica de carrito
    ├── OrderService.php                # Transacciones atómicas de compra y stock
    └── SendGridService.php             # Cliente REST para la API v3 de SendGrid
resources/
└── views/
    ├── cart/
    │   └── index.blade.php             # Vista de carrito y barra de envío gratis
    ├── checkout/
    │   ├── index.blade.php             # Formulario de checkout con Stripe Elements
    │   └── success.blade.php           # Pantalla de confirmación de pedido
    ├── components/
    │   └── manga-card.blade.php        # Componente reutilizable de tarjeta de manga
    ├── layouts/
    │   └── app.blade.php               # Layout Dark Mode (Vite + Tailwind CSS)
    ├── mangas/
    │   ├── index.blade.php             # Vista del catálogo con Hero y filtros
    │   └── show.blade.php              # Ficha técnica detallada del manga
    └── envio_email.blade.php           # Formulario de envío API SendGrid
```

---

## 📄 Licencia

Este proyecto fue desarrollado con fines educativos y de demostración arquitectónica bajo la licencia [MIT](LICENSE).
