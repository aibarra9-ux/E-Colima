<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../Perfil/conexion.php"; // Ajusta la ruta a tu archivo si es necesario

// Capturamos el texto a buscar desde la URL (parámetro 'q')
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$usuario_actual_id = $_SESSION['usuario_id'] ?? 0;

if (empty($busqueda)) {
    echo json_encode([]);
    exit();
}

// Preparamos el término para la consulta SQL agregando los comodines %
$termino = "%" . $busqueda . "%";

// SQL global que busca en título y contenido respetando el sistema de Likes y estados
$sql = "SELECT p.id, p.titulo, p.contenido, p.imagen, p.fecha_creacion, u.username as autor,
               COUNT(l.id) as total_likes,
               SUM(CASE WHEN l.usuario_id = ? THEN 1 ELSE 0 END) as dio_like
        FROM publicaciones p
        INNER JOIN usuarios u ON p.autor_id = u.id
        LEFT JOIN likes l ON p.id = l.publicacion_id
        WHERE (p.titulo LIKE ? OR p.contenido LIKE ?) 
          AND p.estado = 'publicado' 
          AND p.fecha_eliminacion IS NULL
        GROUP BY p.id
        ORDER BY p.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $usuario_actual_id, $termino, $termino);
$stmt->execute();
$resultado = $stmt->get_result();

$publicaciones = []; 

while($row = $resultado->fetch_assoc()) {
    $publicaciones[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'descripcion' => substr($row['contenido'], 0, 100) . '...',
        'imagen' => !empty($row['imagen']) ? '../../assets/Publicaciones/' . $row['imagen'] : 'https://picsum.photos/600/900?' . $row['id'],
        'autor' => $row['autor'],
        'fecha' => !empty($row['fecha_creacion']) ? date("d M, Y", strtotime($row['fecha_creacion'])) : "Sin fecha",
        'likes' => (int)$row['total_likes'],
        'le_gusta' => $row['dio_like'] > 0 ? true : false
    ];
}

echo json_encode($publicaciones);
exit();
?>
