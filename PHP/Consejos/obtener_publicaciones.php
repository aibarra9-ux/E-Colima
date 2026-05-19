<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../Perfil/conexion.php"; // Ajusta la ruta a tu conexión si es necesario

// 1. Capturamos los parámetros de la URL de forma segura
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : 0;
$subcategoria_id = isset($_GET['subcategoria_id']) ? intval($_GET['subcategoria_id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // Captura el texto del buscador
$usuario_actual_id = $_SESSION['usuario_id'] ?? 0;

// Si no se pasa ni categoría ni subcategoría, usamos por defecto la 5 (Consejos)
if ($categoria_id === 0 && $subcategoria_id === 0) {
    $categoria_id = 5;
}

// 2. Base de la consulta SQL (🌟 MODIFICADO: Agregamos p.tipo_media)
$sql = "SELECT p.id, p.titulo, p.contenido, p.imagen, p.tipo_media, p.fecha_creacion, u.username as autor,
               COUNT(l.id) as total_likes,
               SUM(CASE WHEN l.usuario_id = ? THEN 1 ELSE 0 END) as dio_like
        FROM publicaciones p
        INNER JOIN usuarios u ON p.autor_id = u.id
        LEFT JOIN likes l ON p.id = l.publicacion_id
        WHERE p.estado = 'publicado' AND p.fecha_eliminacion IS NULL";

// 3. Aplicamos el filtro dinámico según la subcategoría o categoría padre
if ($subcategoria_id > 0) {
    $sql .= " AND p.subcategoria_id = ? ";
} else {
    $sql .= " AND p.categoria_id = ? ";
}

// Si el usuario escribió algo en el buscador, añadimos el filtro por texto
if (!empty($search)) {
    $sql .= " AND (p.titulo LIKE ? OR p.contenido LIKE ?) ";
}

$sql .= " GROUP BY p.id ORDER BY p.fecha_creacion DESC";

// 4. Preparamos y vinculamos los parámetros de forma dinámica y segura
$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $terminoLike = "%" . $search . "%";
    
    if ($subcategoria_id > 0) {
        $stmt->bind_param("iiss", $usuario_actual_id, $subcategoria_id, $terminoLike, $terminoLike);
    } else {
        $stmt->bind_param("iiss", $usuario_actual_id, $categoria_id, $terminoLike, $terminoLike);
    }
} else {
    if ($subcategoria_id > 0) {
        $stmt->bind_param("ii", $usuario_actual_id, $subcategoria_id);
    } else {
        $stmt->bind_param("ii", $usuario_actual_id, $categoria_id);
    }
}

$stmt->execute();
$resultado = $stmt->get_result();

$publicaciones = []; 

// 5. Procesamos y formateamos los datos incluyendo la discriminación multimedia
while($row = $resultado->fetch_assoc()) {
    
    // Determinamos la ruta del archivo subido o el fallback (Lorem Picsum)
    $ruta_media = !empty($row['imagen']) ? '../../assets/Publicaciones/' . $row['imagen'] : 'https://picsum.photos/600/900?' . $row['id'];
    
    $publicaciones[] = [
        'id' => $row['id'],
        'titulo' => $row['titulo'],
        'descripcion' => substr($row['contenido'], 0, 100) . '...',
        'imagen' => $ruta_media, // Mantenemos la clave por compatibilidad estructural
        'tipo_media' => $row['tipo_media'] ?? 'imagen', // 🌟 NUEVO: Enviamos si es 'imagen' o 'video' al frontend
        'autor' => $row['autor'],
        'fecha' => !empty($row['fecha_creacion']) ? date("d M, Y", strtotime($row['fecha_creacion'])) : "Sin fecha",
        'likes' => (int)$row['total_likes'],
        'le_gusta' => $row['dio_like'] > 0 ? true : false
    ];
}

echo json_encode($publicaciones);
exit();
?>