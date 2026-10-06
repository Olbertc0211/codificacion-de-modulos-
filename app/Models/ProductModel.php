<?php

/**
 * Acceso a datos del catálogo e inventario.
 */
class ProductModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar()
    {
        $resultado = $this->conexion->query(
            "SELECT id, nombre, descripcion, precio, stock, creado_en
             FROM productos ORDER BY id DESC"
        );

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($nombre, $descripcion, $precio, $stock)
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function actualizar($id, $nombre, $descripcion, $precio, $stock)
    {
        $stmt = $this->conexion->prepare(
            "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?"
        );
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $stock, $id);

        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function actualizarStock($id, $stock)
    {
        $stmt = $this->conexion->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $stock, $id);

        return $stmt->execute();
    }
}
?>
