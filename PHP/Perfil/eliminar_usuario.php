
<?php
session_start();
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(403); // Prohibido
    echo json_encode(["error" => "No tienes permiso para ver esta información"]);
    exit();
}
// 1. Requerimos la conexión centralizada
require_once "conexion.php";

// 2. Solo procesamos si la petición es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtenemos el ID enviado desde el JS
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if ($id) {
        // 3. Preparamos la sentencia DELETE
        // Es vital usar "?" para proteger la base de datos
        $sql = "DELETE FROM usuarios WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        
        // "i" indica que el parámetro es un Integer (entero)
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Si se eliminó correctamente, enviamos éxito al JS
            echo json_encode(["success" => true]);
        } else {
            // Si hubo un error en la base de datos
            echo json_encode(["success" => false, "error" => $conn->error]);
        }

        $stmt->close();
    } else {
        // Si no llegó el ID
        echo json_encode(["success" => false, "error" => "ID de usuario no recibido"]);
    }
} else {
    // Si alguien intenta entrar al archivo por URL (GET) directamente
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}

// 4. Cerramos la conexión
$conn->close();
?>