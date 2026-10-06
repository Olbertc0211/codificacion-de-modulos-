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

El módulo se implementó con PHP nativo, MySQLi y MySQL, junto con HTML, CSS y JavaScript. La API cuenta con una separación MVC básica: un controlador coordina las solicitudes y los modelos encapsulan el acceso a datos. La versión actual no utiliza un framework; una eventual migración a Laravel se reconoce como una mejora futura y no como una característica ya realizada.

## Versionamiento y colaboración

El repositorio local está inicializado con Git, pero todavía no contiene commits. Antes de entregar, se debe registrar el estado del proyecto y respaldar los aportes de cada integrante con el historial real de commits, archivos desarrollados y actividades comprobables. La tabla de contribuciones de `ARQUITECTURA.md` se debe completar con la información del equipo.

## Texto sugerido para presentar

> Profesor(a): presentamos el módulo de Suplementor, una aplicación web desarrollada con HTML, CSS, JavaScript, PHP y MySQL. El backend incorpora una API REST con respuestas JSON para autenticación, usuarios, productos, inventario y ventas. La autenticación se maneja mediante sesiones PHP y las contraseñas se verifican usando hashes. Para el registro de ventas se valida la disponibilidad de inventario y se utilizan transacciones para mantener consistentes los datos de la venta y el stock. El instalador local genera una contraseña aleatoria para la primera cuenta administradora.
>
> Reconocemos que esta versión está desarrollada en PHP nativo y no utiliza un framework. Organizamos la API separando el punto de entrada, el controlador y los modelos para mejorar la distribución de responsabilidades. Como evolución, planteamos incorporar rutas y servicios y evaluar una migración a Laravel. Antes de entregar, registraremos el proyecto en Git y contrastaremos los cambios con los aportes reales de cada integrante del equipo.
