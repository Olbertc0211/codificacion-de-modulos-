# Arquitectura y estándares

## Implementación actual

El sistema usa una arquitectura web cliente-servidor:

```text
HTML/CSS/JavaScript
        |
        | HTTP y JSON
        v
api.php / login.php / panel.php
        |
        | MySQLi y consultas preparadas
        v
MySQL: usuarios, productos, ventas, detalle_ventas
```

La API nativa existente está organizada en una separación MVC básica:

- `api.php` inicializa sesión, conexión y dependencias.
- `app/Controllers/ApiController.php` recibe solicitudes, valida datos y construye respuestas HTTP/JSON.
- `app/Models/UserModel.php`, `ProductModel.php` y `SaleModel.php` encapsulan consultas y operaciones de persistencia.
- `conexion.php` configura la conexión MySQLi.
- `index.html`, `panel.php` y sus recursos conforman las vistas/interfaz del sistema.

La aplicación original está construida en PHP nativo y no utiliza un framework.

## Backend Laravel paralelo

Se preparó `laravel-backend/` como una aplicación independiente con Laravel 13, Sanctum, controladores, modelos Eloquent, migraciones y pruebas de características. Implementa autenticación bearer, usuarios, productos, inventario y ventas. Se conserva el backend nativo y la interfaz existente mientras se actualiza el entorno.

Este backend paralelo todavía no está instalado ni ejecutado: el PHP de XAMPP disponible es 8.0.30 y no se detectó Composer, mientras que `composer.json` requiere PHP 8.3 o posterior. No es necesario sustituir XAMPP: puede instalarse PHP 8.3 NTS aparte para la terminal y Composer, conservando XAMPP para Apache, MySQL y el backend anterior. Luego se instalan dependencias, se configura una base separada, se ejecutan las migraciones y pruebas, y se planifica la integración del front-end. La base original `suplementor` no se usa para las migraciones Laravel.

## Posible evolución tras validar Laravel

Una vez que la API Laravel separada esté ejecutada y validada, se puede integrar la interfaz existente y migrar los datos, con respaldo previo. No se deben retirar los endpoints PHP antiguos antes de completar esa comprobación.

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
