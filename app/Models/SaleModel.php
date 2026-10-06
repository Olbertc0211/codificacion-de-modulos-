<?php

/**
 * Acceso a datos y transacciones de ventas.
 */
class SaleModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $sql = "SELECT v.id, v.total, v.creado_en, u.nombre AS cliente
                FROM ventas v
                INNER JOIN usuarios u ON u.id = v.usuario_id
                ORDER BY v.id DESC";
        $resultado = $this->conexion->query($sql);

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Registra la venta y descuenta el stock como una sola operación.
     */
    public function crear($usuarioId, $productos)
    {
        $cantidadesPorProducto = [];
        foreach ($productos as $producto) {
            $productoId = $producto["producto_id"];
            $cantidadesPorProducto[$productoId] =
                ($cantidadesPorProducto[$productoId] ?? 0) + $producto["cantidad"];
        }

        $this->conexion->begin_transaction();
        $confirmada = false;

        try {
            $detalles = [];
            $totalEnCentavos = 0;

            foreach ($cantidadesPorProducto as $productoId => $cantidad) {
                $stmt = $this->conexion->prepare(
                    "SELECT precio, stock FROM productos WHERE id = ? FOR UPDATE"
                );
                $stmt->bind_param("i", $productoId);
                $stmt->execute();
                $producto = $stmt->get_result()->fetch_assoc();

                if (!$producto || (int) $producto["stock"] < $cantidad) {
                    return [
                        "error" => "Producto inexistente o stock insuficiente.",
                        "producto_id" => (int) $productoId
                    ];
                }

                $precio = number_format((float) $producto["precio"], 2, ".", "");
                $precioEnCentavos = (int) round((float) $precio * 100);
                $totalEnCentavos += $precioEnCentavos * $cantidad;
                $detalles[] = [
                    "producto_id" => (int) $productoId,
                    "cantidad" => $cantidad,
                    "precio" => $precio
                ];
            }

            $total = number_format($totalEnCentavos / 100, 2, ".", "");
            $stmt = $this->conexion->prepare(
                "INSERT INTO ventas (usuario_id, total) VALUES (?, ?)"
            );
            $stmt->bind_param("is", $usuarioId, $total);
            $stmt->execute();
            $ventaId = $stmt->insert_id;

            foreach ($detalles as $detalle) {
                $stmtDetalle = $this->conexion->prepare(
                    "INSERT INTO detalle_ventas
                     (venta_id, producto_id, cantidad, precio_unitario)
                     VALUES (?, ?, ?, ?)"
                );
                $stmtDetalle->bind_param(
                    "iiis",
                    $ventaId,
                    $detalle["producto_id"],
                    $detalle["cantidad"],
                    $detalle["precio"]
                );
                $stmtDetalle->execute();

                $stmtStock = $this->conexion->prepare(
                    "UPDATE productos SET stock = stock - ? WHERE id = ?"
                );
                $stmtStock->bind_param(
                    "ii",
                    $detalle["cantidad"],
                    $detalle["producto_id"]
                );
                $stmtStock->execute();
            }

            $this->conexion->commit();
            $confirmada = true;

            return [
                "venta_id" => $ventaId,
                "total" => (float) $total
            ];
        } finally {
            if (!$confirmada) {
                $this->conexion->rollback();
            }
        }
    }
}
?>
