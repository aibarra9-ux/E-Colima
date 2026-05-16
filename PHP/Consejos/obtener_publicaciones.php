<?php
session_start();
include(__DIR__ . "/../Perfil/conexion.php");

// Obtener la categoría de la URL
$categoria_id = $_GET['categoria_id'] ?? 5;

$sql = "SELECT p.id, p.titulo, p.contenido, p.imagen, p.fecha_creacion, u.username AS autor
        FROM publicaciones p
        JOIN usuarios u ON p.autor_id = u.id
        WHERE p.categoria_id = $categoria_id
        ORDER BY p.fecha_creacion DESC";

$result = $conn->query($sql);

$publicaciones = [];

while ($row = $result->fetch_assoc()) {
    $publicaciones[] = [
        'titulo' => $row['titulo'],
        'descripcion' => substr($row['contenido'], 0, 100) . '...',
        'imagen' => !empty($row['imagen']) ? '../../assets/Publicaciones/' . $row['imagen'] : 'https://picsum.photos/600/900?' . $row['id'],
        'autor' => $row['autor'],
        'fecha' => $row['fecha_creacion']
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($publicaciones);
?>