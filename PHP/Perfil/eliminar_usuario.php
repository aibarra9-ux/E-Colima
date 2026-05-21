<?php
session_start();

// 1. Verificación básica de sesión de Administrador
if (!isset($_SESSION['rol_id']) || $_SESSION['rol_id'] != 1) {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "No tienes permiso para realizar esta acción"]);
    exit();
}

require_once "conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_a_borrar = isset($_POST['id']) ? intval($_POST['id']) : null;
    $mi_id = $_SESSION['usuario_id']; // ID del admin que está ejecutando la acción

    if ($id_a_borrar) {
        
        // --- CANDADO 1: No borrarse a sí mismo ---
        if ($id_a_borrar === $mi_id) {
            echo json_encode(["success" => false, "error" => "No puedes eliminar tu propia cuenta de administrador."]);
            exit();
        }

        // --- CANDADO 2: No borrar a otros Administradores ---
        // Consultamos el rol del usuario que se pretende borrar
        $check_sql = "SELECT rol_id FROM usuarios WHERE id = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("i", $id_a_borrar);
        $stmt_check->execute();
        $res = $stmt_check->get_result()->fetch_assoc();

        if ($res && $res['rol_id'] == 1) {
            echo json_encode(["success" => false, "error" => "Seguridad: No se permite eliminar a otros administradores."]);
            exit();
        }

        // --- PROCESO DE ELIMINACIÓN ---
        // Si pasó los candados, procedemos con el DELETE
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_a_borrar);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Error al ejecutar la eliminación"]);
        }

        $stmt->close();
        $stmt_check->close();
    } else {
        echo json_encode(["success" => false, "error" => "ID de usuario no recibido"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}

$conn->close();
?>