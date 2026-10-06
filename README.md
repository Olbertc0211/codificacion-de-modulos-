# Suplementor

Aplicación web académica para consultar suplementos deportivos, iniciar sesión y administrar productos, inventario y ventas. El proyecto utiliza HTML, CSS y JavaScript en el front-end, y PHP con MySQL en el servidor.

> **Estado técnico:** el backend está implementado en PHP nativo, sin un framework. La API REST utiliza una separación MVC básica: `ApiController` recibe y valida solicitudes, y los modelos gestionan el acceso a datos. Esta organización no equivale a una migración a Laravel.

## Tecnologías

- HTML5, CSS3 y JavaScript.
- PHP con extensión MySQLi.
- MySQL/MariaDB.
- XAMPP para desarrollo local.
- Git para control de versiones.

## Requisitos

- XAMPP con Apache, MySQL y PHP.
- Un navegador web.
- Git, si se requiere trabajar con historial de versiones.

## Instalación local

1. Copia la carpeta del proyecto en `C:\xampp\htdocs\suplementor`.
2. Abre el panel de XAMPP e inicia **Apache** y **MySQL**.
3. Abre `http://localhost/phpmyadmin`.
4. Importa el archivo `suplementor.sql`. El script crea la base `suplementor` y las tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`.
5. Revisa los parámetros de conexión en `conexion.php`. La configuración incluida corresponde a XAMPP local (`localhost`, usuario `root`, contraseña vacía).
6. Si todavía no existe un administrador, abre `http://localhost/suplementor/crear_usuario.php` desde el mismo equipo. El instalador crea `admin@localhost`, genera una contraseña aleatoria y la muestra una sola vez. Guárdala de forma segura; el instalador no puede recuperarla.
7. Abre la aplicación en `http://localhost/suplementor/index.html`. No abras `index.html` directamente desde el explorador de archivos, porque PHP requiere Apache.

El instalador solo acepta solicitudes locales y no crea otro administrador si ya existe uno. Elimina o deshabilita `crear_usuario.php` después de su uso.

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
├── suplementor.sql         # Esquema de base de datos
└── docs/
    ├── API.md              # Referencia de endpoints
    ├── ARQUITECTURA.md     # Estructura actual y propuesta de evolución
    └── ENTREGA-PROFESOR.md # Síntesis formal del trabajo
```

## API

La API utiliza `api.php?recurso=...`; los endpoints protegidos necesitan una sesión iniciada. Consulta [docs/API.md](docs/API.md) para ver métodos, permisos, cuerpos JSON y ejemplos.

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
- La autenticación de la API usa sesiones PHP; el cliente debe conservar y enviar la cookie de sesión entre solicitudes.
- La configuración incluida es solo para el entorno de desarrollo local. El instalador genera una contraseña aleatoria; no se incluyen credenciales de administrador fijas.
- El backend no utiliza un framework. La API está separada en un controlador y modelos; las vistas del sitio se mantienen en los archivos de front-end y PHP existentes. La evolución propuesta está descrita en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md).

## Control de versiones

El repositorio local está inicializado, pero aún no contiene commits. Después de revisar los archivos y confirmar que no incluyan contraseñas reales, registra el proyecto:

```powershell
git add .
git commit -m "Organiza la API y documenta Suplementor"
```

Cada integrante debe registrar únicamente los cambios que haya realizado. No se debe atribuir trabajo a otra persona ni compartir credenciales reales en el repositorio.
