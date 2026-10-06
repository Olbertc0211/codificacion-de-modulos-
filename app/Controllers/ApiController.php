<?php

/**
 * Recibe solicitudes HTTP, valida datos y delega las operaciones a los modelos.
 */
class ApiController
{
    private $usuarios;
    private $productos;
    private $ventas;

    public function __construct($usuarios, $productos, $ventas)
    {
        $this->usuarios = $usuarios;
        $this->productos = $productos;
        $this->ventas = $ventas;
    }

    public function manejar($metodo, $recurso, $accion)
    {
        if ($recurso === "auth" && $accion === "login" && $metodo === "POST") {
            $this->iniciarSesion();
        }

        if ($recurso === "auth" && $accion === "logout" && $metodo === "POST") {
            $this->cerrarSesion();
        }

        $this->exigirSesion();

        switch ($recurso) {
            case "usuarios":
                $this->manejarUsuarios($metodo);
                break;
            case "productos":
                $this->manejarProductos($metodo);
                break;
            case "inventario":
                $this->manejarInventario($metodo);
                break;
            case "ventas":
                $this->manejarVentas($metodo);
                break;
            default:
                $this->responder(["error" => "Recurso o método no encontrado."], 404);
        }

        $this->responder(["error" => "Recurso o método no encontrado."], 404);
    }

    private function iniciarSesion()
    {
        $datos = $this->entradaJson();
        $correo = trim($datos["correo"] ?? "");
        $password = $datos["password"] ?? "";

        if ($correo === "" || $password === "") {
            $this->responder(
                ["error" => "El correo y la contraseña son obligatorios."],
                422
            );
        }

        $usuario = $this->usuarios->buscarPorCorreo($correo);
        if (!$usuario || !password_verify($password, $usuario["password"])) {
            $this->responder(["error" => "Credenciales incorrectas."], 401);
        }

        session_regenerate_id(true);
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["correo"] = $usuario["correo"];
        $_SESSION["rol"] = $usuario["rol"];
        unset($usuario["password"]);

        $this->responder([
            "mensaje" => "Inicio de sesión exitoso.",
            "usuario" => $usuario
        ]);
    }

    private function cerrarSesion()
    {
        $this->exigirSesion();
        $_SESSION = [];
        session_destroy();
        $this->responder(["mensaje" => "Sesión cerrada."]);
    }

    private function manejarUsuarios($metodo)
    {
        $this->exigirAdministrador();

        if ($metodo === "GET") {
            $this->responder(["datos" => $this->usuarios->listar()]);
        }

        if ($metodo === "POST") {
            $datos = $this->entradaJson();
            $nombre = trim($datos["nombre"] ?? "");
            $correo = trim($datos["correo"] ?? "");
            $password = $datos["password"] ?? "";
            $rol = trim($datos["rol"] ?? "Cliente");

            if (
                $nombre === "" ||
                !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
                strlen($password) < 6
            ) {
                $this->responder(
                    ["error" => "Nombre, correo válido y contraseña de mínimo 6 caracteres son obligatorios."],
                    422
                );
            }

            $id = $this->usuarios->crear(
                $nombre,
                $correo,
                password_hash($password, PASSWORD_DEFAULT),
                $rol
            );

            if ($id === false) {
                $this->responder(
                    ["error" => "No fue posible crear el usuario. Verifica que el correo no esté repetido."],
                    409
                );
            }

            $this->responder(["mensaje" => "Usuario creado.", "id" => $id], 201);
        }
    }

    private function manejarProductos($metodo)
    {
        if ($metodo === "GET") {
            $this->responder(["datos" => $this->productos->listar()]);
        }

        $this->exigirAdministrador();

        if ($metodo === "POST") {
            $datos = $this->datosProducto();
            $id = $this->productos->crear(
                $datos["nombre"],
                $datos["descripcion"],
                $datos["precio"],
                $datos["stock"]
            );
            $this->responder(["mensaje" => "Producto creado.", "id" => $id], 201);
        }

        $id = $this->obtenerId();
        if ($metodo === "PUT") {
            $datos = $this->datosProducto();
            $this->productos->actualizar(
                $id,
                $datos["nombre"],
                $datos["descripcion"],
                $datos["precio"],
                $datos["stock"]
            );
            $this->responder(["mensaje" => "Producto actualizado."]);
        }

        if ($metodo === "DELETE") {
            $this->productos->eliminar($id);
            $this->responder(["mensaje" => "Producto eliminado."]);
        }
    }

    private function manejarInventario($metodo)
    {
        if ($metodo !== "PUT") {
            return;
        }

        $this->exigirAdministrador();
        $id = $this->obtenerId();
        $datos = $this->entradaJson();
        $stock = filter_var($datos["stock"] ?? null, FILTER_VALIDATE_INT);

        if ($stock === false || $stock < 0) {
            $this->responder(
                ["error" => "El stock debe ser un entero mayor o igual a cero."],
                422
            );
        }

        $this->productos->actualizarStock($id, $stock);
        $this->responder(["mensaje" => "Inventario actualizado."]);
    }

    private function manejarVentas($metodo)
    {
        if ($metodo === "GET") {
            $this->responder(["datos" => $this->ventas->listar()]);
        }

        if ($metodo === "POST") {
            $datos = $this->entradaJson();
            $items = $datos["productos"] ?? [];
            if (!is_array($items) || count($items) === 0) {
                $this->responder(
                    ["error" => "La venta debe incluir al menos un producto."],
                    422
                );
            }

            $productos = [];
            foreach ($items as $item) {
                if (!is_array($item)) {
                    $this->responder(
                        ["error" => "Cada producto debe ser un objeto JSON."],
                        422
                    );
                }

                $productoId = filter_var(
                    $item["producto_id"] ?? null,
                    FILTER_VALIDATE_INT
                );
                $cantidad = filter_var(
                    $item["cantidad"] ?? null,
                    FILTER_VALIDATE_INT
                );

                if (!$productoId || $productoId < 1 || !$cantidad || $cantidad < 1) {
                    $this->responder(
                        ["error" => "Cada producto debe tener producto_id y cantidad válidos."],
                        422
                    );
                }

                $productos[] = [
                    "producto_id" => $productoId,
                    "cantidad" => $cantidad
                ];
            }

            $resultado = $this->ventas->crear(
                $_SESSION["usuario_id"],
                $productos
            );

            if (isset($resultado["error"])) {
                $this->responder($resultado, 409);
            }

            $this->responder([
                "mensaje" => "Venta registrada.",
                "venta_id" => $resultado["venta_id"],
                "total" => $resultado["total"]
            ], 201);
        }
    }

    private function datosProducto()
    {
        $datos = $this->entradaJson();
        $nombre = trim($datos["nombre"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $precio = filter_var($datos["precio"] ?? null, FILTER_VALIDATE_FLOAT);
        $stock = filter_var($datos["stock"] ?? null, FILTER_VALIDATE_INT);

        if (
            $nombre === "" ||
            $descripcion === "" ||
            $precio === false ||
            $precio < 0 ||
            $stock === false ||
            $stock < 0
        ) {
            $this->responder(
                ["error" => "Nombre, descripción, precio y stock deben ser válidos."],
                422
            );
        }

        return [
            "nombre" => $nombre,
            "descripcion" => $descripcion,
            "precio" => $precio,
            "stock" => $stock
        ];
    }

    private function entradaJson()
    {
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            $this->responder(["error" => "El cuerpo debe ser un JSON válido."], 400);
        }

        return $datos;
    }

    private function obtenerId()
    {
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $this->responder(["error" => "Debes indicar un id válido."], 400);
        }

        return $id;
    }

    private function exigirSesion()
    {
        if (!isset($_SESSION["usuario_id"])) {
            $this->responder(
                ["error" => "Debes iniciar sesión para usar este recurso."],
                401
            );
        }
    }

    private function exigirAdministrador()
    {
        $this->exigirSesion();

        if ($_SESSION["rol"] !== "Administrador") {
            $this->responder(
                ["error" => "No tienes permisos para esta operación."],
                403
            );
        }
    }

    private function responder($datos, $estado = 200)
    {
        http_response_code($estado);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
?>
