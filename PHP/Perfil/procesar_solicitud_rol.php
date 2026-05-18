<?php
session_start();
// Ajusta la ruta de conexión si es necesario en base a tu estructura de carpetas
include "conexion.php"; 

header('Content-Type: application/json');

// 🛡️ Filtro de seguridad: Verificar si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["status" => "error", "message" => "Sesión expirada. Por favor, inicia sesión nuevamente."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recolectamos las variables enviadas por el formulario
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : 0;
    $rol_solicitado = isset($_POST['rol_solicitado']) ? intval($_POST['rol_solicitado']) : 0;
    $motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';
    $estado_inicial = 'pendiente'; // Se registra automáticamente en "pendiente" como en tu BD

    // 🔍 Validaciones básicas en el servidor
    if ($usuario_id === 0) {
        echo json_encode(["status" => "error", "message" => "ID de usuario inválido."]);
        exit();
    }

    if ($rol_solicitado === 0) {
        echo json_encode(["status" => "error", "message" => "Debes seleccionar un rango válido."]);
        exit();
    }

    if (empty($motivo)) {
        echo json_encode(["status" => "error", "message" => "Por favor, escribe un motivo detallado."]);
        exit();
    }

    // 🔒 Consulta preparada para insertar de manera segura (Previene Inyección SQL)
    // Dejamos que MySQL use NOW() para guardar la 'fecha_creacion' actual
    $query = "INSERT INTO solicitudes_rol (usuario_id, rol_solicitado, motivo, estado, fecha_creacion) 
              VALUES (?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $usuario_id, $rol_solicitado, $motivo, $estado_inicial);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Solicitud almacenada con éxito."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al guardar en la base de datos: " . $conn->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Método de petición no permitido."]);
}
exit();
?>