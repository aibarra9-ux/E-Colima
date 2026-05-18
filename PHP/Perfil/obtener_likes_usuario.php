<?php
// obtener_likes_usuario.php - Guardar en PHP/Perfil/
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "conexion.php";

// 🛡️ Filtro de seguridad: El usuario debe estar logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// 🔍 Consulta SQL: Trae los datos de los posts que recibieron LIKE por este usuario
$sql = "SELECT p.id, p.titulo, p.contenido, p.imagen, p.fecha_creacion, u.username as autor
        FROM likes l
        INNER JOIN publicaciones p ON l.publicacion_id = p.id
        INNER JOIN usuarios u ON p.autor_id = u.id
        WHERE l.usuario_id = ? AND p.fecha_eliminacion IS NULL
        ORDER BY l.fecha DESC"; // Ordenados por el me gusta más reciente

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$publicacionesLikeadas = [];

while($row = $resultado->fetch_assoc()) {
    $publicacionesLikeadas[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'descripcion' => mb_strimwidth(strip_tags($row['contenido']), 0, 110, "..."),
        'imagen' => !empty($row['imagen']) ? '../../assets/Publicaciones/' . $row['imagen'] : '../../assets/Publicaciones/default_post.jpg',
        'autor' => $row['autor'],
        'fecha' => !empty($row['fecha_creacion']) ? date("d M, Y", strtotime($row['fecha_creacion'])) : "Sin fecha"
    ];
}

echo json_encode($publicacionesLikeadas);
exit();
?>