# Suplementor

Aplicación web académica para consultar suplementos deportivos, iniciar sesión y administrar productos, inventario y ventas. El front-end se conecta a una API desarrollada con Laravel 13 y Sanctum; el backend PHP original se conserva como respaldo.

> **Para ejecutar el proyecto:** Laravel necesita PHP 8.3+ y Composer 2. En este equipo se configuró PHP 8.5 por separado y se conservó XAMPP para Apache y MySQL. El instalador guiado prepara dependencias, configuración, base de datos y cuenta administradora; consulta [`laravel-backend/README.md`](laravel-backend/README.md).

## Tecnologías

- HTML5, CSS3 y JavaScript.
- PHP con extensión MySQLi.
- MySQL/MariaDB.
- XAMPP para desarrollo local.
- Git para control de versiones.
- Laravel 13 / Sanctum para el backend nuevo (PHP 8.3+ y Composer).

## Requisitos

- XAMPP con Apache, MySQL y PHP.
- MySQL Workbench para administrar las bases de datos (no se requiere phpMyAdmin).
- Un navegador web.
- Git, si se requiere trabajar con historial de versiones.
- Para el backend Laravel conectado al front-end: PHP 8.3+ y Composer 2. El PHP 8.0 incluido en este XAMPP no cumple ese requisito.

## Backend PHP anterior (respaldo)

Para conservar o consultar el backend PHP antiguo:

1. Copia la carpeta en `C:\xampp\htdocs\suplementor` e inicia **Apache** y **MySQL** desde XAMPP.
2. En MySQL Workbench conéctate a `127.0.0.1:3306` con el usuario y contraseña configurados para MySQL.
3. Abre y ejecuta `base_de_datos.sql` desde Workbench. El script crea la base `suplementor` y sus tablas.
4. Revisa las credenciales de conexión en `conexion.php`. La configuración predeterminada de XAMPP es `root` sin contraseña.

Este backend se conserva como respaldo. El front-end principal usa la API Laravel descrita a continuación.

## Preparar y ejecutar Laravel

Sigue [`laravel-backend/README.md`](laravel-backend/README.md): instala PHP 8.3+ y Composer, crea `suplementor_laravel` desde MySQL Workbench, ejecuta `scripts/Install-Suplementor.ps1` y arranca Laravel con `php artisan serve`. Inicia Apache y MySQL desde XAMPP y abre `http://localhost/suplementor/index.html`. La base original `suplementor` no se modifica. No se necesita phpMyAdmin.

## Estructura actual

```text
suplementor/
├── api.php                 # Punto de entrada de la API
├── conexion.php            # Conexión MySQLi
├── login.php               # Inicio de sesión usado por el front-end
├── cerrar_sesion.php       # Cierre de sesión del panel web
├── crear_usuario.php       # Alta inicial de la cuenta de demostración
├── panel.php               # Vista protegida por sesión
├── index.html              # Interfaz pública
├── script.js               # Interacciones del front-end
├── estilo.css              # Estilos y diseño responsive
├── base_de_datos.sql       # Esquema de la base de datos original
├── laravel-backend/        # API Laravel 13 conectada con el front-end
│   ├── app/                # Controladores, middleware y modelos Eloquent
│   ├── routes/api.php      # Rutas protegidas con Sanctum y permisos
│   └── database/migrations/# Esquema para suplementor_laravel
│   └── scripts/            # Instalador guiado para Windows
└── docs/
    ├── API.md              # Referencia de endpoints
    ├── ARQUITECTURA.md     # Estructura actual y propuesta de evolución
    └── ENTREGA-PROFESOR.md # Síntesis formal del trabajo
```

## API

La API Laravel ofrece rutas `/api/...` y autenticación bearer Sanctum. El front-end usa esta API para iniciar/cerrar sesión, consultar y administrar productos, actualizar inventario, gestionar usuarios y registrar ventas. Consulta [`laravel-backend/README.md`](laravel-backend/README.md) para la tabla de rutas y el procedimiento de instalación. La API PHP antigua (`api.php`) se conserva como respaldo y no es la que usa el JavaScript actualizado.

## Seguridad y límites conocidos

- Las consultas de la API usan sentencias preparadas en las operaciones con parámetros.
- Las contraseñas de usuarios se almacenan con `password_hash()` y se verifican con `password_verify()`.
- El backend PHP anterior autentica con sesiones. La integración del front-end Laravel utiliza tokens Sanctum guardados temporalmente por pestaña en `sessionStorage`.
- La configuración incluida es solo para el entorno de desarrollo local. El instalador genera una contraseña aleatoria; no se incluyen credenciales de administrador fijas.
- Laravel está en una carpeta independiente para no interrumpir el servidor PHP antiguo. Se requiere iniciar también Laravel y configurar su base antes de que las funciones conectadas de la interfaz estén disponibles.

## Control de versiones

El proyecto está versionado con Git. Después de revisar los archivos modificados y confirmar que no incluyan secretos, registra los cambios:

```powershell
git add .
git commit -m "Prepara backend Laravel para Suplementor"
```

Cada integrante debe registrar únicamente los cambios que haya realizado. No se debe atribuir trabajo a otra persona ni compartir credenciales reales en el repositorio.
