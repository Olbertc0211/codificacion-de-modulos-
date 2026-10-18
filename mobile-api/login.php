<?php
// Define una respuesta JSON para la app Android.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Conecta con la misma base que utiliza el registro.
include 'conexion.php';

// Lee las credenciales que la aplicación envía como JSON.
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Datos inválidos"]);
    exit;
}

$email = is_array($data) ? trim($data["email"] ?? "") : "";
$password = is_array($data) ? ($data["password"] ?? "") : "";

// Valida el formato antes de consultar la tabla.
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_string($password) || $password === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Ingresa un correo y una contraseña válidos."]);
    $conn->close();
    exit;
}

// Busca solo los datos necesarios para comprobar la contraseña y responder.
// La consulta preparada mantiene el correo separado del código SQL.
$stmt = $conn->prepare("SELECT nombre, email, password FROM usuarios WHERE email = ?");
if (!$stmt) {
    error_log("No fue posible preparar el inicio de sesión móvil: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "No fue posible iniciar sesión."]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Compara la contraseña con el hash creado durante el registro.
if ($row && password_verify($password, $row["password"])) {
    // Devuelve el nombre para saludar al usuario en la pantalla de IMC.
    echo json_encode([
        "success" => true,
        "message" => "Login exitoso",
        "user" => $row["nombre"],
        "email" => $row["email"]
    ]);
} else {
    // No revela si falló el correo o la contraseña.
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "El correo o la contraseña no son correctos."]);
}

// Libera los recursos usados por la solicitud.
$stmt->close();
$conn->close();
?>