<?php
session_start();
require_once "conexion.php";

$id_usuario = null;

// 1. Capturamos el ID que mande el JS de forma alterna
if (isset($_POST['usuario_id_alterno']) && !empty($_POST['usuario_id_alterno'])) {
    $id_usuario = intval($_POST['usuario_id_alterno']);
} elseif (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
} elseif (isset($_SESSION['id'])) {
    $id_usuario = $_SESSION['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (empty($id_usuario) || $id_usuario === 0) {
        echo json_encode(["status" => "error", "message" => "ID de usuario vacío."]);
        exit;
    }

    // Recibimos el texto limpio de la biografía
    $biografia = isset($_POST['biografia']) ? trim($_POST['biografia']) : '';

    // Hacemos el UPDATE en tu tabla usuarios
    $stmt = $conn->prepare("UPDATE usuarios SET biografia = ? WHERE id = ?");
    $stmt->bind_param("si", $biografia, $id_usuario);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Biografía guardada correctamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar la base de datos."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Petición no válida."]);
}
exit();
?>