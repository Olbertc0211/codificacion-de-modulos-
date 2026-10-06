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

La aplicación original se implementó con PHP nativo, MySQLi y MySQL, junto con HTML, CSS y JavaScript. Como avance de migración, se preparó una API Laravel 13 independiente en `laravel-backend/`, con Sanctum, controladores, modelos, migraciones y pruebas. La aplicación original se conserva y aún es la que está conectada a la interfaz. Debido a que el entorno disponible tiene PHP 8.0.30 y no Composer, el backend Laravel todavía requiere instalación de dependencias, actualización del entorno, ejecución de migraciones/pruebas e integración con el front-end.

## Versionamiento y colaboración

El proyecto está versionado con Git. Antes de entregar, se deben registrar los cambios actuales y respaldar los aportes de cada integrante con el historial real de commits, archivos desarrollados y actividades comprobables. La tabla de contribuciones de `ARQUITECTURA.md` se debe completar con la información del equipo.

## Texto sugerido para presentar

> Profesor(a): presentamos el módulo de Suplementor, una aplicación web desarrollada con HTML, CSS, JavaScript, PHP y MySQL. La versión original ofrece autenticación y gestión con PHP nativo. Como avance frente a la recomendación de framework, preparamos en `laravel-backend/` una API independiente con Laravel 13 y Sanctum para autenticación por token, usuarios, productos, inventario y ventas; las ventas validan stock y se registran mediante transacciones. Conservamos el sistema actual mientras actualizamos el entorno a PHP 8.3+ e instalamos Composer. La API Laravel debe completar su instalación, pruebas e integración con el front-end antes de considerarse desplegada. Usamos una base separada para no alterar los datos originales.
