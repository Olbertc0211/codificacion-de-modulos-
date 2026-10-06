# Suplementor

Aplicación web académica para consultar suplementos deportivos, iniciar sesión y administrar productos, inventario y ventas. El front-end se conecta a una API desarrollada con Laravel 13 y Sanctum; el backend PHP original se conserva como respaldo.

> **Para ejecutar el proyecto:** Laravel necesita PHP 8.3+ y Composer 2. El XAMPP de este equipo contiene PHP 8.0.30, así que instala PHP 8.3 por separado y conserva XAMPP para Apache y MySQL. El instalador guiado crea la configuración local, ejecuta las migraciones y pruebas; consulta [`laravel-backend/README.md`](laravel-backend/README.md).

## Tecnologías

- HTML5, CSS3 y JavaScript.
- PHP con extensión MySQLi.
- MySQL/MariaDB.
- XAMPP para desarrollo local.
- Git para control de versiones.
- Laravel 13 / Sanctum para el backend nuevo (PHP 8.3+ y Composer).

## Requisitos

- XAMPP con Apache, MySQL y PHP.
- Un navegador web.
- Git, si se requiere trabajar con historial de versiones.

## Instalación local

1. Copia la carpeta del proyecto en `C:\xampp\htdocs\suplementor`.
2. Abre el panel de XAMPP e inicia **Apache** y **MySQL**.
3. Abre `http://localhost/phpmyadmin`.
4. Importa el archivo `base_de_datos.sql`. El script crea la base `suplementor` y las tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`.
5. Revisa los parámetros de conexión en `conexion.php`. La configuración incluida corresponde a XAMPP local (`localhost`, usuario `root`, contraseña vacía).
6. Si todavía no existe un administrador, abre `http://localhost/suplementor/crear_usuario.php` desde el mismo equipo. El instalador crea `admin@localhost`, genera una contraseña aleatoria y la muestra una sola vez. Guárdala de forma segura; el instalador no puede recuperarla.
7. Abre la aplicación en `http://localhost/suplementor/index.html`. No abras `index.html` directamente desde el explorador de archivos, porque PHP requiere Apache.

El instalador solo acepta solicitudes locales y no crea otro administrador si ya existe uno. Elimina o deshabilita `crear_usuario.php` después de su uso.

## Preparar y ejecutar Laravel

Sigue [`laravel-backend/README.md`](laravel-backend/README.md): instala PHP 8.3 y Composer, crea `suplementor_laravel`, ejecuta `scripts/Install-Suplementor.ps1` y arranca Laravel con `php artisan serve`. Inicia Apache y MySQL desde XAMPP y abre `http://localhost/suplementor/index.html`. El JavaScript de la interfaz consume Laravel; la base original `suplementor` se conserva sin cambios.

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
