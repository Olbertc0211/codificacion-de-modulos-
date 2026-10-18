# API PHP para la aplicación móvil

Esta API maneja el registro y el inicio de sesión de la app Android. Utiliza PHP con MySQLi y la base `suplementormovil`; no depende de la base Laravel `suplementor_laravel`.

## Preparación local

1. Copia la carpeta `mobile-api` dentro de `C:\xampp\htdocs\mi_api`, o configura Apache para servir esta carpeta bajo la ruta `/mi_api/`.
2. Inicia Apache y MySQL desde XAMPP.
3. Con MySQL Workbench, conéctate al MySQL local y ejecuta `database.sql`. El script crea la base y la tabla vacía `usuarios`; no elimina ni modifica otras bases.
4. En `conexion.php`, ajusta host, usuario y contraseña MySQL si no usas los valores predeterminados de XAMPP.
5. Abre `http://localhost/mi_api/registro.php` para comprobar que Apache encuentre el archivo. El endpoint espera solicitudes POST JSON.

## Endpoints

| Método | Ruta | Función |
|---|---|---|
| `POST` | `/mi_api/registro.php` | Crea un usuario; guarda la contraseña con `password_hash`. |
| `POST` | `/mi_api/login.php` | Verifica el correo y contraseña con `password_verify`. |

La app envía `nombre`, `email` y `password` al registro; el login recibe `email` y `password`. Las contraseñas nunca se almacenan en texto plano.

## Conectar un teléfono a XAMPP

En `android-app/app/src/main/java/com/example/suplementor/LoginActivity.java`, cambia `BASE_URL` por la dirección IPv4 Wi-Fi del computador que ejecuta XAMPP, por ejemplo `http://192.168.1.25/mi_api/`. El teléfono y el computador deben estar en la misma red y Apache debe estar iniciado. Si Windows Firewall lo solicita, permite las conexiones locales de Apache.

En el emulador Android Studio usa `http://10.0.2.2/mi_api/` en vez de la IP Wi-Fi.
