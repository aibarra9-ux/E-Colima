<?php
header('Content-Type: application/json');
session_start();
require_once "conexion.php";

// Seguridad: Solo el admin entra aquí
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] != 1) {
    echo json_encode([]);
    exit;
}

// Consulta Pro: Traemos el nombre del usuario y filtramos solo las 'pendientes'
$sql = "SELECT s.id, s.motivo, s.rol_solicitado, u.username, 
               CASE 
                WHEN s.rol_solicitado = 1 THEN 'Administrador'
                WHEN s.rol_solicitado = 2 THEN 'Usuario Estándar'
                ELSE 'Otro' 
               END as rol_nombre
        FROM solicitudes_rol s
        INNER JOIN usuarios u ON s.usuario_id = u.id
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