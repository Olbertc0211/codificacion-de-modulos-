# Suplementor API - Laravel

Este es el backend nuevo desarrollado con Laravel 13 y Sanctum. Es un proyecto separado de la aplicación PHP nativa actual: la interfaz web y la API antigua siguen funcionando sin cambios mientras se prepara la actualización de PHP.

## Requisitos

- PHP 8.3 o posterior con `pdo_mysql`, `pdo_sqlite` (para pruebas), `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `dom`, `ctype`, `curl` y `zip` habilitados.
- Composer 2.
- MySQL/MariaDB (XAMPP puede usarse como servidor de base de datos).
- MySQL Workbench para conectarse al servidor y crear la base (no se requiere phpMyAdmin).

El XAMPP contiene PHP 8.0.30, que no cumple los requisitos de Laravel. En el equipo de desarrollo se instaló PHP 8.5 aparte en `C:\php85` y se conservó XAMPP para Apache/MySQL. MySQL Workbench es el cliente gráfico; el servicio MySQL de XAMPP debe estar iniciado. No se deben instalar dependencias ignorando los requisitos de PHP.

## Preparar PHP en Windows

1. Descarga PHP 8.3 o posterior, x64 **Non Thread Safe (NTS)**, en formato ZIP desde [Windows PHP Downloads](https://windows.php.net/download/). NTS se usa para ejecutar PHP desde la terminal; Apache/MySQL continúan siendo los de XAMPP.
2. Extrae el ZIP, por ejemplo en `C:\php85`. No sobrescribas ni reemplaces `C:\xampp\php`.
3. En esa carpeta, copia `php.ini-production` a `php.ini`.
4. Edita `php.ini`: configura `extension_dir="C:\php85\ext"` y habilita (quita el `;` inicial) las extensiones `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `pdo_sqlite` y `zip`. Deja `extension_dir` con una sola definición activa.
5. Agrega `C:\php85` al `Path` de Windows desde **Configuración avanzada del sistema → Variables de entorno**. Cierra y vuelve a abrir PowerShell.
6. Verifica que la terminal esté usando la copia nueva, no la de XAMPP:

   ```powershell
   where.exe php
   php -v
   php -m
   ```

   `php -v` debe indicar 8.3 o superior y `php -m` debe incluir `pdo`, `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `dom` y `zip`.
7. Instala Composer 2 desde [getcomposer.org/download](https://getcomposer.org/download/). Durante la instalación, selecciona `C:\php85\php.exe` como ejecutable de PHP. Abre una ventana nueva de PowerShell y valida:

   ```powershell
   composer --version
   ```

Si `where.exe php` muestra `C:\xampp\php\php.exe` antes de `C:\php85\php.exe`, mueve `C:\php85` por encima de XAMPP en el `Path`, o selecciona explícitamente `C:\php85\php.exe` al ejecutar los comandos PHP de Laravel.

## Instalar y ejecutar Laravel

1. Inicia MySQL desde XAMPP. Conserva el backend anterior en la raíz del proyecto; no copies la carpeta pública de Laravel encima de `C:\xampp\htdocs\suplementor`.
2. Inicia el servicio MySQL desde XAMPP. En MySQL Workbench crea una conexión al host `127.0.0.1`, puerto `3306`, usuario `root` y la contraseña local configurada en XAMPP. Abre `database/create-suplementor-laravel.sql` y pulsa el botón del rayo para ejecutarlo. Esto crea la base vacía `suplementor_laravel`; no uses la base antigua `suplementor`, que se conserva sin cambios. El instalador fija `DB_DATABASE=suplementor_laravel`.
3. Abre PowerShell dentro de `C:\xampp\htdocs\suplementor\laravel-backend`.
4. Ejecuta el instalador guiado:

   ```powershell
   .\scripts\Install-Suplementor.ps1
   ```

   Si PowerShell bloquea scripts locales, usa:

   ```powershell
   powershell -ExecutionPolicy Bypass -File .\scripts\Install-Suplementor.ps1
   ```

   El instalador comprueba PHP y extensiones, Composer, instala dependencias, pregunta el correo del administrador y las credenciales locales de MySQL, genera una contraseña administrativa aleatoria y configura `.env`. Luego aplica migraciones y seed, lista rutas y ejecuta pruebas. El seed crea cinco productos de demostración y al administrador. Confirma que `suplementor_laravel` esté vacía antes de continuar.

5. Al finalizar, copia y guarda la contraseña que muestra el instalador. No se puede volver a mostrar y el `.env` no se sube a Git.
6. Con XAMPP (Apache y MySQL) iniciado, abre una segunda terminal en `laravel-backend` y ejecuta:

   ```powershell
   php artisan serve
   ```

7. Deja esa terminal abierta. La API Laravel queda disponible en `http://127.0.0.1:8000/api`. Entra a `http://localhost/suplementor/index.html`: el JavaScript consume la API Laravel para iniciar sesión, consultar productos, administrar catálogo/inventario/usuarios (rol administrador), ver ventas y registrar compras. Como Apache y Laravel usan puertos distintos, la API incluye CORS local para `localhost` y `127.0.0.1`.

Para volver a ejecutar el sistema otro día, basta con iniciar Apache y MySQL desde XAMPP y, en `laravel-backend`, ejecutar `php artisan serve`. No vuelvas a correr migraciones ni el instalador si la base ya está configurada.

## Rutas principales

| Método | Ruta | Acceso |
|---|---|---|
| `POST` | `/api/auth/login` | Público, límite de 5 intentos por minuto |
| `POST` | `/api/auth/logout` | Token bearer válido |
| `GET` | `/api/auth/me` | Token bearer válido |
| `GET` | `/api/productos` | Público |
| `GET` | `/api/productos/{id}` | Público |
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

La respuesta contiene un token Sanctum. El front-end lo conserva solo en `sessionStorage` para la pestaña actual. En las solicitudes protegidas envía:

```http
Authorization: Bearer TOKEN_RECIBIDO
Accept: application/json
```

## Persistencia y compatibilidad de datos

Las migraciones Laravel describen las tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`, además de la tabla de tokens Sanctum administrada por el paquete. Se ejecutan sobre la base vacía `suplementor_laravel`. No modifican ni copian los datos de la base antigua. La migración de información existente debe hacerse como una tarea separada, con respaldo y comprobación de claves y contraseñas.

## Pruebas

Las pruebas de `tests/Feature/ApiTest.php` comprueban inicio de sesión, acceso protegido, permisos de administrador y registro de ventas con descuento del stock. El seed agrega cinco productos de demostración para que el catálogo no aparezca vacío. Ejecútalas desde `laravel-backend` con `php artisan test`. El repositorio incluye `composer.lock` para instalar las mismas versiones de dependencias con `composer install`.
