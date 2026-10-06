# Suplementor API - Laravel

Este es el backend nuevo desarrollado con Laravel 13 y Sanctum. Es un proyecto separado de la aplicación PHP nativa actual: la interfaz web y la API antigua siguen funcionando sin cambios mientras se prepara la actualización de PHP.

## Requisitos

- PHP 8.3 o posterior con `pdo_mysql`, `pdo_sqlite` (para pruebas), `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype` y `curl` habilitados.
- Composer 2.
- MySQL/MariaDB (XAMPP puede usarse como servidor de base de datos).

El XAMPP detectado tiene PHP 8.0.30. No es necesario reemplazarlo para usar Laravel: instala PHP 8.3 aparte para la terminal y Composer, y conserva XAMPP para Apache/MySQL y la aplicación actual. No se deben instalar dependencias ignorando los requisitos de PHP.

## Preparar PHP 8.3 en Windows

1. Descarga PHP 8.3 x64 **Non Thread Safe (NTS)** en formato ZIP desde [Windows PHP Downloads](https://windows.php.net/download/). NTS se usa aquí para ejecutar PHP desde la terminal; Apache/MySQL continúan siendo los de XAMPP.
2. Extrae el ZIP, por ejemplo, en `C:\php83`. No sobrescribas ni reemplaces `C:\xampp\php`.
3. En `C:\php83`, copia `php.ini-production` a `php.ini`.
4. Edita `C:\php83\php.ini`: configura `extension_dir="C:\php83\ext"` y habilita (quita el `;` inicial) las extensiones `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite` y `zip`. Deja `extension_dir` con una sola definición activa.
5. Agrega `C:\php83` al `Path` de Windows desde **Configuración avanzada del sistema → Variables de entorno**. Cierra y vuelve a abrir PowerShell.
6. Verifica que la terminal esté usando la copia nueva, no la de XAMPP:

   ```powershell
   where.exe php
   php -v
   php -m
   ```

   `php -v` debe indicar 8.3 o superior y `php -m` debe incluir, entre otras, `pdo_mysql`, `pdo_sqlite`, `mbstring` y `openssl`.
7. Instala Composer 2 desde [getcomposer.org/download](https://getcomposer.org/download/). Durante la instalación, selecciona `C:\php83\php.exe` como ejecutable de PHP. Abre una ventana nueva de PowerShell y valida:

   ```powershell
   composer --version
   ```

Si `where.exe php` muestra `C:\xampp\php\php.exe` antes de `C:\php83\php.exe`, mueve `C:\php83` por encima de XAMPP en el `Path`, o selecciona explícitamente `C:\php83\php.exe` al ejecutar los comandos PHP de Laravel.

## Instalar y ejecutar Laravel

1. Inicia MySQL desde XAMPP. Conserva el backend anterior en la raíz del proyecto; no copies la carpeta pública de Laravel encima de `C:\xampp\htdocs\suplementor`.
2. En phpMyAdmin, crea una base de datos vacía llamada `suplementor_laravel`, con cotejamiento `utf8mb4_unicode_ci`. No uses la base antigua `suplementor`: las migraciones Laravel deben crear tablas independientes y preservar los datos existentes.
3. Abre PowerShell dentro de `C:\xampp\htdocs\suplementor\laravel-backend`.
4. Instala las dependencias y crea la configuración local:

   ```powershell
   composer install
   Copy-Item .env.example .env
   ```

5. Edita `.env`: configura `DB_DATABASE=suplementor_laravel`, el usuario y contraseña MySQL locales, y `SUPLEMENTOR_ADMIN_EMAIL` y `SUPLEMENTOR_ADMIN_PASSWORD` con credenciales nuevas. No compartas ni subas este archivo.
6. Ejecuta:

   ```powershell
   php artisan key:generate
   php artisan migrate --seed
   php artisan route:list --path=api
   php artisan test
   php artisan serve
   ```

7. La API Laravel queda disponible en `http://127.0.0.1:8000/api`. La interfaz antigua permanece en `http://localhost/suplementor/index.html`; todavía no está conectada al nuevo endpoint.

## Rutas principales

| Método | Ruta | Acceso |
|---|---|---|
| `POST` | `/api/auth/login` | Público, límite de 5 intentos por minuto |
| `POST` | `/api/auth/logout` | Token bearer válido |
| `GET` | `/api/auth/me` | Token bearer válido |
| `GET` | `/api/productos` | Token bearer válido |
| `GET` | `/api/productos/{id}` | Token bearer válido |
| `POST` | `/api/productos` | Administrador |
| `PUT` | `/api/productos/{id}` | Administrador |
| `DELETE` | `/api/productos/{id}` | Administrador; rechaza productos que aparecen en ventas |
| `PUT` | `/api/inventario/{id}` | Administrador |
| `GET` | `/api/usuarios` | Administrador |
| `POST` | `/api/usuarios` | Administrador |
| `GET` | `/api/ventas` | Administrador |
| `POST` | `/api/ventas` | Token bearer válido |

### Ejemplo de autenticación

```http
POST http://127.0.0.1:8000/api/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "correo": "admin@example.com",
  "password": "la-clave-configurada-localmente"
}
```

La respuesta contiene un token Sanctum. En las solicitudes protegidas se envía:

```http
Authorization: Bearer TOKEN_RECIBIDO
Accept: application/json
```

## Persistencia y compatibilidad de datos

Las migraciones Laravel describen las tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`, además de la tabla de tokens Sanctum administrada por el paquete. Se ejecutan sobre la base vacía `suplementor_laravel`. No modifican ni copian los datos de la base antigua. La migración de información existente debe hacerse como una tarea separada, con respaldo y comprobación de claves y contraseñas.

## Pruebas

Las pruebas de `tests/Feature/ApiTest.php` comprueban inicio de sesión, acceso protegido, permisos de administrador y registro de ventas con descuento del stock. El proyecto no se pudo instalar ni ejecutar en el entorno actual porque falta Composer y el PHP disponible es 8.0.30. Ejecútalas luego de actualizar PHP e instalar las dependencias.
