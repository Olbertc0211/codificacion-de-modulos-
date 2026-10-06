<?php

/**
 * Crea la cuenta inicial solo desde el equipo local.
 * La contraseña se genera aleatoriamente y se muestra una sola vez.
 */
if (
    !in_array($_SERVER["REMOTE_ADDR"] ?? "", ["127.0.0.1", "::1"], true)
) {
    http_response_code(403);
    exit("Este instalador solo se puede ejecutar desde el equipo local.");
}

require_once __DIR__ . "/conexion.php";

$resultado = $conexion->query(
    "SELECT id FROM usuarios WHERE rol = 'Administrador' LIMIT 1"
);

if ($resultado->num_rows > 0) {
    http_response_code(409);
    exit("Ya existe una cuenta administradora. El instalador no hará cambios.");
}

$nombre = "Administrador";
$correo = "admin@localhost";
$password = rtrim(strtr(base64_encode(random_bytes(24)), "+/", "-_"), "=");
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$rol = "Administrador";

$stmt = $conexion->prepare(
    "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $nombre, $correo, $passwordHash, $rol);
$stmt->execute();
$stmt->close();
$conexion->close();

header("Content-Type: text/plain; charset=utf-8");
echo "Administrador creado. Guarda esta contraseña; no se volverá a mostrar.\n";
echo "Correo: " . $correo . "\n";
echo "Contraseña: " . $password . "\n";
echo "Elimina o deshabilita crear_usuario.php después de usarlo.\n";
?>
