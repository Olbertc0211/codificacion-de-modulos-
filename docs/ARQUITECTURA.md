# Arquitectura y estándares

## Implementación actual

El sistema usa una arquitectura web cliente-servidor:

```text
HTML/CSS/JavaScript (Apache)
        |
        | HTTP y JSON
        v
Laravel API :8000 (Sanctum)
        |
        | Eloquent y transacciones
        v
MySQL: suplementor_laravel

Backend PHP original y base `suplementor` conservados como respaldo.
```

El backend PHP nativo anterior está organizado en una separación MVC básica y se conserva como respaldo:

- `api.php` inicializa sesión, conexión y dependencias.
- `app/Controllers/ApiController.php` recibe solicitudes, valida datos y construye respuestas HTTP/JSON.
- `app/Models/UserModel.php`, `ProductModel.php` y `SaleModel.php` encapsulan consultas y operaciones de persistencia.
- `conexion.php` configura la conexión MySQLi.
- `index.html`, `panel.php` y sus recursos conforman las vistas/interfaz del sistema.

La API Laravel es el backend al que se conecta el front-end. Las páginas PHP antiguas siguen disponibles, pero el JavaScript de la portada ya no las usa para el flujo de autenticación, catálogo y ventas.

## Backend Laravel paralelo

Se preparó `laravel-backend/` como una aplicación independiente con Laravel 13, Sanctum, controladores, modelos Eloquent, migraciones y pruebas de características. Implementa autenticación bearer, usuarios, productos, inventario y ventas. `script.js` consume esta API para inicio de sesión, catálogo, carrito/registro de ventas, inventario, usuarios y reportes. Se conserva el backend nativo como respaldo y para las páginas existentes.

La integración del front-end está implementada y fue verificada localmente con PHP 8.5, Composer, MySQL/MariaDB y la base separada. XAMPP conserva su PHP 8.0.30 para páginas antiguas y ofrece Apache y MySQL; PHP 8.5 se ejecuta aparte para Laravel. El instalador guiado instala dependencias, configura `.env`, genera credenciales administrativas y ejecuta migraciones y pruebas. CORS se limita a orígenes de desarrollo local. La base original `suplementor` no se modifica.

## Puesta en marcha y siguiente paso

Para ejecutar el conjunto en otro equipo, instala PHP 8.3+ y Composer, crea `suplementor_laravel` con MySQL Workbench, ejecuta el instalador y mantén Apache, MySQL y `php artisan serve` activos. Antes de retirar el backend PHP antiguo, respalda y migra los datos necesarios.

## Convenciones aplicadas en la documentación de esta entrega

- Nombres de archivos y rutas descriptivos.
- Endpoints y ejemplos JSON documentados.
- Campos y permisos descritos de acuerdo con el comportamiento actual.
- No se registran credenciales personales en los documentos.
- Los cambios colaborativos deben quedar reflejados en commits reales de cada integrante.

## Trabajo grupal y versionamiento

Cada integrante debe documentar únicamente su aporte real en commits y completar esta tabla con información verificada:

| Integrante | Aporte realizado | Evidencia (commit, archivo o actividad) |
|---|---|---|
| Completar con el nombre real | Completar con el aporte comprobable | Completar con la evidencia |
