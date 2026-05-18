<?php
// obtener_mis_publicaciones.php - Guardar en PHP/Perfil/
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "conexion.php";

// 🛡️ Filtro de seguridad
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// 🔍 Consulta SQL: Trae los posts del autor actual (incluyendo pendientes, publicados y rechazados)
$sql = "SELECT id, titulo, contenido, imagen, estado, motivo_rechazo, fecha_creacion 
        FROM publicaciones 
        WHERE autor_id = ? AND fecha_eliminacion IS NULL
        ORDER BY fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$misPublicaciones = [];

while($row = $resultado->fetch_assoc()) {
    $misPublicaciones[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'descripcion' => mb_strimwidth(strip_tags($row['contenido']), 0, 100, "..."),
        'imagen' => !empty($row['imagen']) ? '../../assets/Publicaciones/' . $row['imagen'] : '../../assets/Publicaciones/default_post.jpg',
        'estado' => $row['estado'], // 'pendiente', 'publicado', 'rechazado'
        'motivo_rechazo' => !empty($row['motivo_rechazo']) ? $row['motivo_rechazo'] : null,
        'fecha' => !empty($row['fecha_creacion']) ? date("d M, Y", strtotime($row['fecha_creacion'])) : "Sin fecha"
    ];
}

echo json_encode($misPublicaciones);
exit();
?>