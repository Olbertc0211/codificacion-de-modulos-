# Arquitectura y estándares

## Implementación actual

El sistema usa una arquitectura web cliente-servidor:

```text
HTML/CSS/JavaScript
        |
        | HTTP y JSON
        v
api.php / login.php / panel.php
        |
        | MySQLi y consultas preparadas
        v
MySQL: usuarios, productos, ventas, detalle_ventas
```

La API está organizada en una separación MVC nativa básica:

- `api.php` inicializa sesión, conexión y dependencias.
- `app/Controllers/ApiController.php` recibe solicitudes, valida datos y construye respuestas HTTP/JSON.
- `app/Models/UserModel.php`, `ProductModel.php` y `SaleModel.php` encapsulan consultas y operaciones de persistencia.
- `conexion.php` configura la conexión MySQLi.
- `index.html`, `panel.php` y sus recursos conforman las vistas/interfaz del sistema.

La solución está construida en PHP nativo y no utiliza un framework. Esta organización aplica separación de responsabilidades a la API, pero no equivale a una migración a Laravel ni a una arquitectura empresarial completa.

## Posible evolución con framework

Si el equipo requiere adoptar un framework, puede migrarse gradualmente a una estructura como:

```text
app/
├── Controllers/
│   ├── AuthController.php
│   ├── UserController.php
│   ├── ProductController.php
│   ├── InventoryController.php
│   └── SaleController.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   └── Sale.php
├── Services/
│   └── SaleService.php
└── Support/
    ├── JsonResponse.php
    └── Validator.php
config/
└── database.php
routes/
└── api.php
public/
└── index.php
```

Responsabilidades sugeridas:

- **Rutas:** relacionar método y URL con el controlador correspondiente.
- **Controladores:** validar la solicitud y coordinar la respuesta HTTP.
- **Modelos:** encapsular el acceso a las tablas.
- **Servicios:** reunir reglas de negocio que involucren varias operaciones, como validar stock y registrar una venta.
- **Configuración:** centralizar la conexión y no publicar credenciales.

Esta estructura es una propuesta de evolución; todavía no está implementada. La adopción de Laravel requiere una migración aparte, instalación de Composer y cambios de configuración y despliegue.

## Convenciones aplicadas en la documentación de esta entrega

- Nombres de archivos y rutas descriptivos.
- Endpoints y ejemplos JSON documentados.
- Campos y permisos descritos de acuerdo con el comportamiento actual.
- No se registran credenciales personales en los documentos.
- Los cambios colaborativos deben quedar reflejados en commits reales de cada integrante.

## Trabajo grupal y versionamiento

El repositorio local está inicializado, pero todavía no tiene commits. Antes de entregar, debe registrarse el estado del proyecto con un commit y cada integrante debe documentar únicamente su aporte real. El grupo puede completar esta tabla con información verificada:

| Integrante | Aporte realizado | Evidencia (commit, archivo o actividad) |
|---|---|---|
| Completar con el nombre real | Completar con el aporte comprobable | Completar con la evidencia |
