<?php
header('Content-Type: application/json');
session_start();
require_once "conexion.php";

// 🛡️ Filtro de seguridad: Solo administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode([]);
    exit;
}

// 🔍 CONSULTA OPTIMIZADA: Quitamos el CASE estático y usamos un JOIN real con la tabla roles
$sql = "SELECT 
            s.id, 
            s.motivo, 
            s.rol_solicitado, 
            s.fecha_creacion, 
            u.username, 
            r.nombre AS rol_nombre  -- 🌟 Trae directamente el nombre de tu tabla de roles
        FROM solicitudes_rol s
        INNER JOIN usuarios u ON s.usuario_id = u.id
        INNER JOIN roles r ON s.rol_solicitado = r.id  -- 🌟 Vincula el ID solicitado con su rol real
        WHERE s.estado = 'pendiente'
        ORDER BY s.fecha_creacion ASC";

$resultado = $conn->query($sql);
$solicitudes = [];

if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $solicitudes[] = $row;
    }
}

echo json_encode($solicitudes);
$conn->close();
?>
