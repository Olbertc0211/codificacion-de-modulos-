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

La interfaz de la aplicación utiliza HTML, CSS y JavaScript. El backend anterior está en PHP nativo con MySQLi. Se preparó en `laravel-backend/` una API Laravel 13 con Sanctum, controladores, modelos Eloquent, migraciones y pruebas. El JavaScript ya consume la API Laravel para autenticación, catálogo, inventario, gestión de usuarios y ventas. El servidor Laravel debe iniciarse por separado y usar la base nueva `suplementor_laravel`. Debido a que el entorno disponible tiene PHP 8.0.30 y no Composer, aún se requiere instalar dependencias y ejecutar migraciones y pruebas para validar la integración en ese entorno.

## Versionamiento y colaboración

El proyecto está versionado con Git. Antes de entregar, se deben registrar los cambios actuales y respaldar los aportes de cada integrante con el historial real de commits, archivos desarrollados y actividades comprobables. La tabla de contribuciones de `ARQUITECTURA.md` se debe completar con la información del equipo.

## Texto sugerido para presentar

> Profesor(a): presentamos el módulo Suplementor con interfaz HTML, CSS y JavaScript conectada a una API desarrollada con Laravel 13 y Sanctum. La API maneja autenticación por token, consulta y administración de productos, inventario, usuarios y ventas. Las ventas validan la disponibilidad y se registran mediante transacciones. La nueva API utiliza la base separada `suplementor_laravel` para conservar la base original. La configuración del front-end para las rutas Laravel está implementada; para ejecutar y validar todo el flujo en este equipo hace falta PHP 8.3 o superior, Composer, instalar dependencias, migrar la base y correr las pruebas.
