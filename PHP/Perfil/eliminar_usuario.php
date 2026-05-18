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
    $mi_id = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0; // ID del admin que ejecuta la acción

    if ($id_a_borrar) {
        
        // --- CANDADO 1: No borrarse a sí mismo ---
        if ($id_a_borrar === $mi_id) {
            echo json_encode(["success" => false, "error" => "No puedes eliminar tu propia cuenta de administrador."]);
            exit();
        }

        // --- CANDADO 2: No borrar a otros Administradores ---
        $check_sql = "SELECT rol_id FROM usuarios WHERE id = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("i", $id_a_borrar);
        $stmt_check->execute();
        $res = $stmt_check->get_result()->fetch_assoc();
        $stmt_check->close(); // Lo cerramos aquí para liberar espacio

        if ($res && $res['rol_id'] == 1) {
            echo json_encode(["success" => false, "error" => "Seguridad: No se permite eliminar a otros administradores."]);
            exit();
        }

        // --- PROCESO DE ELIMINACIÓN SEGURO (CON TRANSACCIÓN) ---
        // Iniciamos la transacción para limpiar tablas secundarias antes de borrar al usuario
        $conn->begin_transaction();

        try {
            // 🌟 PASO 1: Eliminar primero las solicitudes de rol que pertenezcan a este usuario
            $sql_solicitudes = "DELETE FROM solicitudes_rol WHERE usuario_id = ?";
            $stmt_sol = $conn->prepare($sql_solicitudes);
            $stmt_sol->bind_param("i", $id_a_borrar);
            $stmt_sol->execute();
            $stmt_sol->close();

            // 🌟 Si en el futuro tienes tablas de 'likes', 'comentarios' o 'publicaciones', 
            // agregarías sus respectivos DELETE aquí arriba usando el $id_a_borrar.

            // 🌟 PASO 2: Ahora sí, procedemos con el DELETE en la tabla usuarios libre de bloqueos
            $sql_user = "DELETE FROM usuarios WHERE id = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("i", $id_a_borrar);
            $stmt_user->execute();
            $stmt_user->close();

            // Si todo se ejecutó sin errores, confirmamos los cambios en la base de datos
            $conn->commit();
            echo json_encode(["success" => true]);

        } catch (Exception $e) {
            // Si algo falla, deshacemos todo para mantener la integridad de la base de datos
            $conn->rollback();
            echo json_encode(["success" => false, "error" => "Error en la base de datos: " . $e->getMessage()]);
        }

    } else {
        echo json_encode(["success" => false, "error" => "ID de usuario no recibido"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}

$conn->close();
?>