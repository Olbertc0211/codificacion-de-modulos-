<?php

/**
 * Acceso a datos de usuarios.
 */
class UserModel
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function buscarPorCorreo($correo)
    {
        $stmt = $this->conexion->prepare(
            "SELECT id, nombre, correo, password, rol FROM usuarios WHERE correo = ?"
        );
        $stmt->bind_param("s", $correo);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function listar()
    {
        $resultado = $this->conexion->query(
            "SELECT id, nombre, correo, rol, creado_en FROM usuarios ORDER BY id DESC"
        );

        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($nombre, $correo, $passwordHash, $rol)
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nombre, $correo, $passwordHash, $rol);

        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $error) {
            if ($error->getCode() === 1062) {
                return false;
            }

            throw $error;
        }

        return $stmt->insert_id;
    }
}
?>
