<?php
// Define el formato de respuesta y permite solicitudes de la app móvil.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Abre la conexión con la base suplementormovil.
include 'conexion.php';

// La aplicación envía nombre, correo y contraseña en el cuerpo JSON.
$data = json_decode(file_get_contents("php://input"), true);

// Rechaza solicitudes con JSON ausente o mal formado.
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Envía los datos del registro en formato JSON."]);
    exit;
}

$nombre = trim($data["nombre"] ?? "");
$email = trim($data["email"] ?? "");
$plainPassword = $data["password"] ?? "";

// Comprueba los campos y sus límites antes de tocar la base de datos.
if ($nombre === "" || strlen($nombre) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($email) > 100 || !is_string($plainPassword) || $plainPassword === "") {
    http_response_code(422);
    echo json_encode(["success" => false, "message" => "Verifica el nombre, correo y contraseña ingresados."]);
    $conn->close();
    exit;
}

// Nunca se guarda la contraseña original: se almacena únicamente su hash.
$password = password_hash($plainPassword, PASSWORD_DEFAULT);
// La consulta preparada evita insertar los datos como parte de SQL ejecutable.
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");

// Informa del fallo de preparación sin exponer detalles internos al cliente.
if (!$stmt) {
    error_log("No fue posible preparar el registro móvil: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "No fue posible procesar el registro."]);
    $conn->close();
    exit;
}

$stmt->bind_param("sss", $nombre, $email, $password);

// Devuelve respuestas distintas para alta exitosa, correo duplicado y otros errores.
if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Usuario registrado correctamente"]);
} elseif ($stmt->errno === 1062) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "Ese correo ya está registrado."]);
} else {
    error_log("No fue posible guardar el registro móvil: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "No fue posible guardar el registro. Inténtalo de nuevo."]);
}

// Libera la consulta y la conexión al terminar el registro.
$stmt->close();
$conn->close();
?>