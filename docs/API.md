# API REST nativa de Suplementor

> Esta referencia corresponde a `api.php` en la raíz, que sigue atendiendo la interfaz actual. La nueva API Laravel paralela usa tokens bearer y sus rutas están en [`laravel-backend/README.md`](../laravel-backend/README.md).

## Información general

- URL base local: `http://localhost/suplementor/api.php`
- Formato de respuesta: JSON UTF-8.
- Los cuerpos de solicitudes POST y PUT deben enviarse como `application/json`.
- Autenticación: sesión PHP. Después del login, el cliente debe conservar la cookie de sesión (por ejemplo, `PHPSESSID`) y enviarla en las demás solicitudes.
- Los parámetros de ruta se reciben mediante query string: `?recurso=productos&id=1`.

## Códigos HTTP utilizados

| Código | Significado |
|---|---|
| `200` | Solicitud atendida |
| `201` | Registro creado |
| `400` | JSON inválido o identificador ausente/inválido |
| `401` | Falta iniciar sesión o las credenciales no son válidas |
| `403` | El rol no tiene permiso |
| `404` | Recurso o método no reconocido |
| `409` | Conflicto, por ejemplo correo duplicado o stock insuficiente |
| `422` | Datos recibidos no válidos |

## Autenticación

### Iniciar sesión

`POST /api.php?recurso=auth&accion=login`

Solicitud:

```json
{
  "correo": "admin@localhost",
  "password": "contraseña-generada-al-instalar"
}
```

La respuesta incluye el usuario sin el hash de contraseña y establece una sesión PHP.

### Cerrar sesión

`POST /api.php?recurso=auth&accion=logout`

Requiere enviar la cookie de sesión creada durante el login.

## Usuarios

Ambas operaciones requieren rol `Administrador`.

### Consultar usuarios

`GET /api.php?recurso=usuarios`

La respuesta contiene usuarios sin el campo de contraseña.

### Crear usuario

`POST /api.php?recurso=usuarios`

```json
{
  "nombre": "Nombre de ejemplo",
  "correo": "persona@example.com",
  "password": "clave123",
  "rol": "Cliente"
}
```

El nombre, un correo válido y una contraseña de al menos seis caracteres son obligatorios. El rol predeterminado es `Cliente`.

## Productos

Todas las operaciones de productos requieren sesión. Consultar productos está disponible para cualquier usuario autenticado; crear, actualizar y eliminar requieren rol `Administrador`.

### Consultar productos

`GET /api.php?recurso=productos`

### Crear producto

`POST /api.php?recurso=productos`

```json
{
  "nombre": "Proteína Whey",
  "descripcion": "Proteína de rápida absorción",
  "precio": 120000,
  "stock": 20
}
```

### Actualizar producto

`PUT /api.php?recurso=productos&id=1`

Envía los mismos campos que al crear producto.

### Eliminar producto

`DELETE /api.php?recurso=productos&id=1`

## Inventario

Actualizar el stock de un producto requiere rol `Administrador`.

`PUT /api.php?recurso=inventario&id=1`

```json
{
  "stock": 35
}
```

El stock debe ser un entero mayor o igual a cero.

## Ventas

Las operaciones requieren una sesión iniciada.

### Consultar ventas

`GET /api.php?recurso=ventas`

Devuelve el identificador, total, fecha y nombre del cliente de cada venta.

### Registrar venta

`POST /api.php?recurso=ventas`

```json
{
  "productos": [
    {
      "producto_id": 1,
      "cantidad": 2
    }
  ]
}
```

La API consulta los precios actuales en la base de datos, comprueba el stock, registra la venta y sus detalles, y descuenta las unidades disponibles dentro de una transacción. Si el stock no alcanza, devuelve `409` y no confirma la venta.

## Prueba manual en XAMPP

1. Inicia Apache y MySQL.
2. Conéctate al MySQL local con MySQL Workbench y abre/ejecuta `base_de_datos.sql`.
3. Inicia sesión para obtener la cookie de sesión.
4. Envía solicitudes a `http://localhost/suplementor/api.php` usando una herramienta que permita especificar método, JSON y cookies (por ejemplo, Postman).

> Esta API usa sesiones de navegador, no tokens JWT. En Postman se debe conservar la cookie `PHPSESSID` entre solicitudes protegidas.
