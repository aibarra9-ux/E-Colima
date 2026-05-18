<?php
// dar_like.php - Ubicado en PHP/Perfil/
header('Content-Type: application/json; charset=utf-8');
session_start();

// Como está en la misma carpeta que conexion.php, se llama directo
require_once "conexion.php";

// 1. Validar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["status" => "error", "message" => "No autorizado"]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$publicacion_id = $_GET['publicacion_id'] ?? 0;

if ($publicacion_id == 0) {
    echo json_encode(["status" => "error", "message" => "ID de publicación inválido"]);
    exit;
}

// 2. Comprobar si ya existe el Like en la tabla
$check_sql = "SELECT id FROM likes WHERE usuario_id = ? AND publicacion_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ii", $usuario_id, $publicacion_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 🌟 YA TIENE LIKE: Lo eliminamos (Toggle para quitar el me gusta)
    $delete_sql = "DELETE FROM likes WHERE usuario_id = ? AND publicacion_id = ?";
    $stmt_del = $conn->prepare($delete_sql);
    $stmt_del->bind_param("ii", $usuario_id, $publicacion_id);
    $stmt_del->execute();
    $accion = "unliked";
} else {
    // 🌟 NO TIENE LIKE: Lo registramos
    $insert_sql = "INSERT INTO likes (usuario_id, publicacion_id, fecha) VALUES (?, ?, NOW())";
    $stmt_ins = $conn->prepare($insert_sql);
    $stmt_ins->bind_param("ii", $usuario_id, $publicacion_id);
    $stmt_ins->execute();
    $accion = "liked";
}

// 3. Obtener el conteo actualizado de likes totales para esta publicación
$count_sql = "SELECT COUNT(*) as total FROM likes WHERE publicacion_id = ?";
$stmt_count = $conn->prepare($count_sql);
$stmt_count->bind_param("i", $publicacion_id);
$stmt_count->execute();
$res_count = $stmt_count->get_result()->fetch_assoc();

// 4. Devolvemos la respuesta al JavaScript
echo json_encode([
    "status" => "success",
    "accion" => $accion,
    "total_likes" => (int)$res_count['total']
]);
exit();
?>