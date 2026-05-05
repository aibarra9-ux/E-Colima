

<?php
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(403); // Prohibido
    echo json_encode(["error" => "No tienes permiso para ver esta información"]);
    exit();
}
require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $rol = isset($_POST['rol']) ? $_POST['rol'] : null;

    if ($id && $rol) {
        // Cambiamos 'rol' por 'rol_id' que es como se llama en tu tabla
        $sql = "UPDATE usuarios SET rol_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Ahora ambos son enteros, usamos "ii" (integer, integer)
        $stmt->bind_param("ii", $rol, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        $stmt->close();
    }
}
$conn->close();
?>