<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once "conexion.php";

// Obtenemos el ID del usuario en sesión (si no ha iniciado sesión, será 0)
$usuario_actual_id = $_SESSION['usuario_id'] ?? 0;

// Consulta SQL avanzada: Cuenta los likes totales de cada post 
// Y evalúa si el usuario en sesión ya le dio like (si da un número mayor a 0, dio_like será true)
$sql = "SELECT p.id, p.titulo, p.imagen, p.fecha_creacion, u.username as autor,
               COUNT(l.id) as total_likes,
               SUM(CASE WHEN l.usuario_id = ? THEN 1 ELSE 0 END) as dio_like
        FROM publicaciones p
        INNER JOIN usuarios u ON p.autor_id = u.id
        LEFT JOIN likes l ON p.id = l.publicacion_id
        WHERE p.estado = 'publicado' 
        AND p.fecha_eliminacion IS NULL
        GROUP BY p.id
        ORDER BY p.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_actual_id);
$stmt->execute();
$resultado = $stmt->get_result();

$publicaciones = []; 

if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        
        if (!empty($row['fecha_creacion'])) {
            $fechaFormateada = date("d M, Y", strtotime($row['fecha_creacion']));
        } else {
            $fechaFormateada = "Sin fecha";
        }
        
        $publicaciones[] = [
            'id' => $row['id'],
            'titulo' => $row['titulo'],
            'autor' => $row['autor'],
            'fecha' => $fechaFormateada,
            'imagen' => !empty($row['imagen']) ? $row['imagen'] : '',
            'likes' => (int)$row['total_likes'],
            'le_gusta' => $row['dio_like'] > 0 ? true : false // Envía un booleano al JS
        ];
    }
}

echo json_encode($publicaciones);
exit();
?>