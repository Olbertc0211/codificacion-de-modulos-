# Presentación del proyecto

## Descripción

Suplementor es una aplicación web académica para la consulta y administración de suplementos deportivos. El proyecto integra una interfaz desarrollada con HTML, CSS y JavaScript con un backend en PHP y una base de datos MySQL.

## Funcionalidades desarrolladas

- Inicio y cierre de sesión con sesiones PHP.
- Gestión de usuarios con roles.
- Consulta y mantenimiento de productos.
- Actualización del inventario.
- Registro de ventas con detalle de productos y actualización del stock.
- API REST que recibe solicitudes HTTP y entrega respuestas JSON.

## Persistencia y validaciones

La base de datos contiene las tablas `usuarios`, `productos`, `ventas` y `detalle_ventas`, relacionadas para conservar usuarios, productos y transacciones. El sistema valida los datos recibidos, utiliza sentencias preparadas en la API y verifica las contraseñas con las funciones de hash de PHP. El registro de ventas utiliza una transacción para que el stock y los registros de venta se mantengan consistentes.

## Tecnologías y alcance

La interfaz de la aplicación utiliza HTML, CSS y JavaScript y consume la API Laravel 13 con Sanctum, controladores, modelos Eloquent, migraciones y pruebas, ubicada en `laravel-backend/`. El JavaScript está conectado para autenticación, catálogo, inventario, gestión de usuarios y ventas. El proyecto conserva el backend anterior en PHP nativo y utiliza la base separada `suplementor_laravel` para Laravel. En el computador de desarrollo, las migraciones y pruebas ya se ejecutaron correctamente. En otro equipo se requiere PHP 8.3+ y Composer; la guía explica la instalación y puesta en marcha.

## Versionamiento y colaboración

El proyecto está versionado con Git. Antes de entregar, se deben registrar los cambios actuales y respaldar los aportes de cada integrante con el historial real de commits, archivos desarrollados y actividades comprobables. La tabla de contribuciones de `ARQUITECTURA.md` se debe completar con la información del equipo.

## Texto sugerido para presentar

> Profesor(a): presentamos el módulo Suplementor con interfaz HTML, CSS y JavaScript conectada a una API Laravel 13 y Sanctum. La API ofrece autenticación por token, gestión de productos, inventario y usuarios, y registro de ventas validando el stock mediante transacciones. Laravel usa una base separada llamada `suplementor_laravel` para conservar los datos originales. En el equipo de desarrollo se validaron la instalación, las migraciones, las pruebas y el inicio de sesión. Para ejecutarlo en otro equipo, se requiere PHP 8.3+ y Composer; siga la guía, inicie Apache y MySQL en XAMPP y Laravel con `php artisan serve`.
