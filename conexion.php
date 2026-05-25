<?php
//if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    //http_response_code(403); // Prohibido
    //echo json_encode(["error" => "No tienes permiso para ver esta información"]);
    //exit();
//}

// Credenciales de acceso
$host = "localhost";
$user = "root";  // Usuario por defecto en XAMPP
$pass = "";      // Contraseña por defecto en XAMPP (vacía)
$db   = "ecolima"; // CAMBIA ESTO por el nombre real de tu BD

// Crear la conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar si hay errores
if ($conn->connect_error) {
    // Si hay error, devolvemos un JSON para que el JS sepa qué pasó
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

// Establecer el conjunto de caracteres a UTF-8 para evitar problemas con acentos
$conn->set_charset("utf8");
