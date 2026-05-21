<?php
// Evitar que cualquier error de PHP se imprima como texto y rompa el JSON
error_reporting(0); 
header('Content-Type: application/json');

session_start();

// 1. Verificación de permisos
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode(["success" => false, "error" => "No autorizado"]);
    exit();
}

require_once "conexion.php";

$response = ["success" => false, "error" => "Datos incompletos"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // IMPORTANTE: Asegúrate de que los nombres coincidan con el FormData del JS
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $rol = isset($_POST['rol']) ? intval($_POST['rol']) : (isset($_POST['rol_id']) ? intval($_POST['rol_id']) : null);

    if ($id && $rol) {
        $sql = "UPDATE usuarios SET rol_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $rol, $id);
            if ($stmt->execute()) {
                $response = ["success" => true];
            } else {
                $response = ["success" => false, "error" => $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ["success" => false, "error" => $conn->error];
        }
    }
}

// 2. Siempre cerramos la conexión y enviamos el JSON
$conn->close();
echo json_encode($response);
exit();
?>