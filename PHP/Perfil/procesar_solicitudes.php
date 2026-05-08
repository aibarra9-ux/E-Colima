<?php
session_start();
require_once "conexion.php";

// 1. Seguridad: Solo admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

// 2. Obtener parámetros
$id_solicitud = isset($_GET['id']) ? intval($_GET['id']) : 0;
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

if ($id_solicitud > 0 && ($accion === 'aprobar' || $accion === 'rechazar')) {
    
    if ($accion === 'aprobar') {
        // Obtener el usuario_id y el rol_solicitado de esa solicitud
        $stmt = $conn->prepare("SELECT usuario_id, rol_solicitado FROM solicitudes_rol WHERE id = ?");
        $stmt->bind_param("i", $id_solicitud);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($row = $resultado->fetch_assoc()) {
            $user_id = $row['usuario_id'];
            $nuevo_rol = $row['rol_solicitado'];

            // TRANSACCIÓN: Actualizamos el usuario Y la solicitud
            $conn->begin_transaction();
            try {
                // Actualizar rol del usuario
                $upd_user = $conn->prepare("UPDATE usuarios SET rol_id = ? WHERE id = ?");
                $upd_user->bind_param("ii", $nuevo_rol, $user_id);
                $upd_user->execute();

                // Marcar solicitud como aprobada
                $upd_sol = $conn->prepare("UPDATE solicitudes_rol SET estado = 'aprobado', fecha_respuesta = NOW() WHERE id = ?");
                $upd_sol->bind_param("i", $id_solicitud);
                $upd_sol->execute();

                $conn->commit();
                echo json_encode(["status" => "success", "message" => "¡Solicitud aprobada! El usuario ahora tiene el nuevo rol."]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(["status" => "error", "message" => "Error al procesar el cambio."]);
            }
        }
    } else {
        // Si se rechaza, solo cambiamos el estado de la solicitud
        $stmt = $conn->prepare("UPDATE solicitudes_rol SET estado = 'rechazado', fecha_respuesta = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id_solicitud);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Solicitud rechazada y archivada."]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo rechazar la solicitud."]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Datos inválidos."]);
}