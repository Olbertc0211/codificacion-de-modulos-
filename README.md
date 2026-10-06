# Suplementor

Aplicación web académica para consultar suplementos deportivos, iniciar sesión y administrar productos, inventario y ventas. La interfaz existente utiliza PHP nativo/MySQL; se añadió un backend Laravel independiente para preparar la migración.

> **Estado de migración:** la aplicación original continúa funcionando con su backend PHP nativo. El nuevo backend Laravel está aislado en [`laravel-backend/`](laravel-backend/README.md), conserva la aplicación anterior y no está conectado todavía a la interfaz. Laravel requiere PHP 8.3+ y Composer; el entorno detectado tenía PHP 8.0.30 y no tenía Composer, por lo que las dependencias y pruebas quedan pendientes de instalar y ejecutar tras actualizar el entorno.

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

## Backend Laravel en preparación

Para configurar la API Laravel separada, sigue [`laravel-backend/README.md`](laravel-backend/README.md). No es necesario reemplazar XAMPP: se instala PHP 8.3 aparte para Laravel/Composer y se conserva XAMPP para el sitio anterior y MySQL. Usa la base nueva `suplementor_laravel`; la base `suplementor` y el sitio anterior no se modifican. La interfaz actual sigue utilizando sus endpoints PHP nativos hasta completar y probar una fase posterior de integración.

## Estructura actual

```text
suplementor/
├── api.php                 # Punto de entrada de la API
├── app/
│   ├── Controllers/
│   │   └── ApiController.php
│   └── Models/
│       ├── ProductModel.php
│       ├── SaleModel.php
│       └── UserModel.php
├── conexion.php            # Conexión MySQLi
├── login.php               # Inicio de sesión usado por el front-end
├── cerrar_sesion.php       # Cierre de sesión del panel web
├── crear_usuario.php       # Alta inicial de la cuenta de demostración
├── panel.php               # Vista protegida por sesión
├── index.html              # Interfaz pública
├── script.js               # Interacciones del front-end
├── estilo.css              # Estilos y diseño responsive
├── base_de_datos.sql       # Esquema de la base de datos original
├── laravel-backend/        # API Laravel separada, pendiente de instalar en PHP 8.3+
└── docs/
    ├── API.md              # Referencia de endpoints
    ├── ARQUITECTURA.md     # Estructura actual y propuesta de evolución
    └── ENTREGA-PROFESOR.md # Síntesis formal del trabajo
```

## API

La API actualmente conectada al sitio utiliza `api.php?recurso=...`; los endpoints protegidos necesitan una sesión iniciada. Consulta [docs/API.md](docs/API.md). La API Laravel separada utiliza rutas `/api/...`, tokens bearer Sanctum y está documentada en [`laravel-backend/README.md`](laravel-backend/README.md).

Resumen:

| Recurso | Métodos | Acceso |
|---|---|---|
| `auth&accion=login` | POST | Público |
| `auth&accion=logout` | POST | Sesión iniciada |
| `usuarios` | GET, POST | Administrador |
| `productos` | GET | Sesión iniciada |
| `productos` | POST, PUT, DELETE | Administrador |
| `inventario&id={id}` | PUT | Administrador |
| `ventas` | GET, POST | Sesión iniciada |

## Seguridad y límites conocidos

- Las consultas de la API usan sentencias preparadas en las operaciones con parámetros.
- Las contraseñas de usuarios se almacenan con `password_hash()` y se verifican con `password_verify()`.
- El backend PHP actual autentica con sesiones. El backend Laravel en preparación usa tokens Sanctum.
- La configuración incluida es solo para el entorno de desarrollo local. El instalador genera una contraseña aleatoria; no se incluyen credenciales de administrador fijas.
- El framework Laravel se encuentra en un backend paralelo y aún no reemplaza al backend conectado a la página. La secuencia de migración y las limitaciones están descritas en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md).

## Control de versiones

El proyecto está versionado con Git. Después de revisar los archivos modificados y confirmar que no incluyan secretos, registra los cambios:

```powershell
git add .
git commit -m "Prepara backend Laravel para Suplementor"
```

Cada integrante debe registrar únicamente los cambios que haya realizado. No se debe atribuir trabajo a otra persona ni compartir credenciales reales en el repositorio.
