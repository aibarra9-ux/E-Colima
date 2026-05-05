
<?php
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(403); // Prohibido
    echo json_encode(["error" => "No tienes permiso para ver esta información"]);
    exit();
}
require_once "conexion.php";
header('Content-Type: application/json');

// Añadimos fecha_registro a la consulta
$sql = "SELECT id, username, email, fecha_registro, foto_perfil, rol_id FROM usuarios"; 
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
$conn->close();
?>