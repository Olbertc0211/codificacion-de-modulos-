<?php
// Todas las respuestas de esta API usan JSON en codificación UTF-8.
header("Content-Type: application/json; charset=UTF-8");
// Permite solicitudes desde la app Android y desde otros clientes locales.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Datos de conexión al MySQL que inicia XAMPP.
$host = "127.0.0.1";
$user = "root";
$pass = ""; // Sin contraseña en XAMPP por defecto
$db = "suplementormovil";

// Crea la conexión reutilizada por registro.php y login.php.
$conn = new mysqli($host, $user, $pass, $db);

// Informa el problema en JSON para que el cliente pueda mostrar un error claro.
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Conserva correctamente nombres y mensajes con caracteres en español.
$conn->set_charset("utf8mb4");
?>